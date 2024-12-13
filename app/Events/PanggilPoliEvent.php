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

    public function __construct($nm_pasien, $kd_ruang_poli, $nm_poli, $no_reg, $kd_display)
    {
        $this->message = [
            'nm_pasien' => $nm_pasien,
            'kd_ruang_poli' => $kd_ruang_poli,
            'nm_poli' => $nm_poli,
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
