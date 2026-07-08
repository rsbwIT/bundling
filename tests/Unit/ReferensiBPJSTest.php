<?php

namespace Tests\Unit;

use App\Services\Bpjs\ReferensiBPJS;
use PHPUnit\Framework\TestCase;

class ReferensiBPJSTest extends TestCase
{
    public function test_update_ruangan_falls_back_to_create_when_bpjs_says_data_missing(): void
    {
        $service = new class extends ReferensiBPJS {
            public function setKodeRs(string $kodeRs): void
            {
                $this->kode_rs = $kodeRs;
            }

            public function setAplicare(object $aplicare): void
            {
                $this->aplicare = $aplicare;
            }
        };

        $service->setKodeRs('12345');

        $stub = new class {
            public array $calls = [];

            public function postRequest($endpoint, $data)
            {
                $this->calls[] = $endpoint;

                if ($endpoint === 'aplicaresws/rest/bed/update/12345') {
                    return '{"metadata":{"code":0,"message":"Data tidak ada di database."}}';
                }

                return '{"metadata":{"code":200,"message":"OK"}}';
            }
        };

        $service->setAplicare($stub);

        $response = $service->updateRuangan('{"kodekelas":"VVP"}');

        $this->assertStringContainsString('Ruangan belum ada di BPJS', $response);
        $this->assertStringContainsString('created', $response);
        $this->assertSame([
            'aplicaresws/rest/bed/update/12345',
            'aplicaresws/rest/bed/create/12345',
        ], $stub->calls);
    }
}
