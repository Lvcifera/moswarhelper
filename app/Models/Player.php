<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    protected $table = 'characters';

    public function licence()
    {
        return $this->hasOne(Licence::class);
    }
}
