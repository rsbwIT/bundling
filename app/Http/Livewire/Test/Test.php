<?php

namespace App\Http\Livewire\Test;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Services\Bpjs\ReferensiBPJS;
use Illuminate\Support\Facades\Response;

class Test extends Component
{
    public $test;
    public function mount() {
        $this->test = '';
    }
    public function render()
    {
        return view('livewire.test.test');
    }

    function generatePdfToBase64() {
        $filepath = file_get_contents('storage/resume_dll/RESUMEDLL-20241119000175.pdf');
        $test= base64_encode($filepath);
        $this->test = $test;
    }
    function decodeBade64($test) {
        $decodedContent = base64_decode($test);
        $filepath = public_path('storage/resume_dll/file_encode.pdf');
        file_put_contents($filepath, $decodedContent);
        return Response::download($filepath, 'test.pdf')->deleteFileAfterSend(false);
    }
}
