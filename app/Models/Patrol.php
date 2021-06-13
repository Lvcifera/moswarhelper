<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patrol extends Model
{
    use HasFactory;

    protected $table = 'patrols';
    protected $fillable = ['user_id', 'character_id', 'time', 'first_region', 'second_region', 'third_region'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function character()
    {
        return $this->hasOne(Character::class, 'id', 'character_id');
    }

    public function getFirstRegionAttribute($key)
    {
        $regions = [
            1 => 'Кремлевский',
            2 => 'Звериный',
            3 => 'Вокзальный',
            4 => 'Винно-заводский',
            5 => 'Монеточный',
            6 => 'Небоскреб-сити',
            7 => 'Промышленный',
            8 => 'Телевизионный',
            10 => 'Парковый',
            11 => 'Спальный',
            12 => 'Дворцовый',
            13 => 'Газовый',
            15 => 'Причальный',
            16 => 'Водоохранный',
            17 => 'Лосинск',
            18 => 'Внучатово',
            20 => 'Забугорный',
            21 => 'Отдохнуть в Тыве',
            22 => 'Ночной дозор',
        ];
        return $regions[$key];
    }

    public function getSecondRegionAttribute($key)
    {
        $regions = [
            1 => 'Кремлевский',
            2 => 'Звериный',
            3 => 'Вокзальный',
            4 => 'Винно-заводский',
            5 => 'Монеточный',
            6 => 'Небоскреб-сити',
            7 => 'Промышленный',
            8 => 'Телевизионный',
            10 => 'Парковый',
            11 => 'Спальный',
            12 => 'Дворцовый',
            13 => 'Газовый',
            15 => 'Причальный',
            16 => 'Водоохранный',
            17 => 'Лосинск',
            18 => 'Внучатово',
            20 => 'Забугорный',
            21 => 'Отдохнуть в Тыве',
            22 => 'Ночной дозор',
        ];
        return $regions[$key];
    }

    public function getThirdRegionAttribute($key)
    {
        $regions = [
            1 => 'Кремлевский',
            2 => 'Звериный',
            3 => 'Вокзальный',
            4 => 'Винно-заводский',
            5 => 'Монеточный',
            6 => 'Небоскреб-сити',
            7 => 'Промышленный',
            8 => 'Телевизионный',
            10 => 'Парковый',
            11 => 'Спальный',
            12 => 'Дворцовый',
            13 => 'Газовый',
            15 => 'Причальный',
            16 => 'Водоохранный',
            17 => 'Лосинск',
            18 => 'Внучатово',
            20 => 'Забугорный',
            21 => 'Отдохнуть в Тыве',
            22 => 'Ночной дозор',
        ];
        return $regions[$key];
    }
}
