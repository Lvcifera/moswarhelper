<?php

namespace App\Console\Commands;

use App\Classes\SendRequest;
use Carbon\Carbon;
use Illuminate\Console\Command;
use simplehtmldom\HtmlDocument;

class Taxes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'taxes:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command start to tax with car what need';

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
        $taxes = \App\Models\Taxes::with('character.licence')
            ->whereHas('character.licence', function ($query) {
                $query->where('end', '>', Carbon::now());
            })->get();

        foreach ($taxes as $tax) {
            $arbatPage = SendRequest::getRequest($tax->character, 'https://www.moswar.ru/arbat/');
            $document = new HtmlDocument();
            $document->load($arbatPage->body());

            /**
             * если есть кнопка "Бомбить"
             */
            $buttonIsset = isset($document->find('button[class=button ride-button]')[0]);

            /**
             * отправляем машину бомбить
             */
            $content = 'car=' . $tax->carID . '&__ajax=1&return_url=%2Farbat%2F';
            if ($buttonIsset) {
                $taxes = SendRequest::postRequest(
                    $tax->character,
                    $content,
                    'application/x-www-form-urlencoded; charset=UTF-8',
                    'https://www.moswar.ru/automobile/bringup/'
                );
                $document->load($taxes->body());
                $needFuel = isset($document->find('div[id=alert-text]')[0]);
                if ($needFuel) {
                    $petrolContent = '__ajax=1&return_url=%2Fautomobile%2Fcar%2F' . $tax->carID . '%2F';
                    /**
                     * заправим машину, если у нее закончилось топливо
                     */
                    $buyPetrol = SendRequest::postRequest(
                        $tax->character,
                        $petrolContent,
                        'application/x-www-form-urlencoded; charset=UTF-8',
                        'https://www.moswar.ru/automobile/buypetrol/' . $tax->carID . '/'
                    );
                    /**
                     * затем отправим машину бомбить
                     */
                    $taxes = SendRequest::postRequest(
                        $tax->character,
                        $content,
                        'application/x-www-form-urlencoded; charset=UTF-8',
                        'https://www.moswar.ru/automobile/bringup/'
                    );
                }
                $tax->last_start = Carbon::now();
                $tax->save();
            }
        }
    }
}
