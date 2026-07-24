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
            return redirect()->back()->with('error', 'Validasi gagal: ' . $validator->errors()->first());
        }

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

        // Create relative URL for session so it works across network
        $parsed = parse_url(asset($relativePath));
        $relativeUrl = ($parsed['path'] ?? '') . (isset($parsed['query']) ? '?'.$parsed['query'] : '');
        $user->foto = $relativeUrl;
        session(['user' => $user]);

        // Attempt to upload to remote Khanza SFTP (non-blocking for UX)
        try {
            Storage::disk('sftp_photo')->put($name, file_get_contents($dir . '/' . $name));
            $remotePath = 'pages/pegawai/photo/' . $name;
            DB::table('pegawai')->where('nama', $user->nama)->update(['photo' => $remotePath]);
        } catch (\Exception $e) {
            Log::error('Profile photo SFTP upload failed for ' . $name . ': ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Foto profil berhasil diunggah.');
    }

    /**
     * Upload photo for a specific pegawai (by nik) - admin use
     */
    public function uploadPhotoForNik(Request $request, $nik)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'photo' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with('error', 'Validasi gagal: ' . $validator->errors()->first());
        }

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
        try {
            Storage::disk('sftp_photo')->put($name, file_get_contents($dir . '/' . $name));
            $remotePath = 'pages/pegawai/photo/' . $name;
            DB::table('pegawai')->where('nik', $nik)->update(['photo' => $remotePath]);
        } catch (\Exception $e) {
            Log::error('Profile photo for nik ' . $nik . ' SFTP upload failed: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Foto pegawai berhasil diunggah.');
    }
}
