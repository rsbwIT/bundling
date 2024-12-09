<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PanggilPoliEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct($no_rawat,  $kd_dokter, $kd_ruang_poli, $no_reg, $kd_display)
    {
        $this->message = [
            'no_rawat' => $no_rawat,
            'kd_dokter' => $kd_dokter,
            'kd_ruang_poli' => $kd_ruang_poli,
            'kd_display' => $kd_display,
            'no_reg' => $no_reg,
        ];
    }

    public function broadcastOn()
    {
        return new Channel('messages'.$this->message['kd_display']);
    }

    public function broadcastAs()
    {
        return 'message';
    }
}
