<?php

namespace App\Classes;

use Illuminate\Support\Facades\Http;

class Request {
    public static function getRequest($character, $url)
    {
        return Http::withHeaders(
            [
                'User-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.77 Safari/537.36'
            ])->withCookies(
            [
                'PHPSESSID' => $character->PHPSESSID,
                'authkey' => $character->authkey,
                'userid' => $character->userid,
                'player' => $character->player,
                'player_id' => $character->player_id,
            ], 'moswar.ru')->get($url);
    }

    public static function postRequest($character, $content, $contentType, $url)
    {
        return Http::withBody($content, $contentType)
            ->withHeaders(
                [
                    'User-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.77 Safari/537.36',
                    //'X-Requested-With' => 'XMLHttpRequest',
                ])
            ->withCookies(
                [
                    'PHPSESSID' => $character->PHPSESSID,
                    'authkey' => $character->authkey,
                    'userid' => $character->userid,
                    'player' => $character->player,
                    'player_id' => $character->player_id,
                ], 'moswar.ru')->post($url);
    }
}
