<?php require 'vendor/autoload.php'; $app = require_once 'bootstrap/app.php'; $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap(); 
print_r(DB::select("SELECT * FROM reg_periksa WHERE tgl_registrasi = '2026-08-11' ORDER BY jam_reg DESC LIMIT 5"));
print_r(DB::select("SELECT * FROM referensi_mobilejkn_bpjs ORDER BY validasi DESC LIMIT 2"));
print_r(DB::select("SELECT * FROM referensi_mobilejkn_bpjs_batal ORDER BY tanggalbatal DESC LIMIT 2"));
