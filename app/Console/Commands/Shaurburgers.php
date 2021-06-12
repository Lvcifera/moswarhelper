<?php

namespace App\Console\Commands;

use App\Classes\Request;
use Carbon\Carbon;
use Illuminate\Console\Command;
use simplehtmldom\HtmlDocument;

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
            $playerPage = Request::getRequest($shaurburger->character, 'https://www.moswar.ru/shaurburgers/');
            $document = new HtmlDocument();
            $document->load($playerPage->body());

            /**
             * ищем кнопку старта работы,
             * empty() => true, если ее нет на странице;
             * либо если время работы на сегодня вышло
             */
            $flag = true;
            $shaurProcess = $document->find("span[onclick=$(this).addClass('disabled');$('#workForm').trigger('submit');]");
            $timeleft = isset($document->find('span[class=error]')[0]->_[5]);
            if (empty($shaurProcess) || $timeleft) {
                $flag = false;
            }
            if ($flag) {
                $content = 'action=work&time=' . $shaurburger->getRawOriginal('time') . '&__ajax=1&return_url=/shaurburgers/';
                $shaurburgers_start = Request::postRequest(
                    $shaurburger->character,
                    $content,
                    'application/x-www-form-urlencoded; charset=UTF-8',
                    'https://www.moswar.ru/shaurburgers/'
                );
                $shaurburger->last_start = Carbon::now();
                $shaurburger->save();
            }
        }
    }
}
