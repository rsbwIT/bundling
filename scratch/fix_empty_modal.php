<?php
$file = 'c:\xampp\htdocs\bundling2\app\Http\Controllers\Bpjs\bridginginacbg2.php';
$content = file_get_contents($file);

// Fix Triase - add check before rendering view
$searchTriase = "\$html = view('bpjs.component._modal_triase', compact(";
$replaceTriase = "if (!\$triase) {\n            return response()->json(['error' => 'Data Triase belum dibuat untuk pasien ini.'], 404);\n        }\n        \n        \$html = view('bpjs.component._modal_triase', compact(";
$content = str_replace($searchTriase, $replaceTriase, $content);

// Fix Resume - add check before rendering view
$searchResume = "\$html = view('bpjs.component._modal_resume', compact(";
$replaceResume = "if (!\$resume) {\n            return response()->json(['error' => 'Resume Medis belum dibuat untuk pasien ini.'], 404);\n        }\n        \n        \$html = view('bpjs.component._modal_resume', compact(";
$content = str_replace($searchResume, $replaceResume, $content);

file_put_contents($file, $content);
echo "Fixed controller for empty resume/triase!\n";
?>
