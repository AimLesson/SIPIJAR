<?php

namespace App\Filament\Widgets;

use Livewire\Livewire;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;
use App\Models\Event as EventModel;
use Guava\Calendar\ValueObjects\Event;
use Filament\Notifications\Notification;
use Guava\Calendar\Widgets\CalendarWidget;
use Guava\Calendar\ValueObjects\CalendarEvent;

class Calendar extends CalendarWidget
{
    protected string $calendarView = 'dayGridMonth';
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public function getEvents(array $fetchInfo = []): Collection|array
    {
        return EventModel::whereNotNull('date')
            ->get()
            ->map(function ($event) {
                // Combine date and time into a single datetime string
                $startDateTime = "{$event->date} {$event->start_time}";
                $endDateTime = "{$event->date} {$event->finish_time}";

                return CalendarEvent::make()
                    ->title($event->name)
                    ->start($startDateTime)
                    ->end($endDateTime)
                    ->styles([
                        'backgroundColor' => $event->is_approve ? '#81C784' : '#FFD54F',
                        'color' => 'black',
                    ]);
            });
    }
}
