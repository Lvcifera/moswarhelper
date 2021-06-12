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
        $patrols = \App\Models\Patrol::with('character.licence')
            ->whereHas('character.licence', function ($query) {
                $query->where('end', '>', Carbon::now());
            })->get();

        foreach ($patrols as $patrol) {
            $alleyPage = SendRequest::getRequest($patrol->character, 'https://www.moswar.ru/alley/');
            $document = new HtmlDocument();
            $document->load($alleyPage->body());
            $first_region = isset($document->find('div[class=regions-choose] li[data-metro-id='. $patrol->getRawOriginal('first_region') . ']')[0]);
            $second_region = isset($document->find('div[class=regions-choose] li[data-metro-id='. $patrol->getRawOriginal('second_region') . ']')[0]);
            $third_region = isset($document->find('div[class=regions-choose] li[data-metro-id='. $patrol->getRawOriginal('third_region') . ']')[0]);

            /**
             * если на странице есть кнопка старта патрулирования
             */
            $button = isset($document->find("button[onclick=$('#patrolForm').trigger('submit');]")[0]);
            if ($button) {
                if ($first_region) {
                    $content = 'action=patrol&region=' . $patrol->getRawOriginal('first_region') . '&time=' . $patrol->time . '&__ajax=1&return_url=/alley/';
                    $patrol_start = SendRequest::postRequest(
                        $patrol->character,
                        $content,
                        'application/x-www-form-urlencoded; charset=UTF-8',
                        'https://www.moswar.ru/alley/'
                    );
                } elseif (!$first_region && $second_region) {
                    $content = 'action=patrol&region=' . $patrol->getRawOriginal('second_region') . '&time=' . $patrol->time . '&__ajax=1&return_url=/alley/';
                    $patrol_start = SendRequest::postRequest(
                        $patrol->character,
                        $content,
                        'application/x-www-form-urlencoded; charset=UTF-8',
                        'https://www.moswar.ru/alley/'
                    );
                } elseif (!$first_region && !$second_region && $third_region) {
                    $content = 'action=patrol&region=' . $patrol->getRawOriginal('third_region') . '&time=' . $patrol->time . '&__ajax=1&return_url=/alley/';
                    $patrol_start = SendRequest::postRequest(
                        $patrol->character,
                        $content,
                        'application/x-www-form-urlencoded; charset=UTF-8',
                        'https://www.moswar.ru/alley/'
                    );
                }
                $patrol->last_start = Carbon::now();
                $patrol->save();
            }
        }
    }
}
