<?php

namespace App\Console\Commands;

use App\Classes\Request;
use Carbon\Carbon;
use Illuminate\Console\Command;
use simplehtmldom\HtmlDocument;

class Potion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'potion:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command buy potions to left money';

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
        $potions = \App\Models\Potion::with('character.licence')
            ->whereHas('character.licence', function ($query) {
                $query->where('end', '>', Carbon::now());
            })->get();

        foreach ($potions as $potion) {
            $response = Request::getRequest($potion->character, 'https://www.moswar.ru/alley/');
            $document = new HtmlDocument();
            $document->load($response->body());
            $money = $document->find('ul[class=wallet wallet-4] li[class=tugriki-block]');
            $currentMoney = (integer) mb_strcut($money[0]->attr['title'], 12);
            $potionCount = (integer) floor(((integer) mb_strcut($money[0]->attr['title'], 12) - $potion->money_left) / 100);

            if ($currentMoney < $potion->money_left) {
                continue;
            }

            $content = 'key=' . $potion->character->param . '&action=buy&item=51&amount=' . $potionCount . '&return_url=%2Fshop%2Fsection%2Fpharmacy%2F&type=&ajax_ext=2&autochange_honey=0';
            $buyPotion = Request::postRequest(
                $potion->character,
                $content,
                'application/x-www-form-urlencoded; charset=UTF-8',
                'https://www.moswar.ru/shop/json/'
            );
            $potion->last_start = Carbon::now();
            $potion->save();
        }
    }
}
