<?php

namespace App\Http\Controllers\Regperiksa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use App\Models\LogActivity;
use Carbon\Carbon;
use App\Models\BwTrackerLogReg;

class RegPeriksaBillingController extends Controller
{
    public function __construct()
    {
        // Ensure session data is loaded
        if (session()->has('auth')) {
            $userId = session('auth')['id_user'];
            Log::info('Session auth found in constructor', ['user_id' => $userId]);

            // Cache user data
            $cacheKey = 'user_' . $userId;
            if (!Cache::has($cacheKey)) {
                $userLogin = DB::table('pegawai')
                    ->select('pegawai.nama')
                    ->where('pegawai.nik', '=', $userId)
                    ->first();

                if ($userLogin) {
                    Cache::put($cacheKey, $userLogin, 720); // Cache for 12 hours
                    Log::info('User data cached', ['user' => $userLogin]);
                }
            }
        } else {
            Log::warning('No auth session in constructor');
        }
    }

    /**
     * Menampilkan daftar reg_periksa berdasarkan no_rkm_medis (jika diisi).
     */
    public function regperiksabilling(Request $request)
    {
        $data = collect(); // Default kosong

        // Get user data from auth session
        $userId = session('auth')['id_user'] ?? null;
        Log::info('Session check in regperiksabilling', [
            'has_session' => session()->has('auth'),
            'user_id' => $userId
        ]);

        $userLogin = null;

        if ($userId) {
            $userLogin = DB::table('pegawai')
                ->select('pegawai.nik', 'pegawai.nama')
                ->where('pegawai.nik', '=', $userId)
                ->first();
            Log::info('Found user data', ['user' => $userLogin]);
        }

        // Fetch logs for the activity table
        $logs = DB::table('bw_tracker_log_reg2')
            ->select(
                'bw_tracker_log_reg2.tanggal',
                'bw_tracker_log_reg2.id_user',
                'bw_tracker_log_reg2.nama_user',
                'bw_tracker_log_reg2.status',
                'bw_tracker_log_reg2.keterangan'
            )
            ->orderBy('bw_tracker_log_reg2.tanggal', 'desc')
            ->limit(100)
            ->get();

        if ($request->no_rkm_medis) {
            $data = DB::table('reg_periksa')
                ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
                ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
                ->select(
                    'reg_periksa.no_rawat',
                    'reg_periksa.no_rkm_medis',
                    'pasien.nm_pasien',
                    'reg_periksa.tgl_registrasi',
                    'reg_periksa.status_lanjut',
                    'reg_periksa.stts',
                    'reg_periksa.status_bayar',
                    'dokter.nm_dokter',
                    'poliklinik.nm_poli'
                )
                ->where('reg_periksa.no_rkm_medis', $request->no_rkm_medis)
                ->orderByDesc('reg_periksa.tgl_registrasi')
                ->get();

            Log::info('Search results', ['count' => $data->count()]);

            // Log pencarian data
            if ($userId) {
                try {
                    $this->logActivity(
                        'SEARCH',
                        "Pencarian data registrasi pasien dengan No.RM: {$request->no_rkm_medis}, ditemukan {$data->count()} data"
                    );
                    Log::info('Search activity logged successfully');
                } catch (\Exception $e) {
                    Log::error('Failed to log search activity', ['error' => $e->getMessage()]);
                }
            }
        }

        return view('regperiksa.regperiksabilling', [
            'results' => $data,
            'no_rkm_medis' => $request->no_rkm_medis,
            'logs' => $logs,
            'user_id' => $userId,
            'user_data' => $userLogin
        ]);
    }

    /**
     * Mengupdate status pasien berdasarkan no_rawat.
     */
    public function updateStatus(Request $request)
    {
        try {
            $request->validate([
                'no_rawat' => 'required|string',
                'stts' => 'required|string|in:belum,batal'
            ]);

            // Get old data
            $oldData = DB::table('reg_periksa')
                ->where('no_rawat', $request->no_rawat)
                ->first();

            if (!$oldData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data registrasi tidak ditemukan'
                ], 404);
            }

            // Update status using direct query with proper error handling
            try {
                $updated = DB::table('reg_periksa')
                    ->where('no_rawat', $request->no_rawat)
                    ->update(['stts' => strtolower($request->stts)]);

                if ($updated) {
                    // Only log if the status actually changed
                    if (strtolower($oldData->stts) !== strtolower($request->stts)) {
                        // Log the update
                        $logSuccess = $this->logUpdateStatus(
                            'UPDATE_STATUS',
                            "Mengubah status no rawat {$request->no_rawat} dari {$oldData->stts} menjadi {$request->stts}"
                        );

                        if (!$logSuccess) {
                            Log::warning('Status updated but failed to create log', [
                                'no_rawat' => $request->no_rawat,
                                'old_status' => $oldData->stts,
                                'new_status' => $request->stts
                            ]);
                        }
                    }

                    return response()->json([
                        'success' => true,
                        'message' => "Status berhasil diubah dari {$oldData->stts} menjadi {$request->stts}"
                    ]);
                }

                throw new \Exception('Gagal mengupdate status di database');

            } catch (\Exception $e) {
                Log::error('Database error while updating status', [
                    'error' => $e->getMessage(),
                    'no_rawat' => $request->no_rawat,
                    'old_status' => $oldData->stts,
                    'new_status' => $request->stts
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengupdate status di database: ' . $e->getMessage()
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Error updating status', [
                'error' => $e->getMessage(),
                'no_rawat' => $request->no_rawat ?? 'not_set',
                'stts' => $request->stts ?? 'not_set'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengubah status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengupdate status untuk semua pasien dengan status 'belum'.
     */
    public function updateBulkStatus(Request $request)
    {
        try {
            $request->validate([
                'stts' => 'required|string|in:belum,batal',
                'no_rawat' => 'required|array',
                'no_rawat.*' => 'required|string'
            ]);

            // Get all selected records with their current status
            $records = DB::table('reg_periksa')
                ->whereIn('no_rawat', $request->no_rawat)
                ->get(['no_rawat', 'stts']);

            if ($records->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data registrasi tidak ditemukan'
                ], 404);
            }

            // Store old statuses for logging
            $oldStatuses = $records->pluck('stts', 'no_rawat')->toArray();

            try {
                DB::beginTransaction();

                // Update status using query builder
                $updated = DB::table('reg_periksa')
                    ->whereIn('no_rawat', $request->no_rawat)
                    ->update(['stts' => strtolower($request->stts)]);

                if ($updated) {
                    // Log the bulk update with detailed information
                    $logMessage = "";
                    foreach ($oldStatuses as $no_rawat => $oldStatus) {
                        $logMessage .= "Mengubah status no rawat {$no_rawat} dari {$oldStatus} menjadi {$request->stts}\n";
                    }

                    $logSuccess = $this->logUpdateStatus(
                        'BULK_UPDATE_STATUS',
                        rtrim($logMessage)
                    );

                    if (!$logSuccess) {
                        Log::warning('Bulk status updated but failed to create log', [
                            'no_rawat' => $request->no_rawat,
                            'old_statuses' => $oldStatuses,
                            'new_status' => $request->stts
                        ]);
                    }

                    DB::commit();

                    return response()->json([
                        'success' => true,
                        'message' => "Berhasil mengubah {$updated} status registrasi menjadi {$request->stts}",
                        'updated_count' => $updated
                    ]);
                }

                throw new \Exception('Gagal mengupdate status di database');

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Database error while updating bulk status', [
                    'error' => $e->getMessage(),
                    'no_rawat' => $request->no_rawat,
                    'old_statuses' => $oldStatuses,
                    'new_status' => $request->stts
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengupdate status di database: ' . $e->getMessage()
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Error updating bulk status', [
                'error' => $e->getMessage(),
                'no_rawat' => $request->no_rawat ?? [],
                'stts' => $request->stts ?? 'not_set'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengubah status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mencatat aktivitas umum ke log.
     */
    protected function logActivity($status, $keterangan)
    {
        try {
            Log::info('Attempting to create log', [
                'status' => $status,
                'keterangan' => $keterangan,
                'session' => session()->all()
            ]);

            return $this->insertLogTracker($status, $keterangan);
        } catch (\Exception $e) {
            Log::error('Error in logActivity', [
                'status' => $status,
                'keterangan' => $keterangan,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Mencatat aktivitas ke log dan mengembalikan status.
     */
    protected function logActivityWithStatus($status, $keterangan)
    {
        try {
            return $this->insertLogTracker($status, $keterangan);
        } catch (\Exception $e) {
            Log::error('Failed to log activity with status', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    protected function insertLogTracker($status, $keterangan)
    {
        try {
            // Get user data from session
            $userId = session('auth.id_user');
            $userName = null;

            // Only proceed if we have a real user ID
            if ($userId) {
                // Get user name from pegawai table
                $user = DB::table('pegawai')
                    ->select('nama')
                    ->where('nik', $userId)
                    ->first();

                if ($user) {
                    $userName = $user->nama;
                }

                // Only create log if we have both user ID and name
                if ($userName) {
                    $log = BwTrackerLogReg::create([
                        'id_user' => $userId,
                        'nama_user' => $userName,
                        'tanggal' => now(),
                        'status' => strtoupper($status),
                        'keterangan' => $keterangan
                    ]);

                    if ($log) {
                        Log::info('Log tracker inserted successfully', [
                            'id' => $log->id,
                            'id_user' => $userId,
                            'nama_user' => $userName,
                            'status' => $status
                        ]);
                        return true;
                    }
                }
            }

            Log::info('Skipping log creation - no valid user data');
            return true; // Return true to not trigger error handling

        } catch (\Exception $e) {
            Log::error('Failed to insert log tracker', [
                'error' => $e->getMessage(),
                'status' => $status,
                'keterangan' => $keterangan
            ]);
            return false;
        }
    }

    protected function logUpdateStatus($status, $keterangan)
    {
        try {
            return $this->insertLogTracker($status, $keterangan);
        } catch (\Exception $e) {
            Log::error('Error in logUpdateStatus', [
                'status' => $status,
                'keterangan' => $keterangan,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function getLogs(Request $request)
    {
        try {
            $query = DB::table('bw_tracker_log_reg2')
                ->select(
                    'tanggal',
                    'id_user',
                    'nama_user',
                    'status',
                    'keterangan'
                )
                ->orderBy('tanggal', 'desc');

            // Apply date filters if provided
            if ($request->has('start_date')) {
                $startDate = Carbon::parse($request->start_date)->startOfDay()->format('Y-m-d H:i:s');
                $query->where('tanggal', '>=', $startDate);
                Log::info('Start date filter:', ['date' => $startDate]);
            }

            if ($request->has('end_date')) {
                $endDate = Carbon::parse($request->end_date)->endOfDay()->format('Y-m-d H:i:s');
                $query->where('tanggal', '<=', $endDate);
                Log::info('End date filter:', ['date' => $endDate]);
            }

            $logs = $query->limit(1000)->get();

            // Convert dates to display format
            $logs = $logs->map(function($log) {
                $log->tanggal = Carbon::parse($log->tanggal)->format('d/m/Y H:i:s');
                return $log;
            });

            Log::info('Log query results:', [
                'count' => $logs->count(),
                'first_log' => $logs->first(),
                'sql' => $query->toSql(),
                'bindings' => $query->getBindings()
            ]);

            return response()->json([
                'success' => true,
                'data' => $logs
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getLogs:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data log: ' . $e->getMessage()
            ], 500);
        }
    }

    public function ajaxLogActivity(Request $request)
    {
        try {
            $request->validate([
                'status' => 'required|string',
                'keterangan' => 'required|string',
                'id_user' => 'required|string',
                'nama_user' => 'required|string'
            ]);

            // Create new log entry using create method
            $log = BwTrackerLogReg::create([
                'id_user' => $request->id_user,
                'nama_user' => $request->nama_user,
                'tanggal' => now(),
                'status' => strtoupper($request->status),
                'keterangan' => $request->keterangan
            ]);

            if (!$log) {
                throw new \Exception('Failed to insert log');
            }

            return response()->json([
                'success' => true,
                'message' => 'Log activity saved successfully',
                'log_id' => $log->id
            ]);
        } catch (\Exception $e) {
            Log::error('Error in ajaxLogActivity', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to save log activity'
            ], 500);
        }
    }
}
