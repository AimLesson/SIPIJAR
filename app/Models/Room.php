<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'id',
        'name',
        'desc',
        'capacity',
        'image',
        'status',
        'yt_video_link',
    ];

    public function events()
    {
        return $this->hasMany(Event::class, 'room_id', 'id');
    }
}
