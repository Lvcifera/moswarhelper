<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patriot extends Model
{
    use HasFactory;

    protected $table = 'patriot';
    protected $fillable = ['user_id', 'character_id', 'time', 'time_start'];

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
        ];
        return $times[$key];
    }

    public function getTimeStartAttribute($key)
    {
        return mb_strcut($key, 0, 5);
    }
}
