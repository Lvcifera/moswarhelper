<?php

namespace App\Classes;

use Illuminate\Support\Facades\Http;

class SendRequest {
    public static function getRequest($playerData, $url)
    {
        return Http::withCookies(
            [
                'PHPSESSID' => $playerData->PHPSESSID,
                'authkey' => $playerData->authkey,
                'userid' => $playerData->userid,
                'player' => $playerData->player,
                'player_id' => $playerData->player_id,
            ], 'moswar.ru')->get($url);
    }

    public static function postRequest($playerData, $content, $contentType, $url)
    {
        return Http::withBody($content, $contentType)
            ->withCookies(
                [
                    'PHPSESSID' => $playerData->PHPSESSID,
                    'authkey' => $playerData->authkey,
                    'userid' => $playerData->userid,
                    'player' => $playerData->player,
                    'player_id' => $playerData->player_id,
                ], 'moswar.ru')->post($url);

    }
}
