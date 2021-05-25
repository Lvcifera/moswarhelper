<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class Patrol extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'patrol:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command send request to start patrol';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $patrols = \App\Models\Patrol::whereHas('character.licence', function ($query) {
            $query->where('end', '>', Carbon::now());
        })->get();

        foreach ($patrols as $patrol) {
            /**
             * зайдем в закоулки, проверим активно
             * ли патрулирование или на сегодня
             * больше нет времени
             */
            $flag = true;
            $alley = Http::withBody('','application/x-www-form-urlencoded; charset=UTF-8')
                ->withCookies(
                    [
                        'PHPSESSID' => $patrol->character->PHPSESSID,
                        'authkey' => $patrol->character->authkey,
                        'userid' => $patrol->character->userid,
                        'player' => $patrol->character->player,
                        'player_id' => $patrol->character->player_id,
                    ], 'moswar.ru')->get('https://www.moswar.ru/alley/');
            $time_lost = explode("На сегодня Вы уже истратили все время патрулирования", $alley->body());
            $patrol_active = explode("Улизнуть с патрулирования", $alley->body());
            if (count($time_lost) == 2) { // на сегодня больше нет времени
                $flag = false;
            } elseif (count($patrol_active) == 2) { // на данный момент персонаж уже патрулирует
                $flag = false;
            }

            /**
             * если время для патрулирования еще есть
             * и персонаж на данный момент не патрулирует,
             * отправляем его в патруль
             */
            if ($flag) {
                $content = 'action=patrol&region=' . $patrol->getRawOriginal('region') . '&time=' . $patrol->time . '&__ajax=1&return_url=/alley/';
                $patrol_start = Http::withBody($content,
                    'application/x-www-form-urlencoded; charset=UTF-8')
                    ->withCookies(
                        [
                            'PHPSESSID' => $patrol->character->PHPSESSID,
                            'authkey' => $patrol->character->authkey,
                            'userid' => $patrol->character->userid,
                            'player' => $patrol->character->player,
                            'player_id' => $patrol->character->player_id,
                        ], 'moswar.ru')->post('https://www.moswar.ru/alley/');
                $patrol->last_start = Carbon::now();
                $patrol->save();
            }
        }
    }
}
