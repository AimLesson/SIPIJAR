<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'id',
        'name',
        'room_id',
        'asal_bidang',
        'date',
        'start_time',
        'finish_time',
        'guest_count',
        'is_approved',
        'user_id', // ✅ tambahkan agar mass assignable
        'notes',
        'info',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function hasScheduleConflict($roomId, $date, $startTime, $finishTime, $excludeId = null): bool
    {
        return self::where('room_id', $roomId)
            ->where('date', $date)
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->where(function ($query) use ($startTime, $finishTime) {
                $query->where('start_time', '<', $finishTime)
                      ->where('finish_time', '>', $startTime);
            })
            ->exists();
    }
    
}
