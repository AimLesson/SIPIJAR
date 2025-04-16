<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use App\Models\Room;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RoomStats extends BaseWidget
{
    protected function getStats(): array
    {
        $stats = [];

        $now = Carbon::now();
        $startOfMonth = $now->startOfMonth()->toDateString();
        $endOfMonth = $now->endOfMonth()->toDateString();

        // Get approved bookings this month
        $roomBookings = Event::query()
            ->select('room_id', DB::raw('count(*) as bookings'))
            ->where('is_approve', true)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->groupBy('room_id')
            ->get()
            ->keyBy('room_id');

        // Find most booked room
        $mostBooked = $roomBookings->sortByDesc('bookings')->first();
        if ($mostBooked) {
            $mostBookedRoom = Room::find($mostBooked->room_id);
            $stats[] = Stat::make('Most Booked Room', $mostBookedRoom->name ?? '-')
                ->description("{$mostBooked->bookings} approved bookings this month")
                ->descriptionIcon('heroicon-m-trophy')
                ->color('primary');
        } else {
            $stats[] = Stat::make('Most Booked Room', '-')
                ->description("No approved bookings this month")
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('gray');
        }

        // Add each room's approved booking count
        $rooms = Room::all();
        foreach ($rooms as $room) {
            $count = $roomBookings[$room->id]->bookings ?? 0;
            $stats[] = Stat::make($room->name, "{$count} bookings")
                ->description("Approved this month")
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color($count > 0 ? 'success' : 'gray');
        }

        return $stats;
    }
}
