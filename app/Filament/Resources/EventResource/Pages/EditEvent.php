<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Models\Event;
use Filament\Actions;
use App\Helpers\NotificationHelper;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\EventResource;
use Illuminate\Validation\ValidationException;

class EditEvent extends EditRecord
{
    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (
            Event::hasScheduleConflict(
                $data['room_id'],
                $data['date'],
                $data['start_time'],
                $data['finish_time'],
                $this->record->id
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

        return $data;
    }

    protected function afterSave(): void
    {
        $event = $this->record;

        $message = "Halo ! Terimakasih sudah mengajukan peminjaman ruangan. Form peminjaman ruangan anda telah diperbarui. Permintaan anda sedang kami proses kembali. Silakan cek status persetujuan ruangan di akun anda. Terimakasih.";

        NotificationHelper::sendWhatsApp('62' . ltrim($event->phone_number, '0'), $message);
    }

}
