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
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'photo' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);
        if ($validator->fails()) {
            Log::error('Profile upload validation failed: ' . json_encode($validator->errors()));
            return redirect()->back()->with('error', 'Validasi gagal: ' . $validator->errors()->first());
        }

        $user = session('user');
        if(!$user || empty($user->nama)){
            return redirect()->back()->with('error','User tidak ditemukan di sesi.');
        }

        $file = $request->file('photo');
        $ext = $file->getClientOriginalExtension();
        $name = 'pegawai_'.preg_replace('/[^A-Za-z0-9]/','_',substr($user->nama,0,30)).'_'.time().'.'.$ext;

        try {
            Storage::disk('sftp_photo')->put($name, file_get_contents($file->getRealPath()));
        } catch (\Exception $e) {
            Log::error('Profile photo SFTP upload failed for ' . $name . ': ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengunggah ke server utama.');
        }

        try {
            $remotePath = 'pages/pegawai/photo/' . $name;
            DB::table('pegawai')->where('nama', $user->nama)->update(['photo' => $remotePath]);
            $remoteUrl = rtrim(env('URL_KHANZA', ''), '/') . '/webapps/penggajian/' . $remotePath;
            $user->foto = $remoteUrl;
            session(['user' => $user]);
        } catch (\Exception $e) {
            Log::error('Failed to update DB/session after SFTP upload for ' . $name . ': ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui database.');
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

        try {
            Storage::disk('sftp_photo')->put($name, file_get_contents($file->getRealPath()));
        } catch (\Exception $e) {
            Log::error('Profile photo for nik ' . $nik . ' SFTP upload failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengunggah ke server utama.');
        }

        try {
            $remotePath = 'pages/pegawai/photo/' . $name;
            DB::table('pegawai')->where('nik', $nik)->update(['photo' => $remotePath]);
        } catch (\Exception $e) {
            Log::error('Failed to update pegawai.photo after SFTP for nik ' . $nik . ': ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui database.');
        }

        return redirect()->back()->with('success', 'Foto pegawai berhasil diunggah.');
    }
}
