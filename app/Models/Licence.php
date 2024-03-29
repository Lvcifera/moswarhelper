<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Licence extends Model
{
    use HasFactory;

    protected $table = 'licences';
    protected $fillable = ['user_id', 'player', 'start', 'end'];

    public function characters()
    {
        return $this->hasMany(Character::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
