<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kubovich extends Model
{
    use HasFactory;

    protected $table = 'kubovich';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function character()
    {
        return $this->hasOne(Character::class, 'id', 'character_id');
    }
}
