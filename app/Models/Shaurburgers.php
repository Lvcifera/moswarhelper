<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shaurburgers extends Model
{
    use HasFactory;

    protected $table = 'shaurburgers';
    protected $fillable = ['user_id', 'character_id', 'time'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function character()
    {
        return $this->hasOne(Character::class, 'id', 'character_id');
    }

    public function getTimeAttribute($key)
    {
        $times = [
            1 => '1 час',
            2 => '2 часа',
            3 => '3 часа',
            4 => '4 часа',
            5 => '5 часов',
            6 => '6 часов',
            7 => '7 часов',
            8 => '8 часов',
        ];
        return $times[$key];
    }
}
