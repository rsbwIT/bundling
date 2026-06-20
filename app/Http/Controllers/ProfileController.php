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

        // Save filename (not full path) so views can resolve via URL_KHANZA when needed
        DB::table('pegawai')->where('nama', $user->nama)->update(['photo' => $name]);

        // Update session user foto to point to remote Khanza by default
        $user->foto = rtrim(env('URL_KHANZA', ''), '/') . '/webapps/penggajian/' . $name;
        session(['user' => $user]);

        // Attempt to upload the file to remote server via SSH/SFTP
        $sftpHost = env('SFTP_HOST');
        $sftpPort = env('SFTP_PORT', 22);
        $sftpUser = env('SFTP_USERNAME');
        $sftpPass = env('SFTP_PASSWORD');
        $remoteBase = env('SFTP_REMOTE_BASE', '/webapps/penggajian');

        $localFile = $dir . DIRECTORY_SEPARATOR . $name;
        $remotePath = rtrim($remoteBase, '/') . '/' . $name;

        $uploadRemoteOk = false;

        if(!empty($sftpHost) && function_exists('ssh2_connect')){
            try{
                $conn = @ssh2_connect($sftpHost, (int)$sftpPort);
                if($conn && @ssh2_auth_password($conn, $sftpUser, $sftpPass)){
                    $sftp = @ssh2_sftp($conn);
                    if($sftp){
                        $remoteDir = dirname($remotePath);
                        // try create remote dir (recursive)
                        @ssh2_sftp_mkdir($sftp, $remoteDir, 0755, true);

                        // open remote stream and write file contents
                        $remoteUrl = "ssh2.sftp://".intval($sftp).$remotePath;
                        $stream = @fopen($remoteUrl, 'w');
                        if($stream){
                            $data = @file_get_contents($localFile);
                            if($data !== false){
                                $bytes = @fwrite($stream, $data);
                                fclose($stream);
                                if($bytes !== false){
                                    $uploadRemoteOk = true;
                                }
                            }else{
                                fclose($stream);
                            }
                        }
                    }
                }
            }catch(\Throwable $ex){
                \Log::warning('ProfileController: remote upload error - '.$ex->getMessage());
            }
        }

        if(!$uploadRemoteOk){
            \Log::warning('ProfileController: remote upload failed for '.$name.' to '.$remotePath.' via '.$sftpHost);
            return redirect()->back()->with('success','Foto profil berhasil diunggah (lokal).')->with('info','Transfer ke server gambar eksternal gagal; avatar akan tampil setelah file tersedia di server remote.');
        }

        return redirect()->back()->with('success','Foto profil berhasil diunggah dan ditransfer ke server eksternal.');
    }
}
