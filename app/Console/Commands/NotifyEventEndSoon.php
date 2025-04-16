<?php

namespace App\Console\Commands;

use App\Models\Event;
use Illuminate\Console\Command;
use App\Helpers\NotificationHelper;

class NotifyEventEndSoon extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:notify-event-end-soon';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = now();
        $soon = $now->addMinutes(15); // 15 menit sebelum habis
    
        $events = Event::where('is_approve', true)
            ->where('date', $now->toDateString())
            ->whereTime('finish_time', '>=', $now->toTimeString())
            ->whereTime('finish_time', '<=', $soon->toTimeString())
            ->get();
    
        foreach ($events as $event) {
            $message = "Halo ! Terimakasih sudah mengajukan peminjaman ruangan. Waktu peminjaman ruangan Anda hampir berakhir. Mohon pastikan semua keperluan anda telah selesai sebelum waktu habis. Terima kasih!";
    
            NotificationHelper::sendWhatsApp($event->user->phone ?? '08123456789', $message);
        }
    }
    
}
