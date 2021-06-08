<?php

namespace App\Classes;

use Illuminate\Support\Facades\Http;

class SendRequest {
    public static function getRequest($playerData, $url)
    {
        return Http::withHeaders(
            [
                'User-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.77 Safari/537.36'
            ])->withCookies(
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
            ->withHeaders(
                [
                    'User-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.77 Safari/537.36',
                    //'X-Requested-With' => 'XMLHttpRequest',
                ])
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
