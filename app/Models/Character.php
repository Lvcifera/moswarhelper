<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Character extends Model
{
    protected $table = 'characters';
    protected $fillable = [
        'user_id',
        'licence_id',
        'PHPSESSID',
        'authkey',
        'userid',
        'player',
        'player_id',
        'param',
        'email',
        'password'];

    public function licence()
    {
        return $this->belongsTo(Licence::class, 'licence_id', 'id');
    }

}
