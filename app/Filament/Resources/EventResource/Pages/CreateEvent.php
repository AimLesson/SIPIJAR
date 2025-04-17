<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Models\Event;
use Filament\Actions;
use Filament\Notifications\Notification;
use App\Filament\Resources\EventResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;
use App\Helpers\NotificationHelper;

class CreateEvent extends CreateRecord
{
    protected static string $resource = EventResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (
            Event::hasScheduleConflict(
                $data['room_id'],
                $data['date'],
                $data['start_time'],
                $data['finish_time']
            )
        ) {
            Notification::make()
                ->title('Konflik Jadwal')
                ->body('Ruangan sudah terpakai pada waktu tersebut.')
                ->danger()
                ->send();

            throw ValidationException::withMessages([
                'room_id' => 'Jadwal bentrok dengan kegiatan lain di ruangan ini.',
            ]);
        }
        $data['user_id'] = auth()->id();

        return $data;
    }

    protected function afterCreate(): void
    {
        $event = $this->record;

        $message = "Halo ! Terima kasih sudah mengajukan peminjaman ruangan.
Permintaan anda sedang kami proses. Silakan cek status persetujuan ruangan di akun Anda. Terimakasih.";

        NotificationHelper::sendWhatsApp('62' . ltrim($event->phone_number, '0'), $message);
    }

}
