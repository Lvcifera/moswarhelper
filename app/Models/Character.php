<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Character extends Model
{
    protected $table = 'characters';

    public function licence()
    {
        return $this->belongsTo(Licence::class, 'licence_id', 'id');
    }

}
