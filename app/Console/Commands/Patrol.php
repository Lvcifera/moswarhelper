<?php

namespace App\Console\Commands;

use App\Classes\SendRequest;
use Carbon\Carbon;
use Illuminate\Console\Command;
use simplehtmldom\HtmlDocument;

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
            $playerPage = SendRequest::getRequest($patrol->character, 'https://www.moswar.ru/alley/');
            $document = new HtmlDocument();
            $document->load($playerPage->body());

            /**
             * если на сегодня израсходовано все
             * время на патрулирование или персонаж
             * сейчас патрулирует
             */
            $flag = true;
            $timeleft = $document->find('p[class=timeleft]')[0]->plaintext;
            $patrolProcess = $document->find("button[onclick=$('#patrolForm').trigger('submit');]");
            if ($timeleft == 'На сегодня Вы уже истратили все время патрулирования.' || empty($patrolProcess)) {
                $flag = false;
            }
            if ($flag) {
                $content = 'action=patrol&region=' . $patrol->getRawOriginal('region') . '&time=' . $patrol->time . '&__ajax=1&return_url=/alley/';
                $patrol_start = SendRequest::postRequest(
                    $patrol->character,
                    $content,
                    'application/x-www-form-urlencoded; charset=UTF-8',
                    'https://www.moswar.ru/alley/'
                );
                $patrol->last_start = Carbon::now();
                $patrol->save();
            }
        }
    }
}
