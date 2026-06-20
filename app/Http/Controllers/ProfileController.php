<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
        if(!file_exists($dir)){
            mkdir($dir,0755,true);
        }

        $file->move($dir, $name);

        $relativePath = 'uploads/pegawai/'.$name;

        // Update pegawai table
        DB::table('pegawai')->where('nama', $user->nama)->update(['photo' => $relativePath]);

        // Update session user foto to accessible asset
        $user->foto = asset($relativePath);
        session(['user' => $user]);

        return redirect()->back()->with('success','Foto profil berhasil diunggah.');
    }
}
