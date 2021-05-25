<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class Shaurburgers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shaurburgers:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command send request to start shaurburgers';

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
        $shaurburgers = \App\Models\Shaurburgers::whereHas('character.licence', function ($query) {
            $query->where('end', '>', Carbon::now());
        })->get();

        foreach ($shaurburgers as $shaurburger) {
            /**
             * зайдем в шаурбургерс, проверим активна
             * ли работа или на сегодня
             * больше нет времени
             */
            $flag = true;
            $work = Http::withBody('','application/x-www-form-urlencoded; charset=UTF-8')
                ->withCookies(
                    [
                        'PHPSESSID' => $shaurburger->character->PHPSESSID,
                        'authkey' => $shaurburger->character->authkey,
                        'userid' => $shaurburger->character->userid,
                        'player' => $shaurburger->character->player,
                        'player_id' => $shaurburger->character->player_id,
                    ], 'moswar.ru')->get('https://www.moswar.ru/shaurburgers/');
            $time_lost = explode("На сегодня вы отработали свою максимальную смену", $work->body());
            $shaurburger_active = explode("1 час", $work->body());
            if (count($time_lost) == 2) { // на сегодня больше нет времени
                $flag = false;
            } elseif (count($shaurburger_active) == 1) { // на данный момент персонаж уже работает
                $flag = false;
            }

            /**
             * если время для работы еще есть
             * и персонаж на данный момент не работает,
             * отправляем его в работу
             */
            if ($flag) {
                $content = 'action=work&time=' . $shaurburger->getRawOriginal('time') . '&__ajax=1&return_url=/shaurburgers/';
                $shaurburgers_start = Http::withBody($content,
                    'application/x-www-form-urlencoded; charset=UTF-8')
                    ->withCookies(
                        [
                            'PHPSESSID' => $shaurburger->character->PHPSESSID,
                            'authkey' => $shaurburger->character->authkey,
                            'userid' => $shaurburger->character->userid,
                            'player' => $shaurburger->character->player,
                            'player_id' => $shaurburger->character->player_id,
                        ], 'moswar.ru')->post('https://www.moswar.ru/shaurburgers/');
                $shaurburger->last_start = Carbon::now();
                $shaurburger->save();
            }
        }
    }
}
