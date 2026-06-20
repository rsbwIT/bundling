<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user = session('user');
        if(!$user || empty($user->nama)){
            return redirect()->back()->with('error','User tidak ditemukan di sesi.');
        }

        $file = $request->file('photo');
        $ext = $file->getClientOriginalExtension();
        $name = 'pegawai_'.preg_replace('/[^A-Za-z0-9]/','_',substr($user->nama,0,30)).'_'.time().'.'.$ext;

        $dir = public_path('uploads/pegawai');
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        // save local copy first for immediate display
        $file->move($dir, $name);
        $relativePath = 'uploads/pegawai/' . $name;

        // Update pegawai table with local path so layout can use it immediately
        DB::table('pegawai')->where('nama', $user->nama)->update(['photo' => $relativePath]);

        // Update session user foto to accessible local asset (immediate UX)
        $user->foto = asset($relativePath);
        session(['user' => $user]);

        // Attempt to upload to remote Khanza SFTP (non-blocking for UX)
        $sftpSuccess = false;
        try {
            $remoteDir = 'webapps/penggajian/';
            $remotePath = $remoteDir . $name;
            // use configured SFTP disk; adjust disk name if needed
            Storage::disk('sftp_berkas')->put($remotePath, file_get_contents($dir . '/' . $name));
            $sftpSuccess = true;
        } catch (\Exception $e) {
            Log::error('Profile photo SFTP upload failed for ' . $name . ': ' . $e->getMessage());
            $sftpSuccess = false;
        }

        // If remote put succeeded, update DB to store remote filename and session foto to remote URL
        if ($sftpSuccess) {
            try {
                DB::table('pegawai')->where('nama', $user->nama)->update(['photo' => $name]);
                $remoteUrl = rtrim(env('URL_KHANZA', ''), '/') . '/webapps/penggajian/' . $name;
                $user->foto = $remoteUrl;
                session(['user' => $user]);
            } catch (\Exception $e) {
                Log::error('Failed to update DB/session after SFTP upload for ' . $name . ': ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'Foto profil berhasil diunggah.');
    }

    /**
     * Upload photo for a specific pegawai (by nik) - admin use
     */
    public function uploadPhotoForNik(Request $request, $nik)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $file = $request->file('photo');
        $ext = $file->getClientOriginalExtension();
        $name = 'pegawai_' . preg_replace('/[^A-Za-z0-9]/', '_', substr($nik, 0, 30)) . '_' . time() . '.' . $ext;

        $dir = public_path('uploads/pegawai');
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        // save local copy first
        $file->move($dir, $name);
        $relativePath = 'uploads/pegawai/' . $name;

        // Update pegawai.photo with local path initially
        DB::table('pegawai')->where('nik', $nik)->update(['photo' => $relativePath]);

        // Attempt SFTP put to remote Khanza
        $sftpSuccess = false;
        try {
            $remoteDir = 'webapps/penggajian/';
            $remotePath = $remoteDir . $name;
            Storage::disk('sftp_berkas')->put($remotePath, file_get_contents($dir . '/' . $name));
            $sftpSuccess = true;
        } catch (\Exception $e) {
            Log::error('Profile photo for nik ' . $nik . ' SFTP upload failed: ' . $e->getMessage());
            $sftpSuccess = false;
        }

        if ($sftpSuccess) {
            try {
                // store the remote filename in DB so views can resolve via URL_KHANZA
                DB::table('pegawai')->where('nik', $nik)->update(['photo' => $name]);
            } catch (\Exception $e) {
                Log::error('Failed to update pegawai.photo after SFTP for nik ' . $nik . ': ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'Foto pegawai berhasil diunggah.');
    }
}
