<?php

namespace App\Console\Commands;

use App\Classes\SendRequest;
use Carbon\Carbon;
use Illuminate\Console\Command;
use simplehtmldom\HtmlDocument;

class Patriot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'patriot:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command start watch patriot TV';

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
        $patriots = \App\Models\Patriot::with('character.licence')
            ->whereHas('character.licence', function ($query) {
                $query->where('end', '>', Carbon::now());
            })->where('time_start', '<', Carbon::now()->format('H:i:s'))
            ->get();
        foreach ($patriots as $patriot) {
            $alleyPage = SendRequest::getRequest(
                $patriot->character,
                'https://www.moswar.ru/alley/'
            );
            $document = new HtmlDocument();
            $document->load($alleyPage->body());
            /**
             * находим кнопку, true, если она есть
             * false, если ее нет
             */
            $patriotTV = isset($document->find("button[onclick=$('#patriottvForm').trigger('submit');]")[0]);
            /**
             * истекло ли время, true, если истекло
             */
            $timeleft = $document->find('form[id=patriottvForm] p[class=timeleft]')[0]->plaintext;
            $flag = true;
            if ($timeleft == 'На сегодня Вы уже истратили все время перед ТВ.' || !$patriotTV) {
                $flag = false;
            }

            if ($flag) {
                $content = 'action=patriottv&time=' . $patriot->getRawOriginal('time') .' &__ajax=1&return_url=%2Falley%2F';
                $watch = SendRequest::postRequest(
                    $patriot->character,
                    $content,
                    'application/x-www-form-urlencoded; charset=UTF-8',
                    'https://www.moswar.ru/alley/'
                );
                $patriot->last_start = Carbon::now();
                $patriot->save();
            }
        }
    }
}
