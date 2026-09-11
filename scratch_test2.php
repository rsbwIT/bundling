<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$no_rawat = 'some_rawat'; // We just want to see the SQL query generated
$resumeData = [
    'kd_diagnosa_utama' => 'Z09.8',
    'diagnosa_utama' => 'Follow-up examination after other treatment for other conditions',
];

$query = Illuminate\Support\Facades\DB::table('resume_pasien')->where('no_rawat', $no_rawat)->toSql();
var_dump($query);

$updateQuery = Illuminate\Support\Facades\DB::table('resume_pasien')->where('no_rawat', $no_rawat)->update($resumeData);
var_dump($updateQuery);
