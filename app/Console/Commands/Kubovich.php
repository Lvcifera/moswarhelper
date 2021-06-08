<?php

namespace App\Console\Commands;

use App\Classes\SendRequest;
use App\Models\Character;
use Carbon\Carbon;
use Illuminate\Console\Command;
use simplehtmldom\HtmlDocument;

class Kubovich extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kubovich:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command send request to play kubovich';

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
        /**
         * загрузим список всех заданий
         */
        $kubovich = \App\Models\Kubovich::whereHas('character.licence', function ($query) {
            $query->where('end', '>', Carbon::now());
        })->whereHas('character.licence', function ($query) {
            $query->whereColumn('today_count', '!=', 'count');
        })->get();

        /**
         * перед стартом заданий необходимо проверить,
         * доступен ли сейчас кубович
         */
        $playerData = Character::find(3);
        $casinoPage = SendRequest::getRequest($playerData, 'https://www.moswar.ru/casino/kubovich/');
        $document = new HtmlDocument();
        $document->load($casinoPage->body());
        $flag = true;
        /**
         * проверяем наличие таймера на странице,
         * true, если он есть
         */
        $timerOnPage = isset($document->find('div[class=hungry-timer]')[0]);
        if (!$timerOnPage) {
            $flag = false;
        }

        if ($flag) {
            $changeOre = 'action=ore&count=20';
            $playKubovich = 'action=black&type=black';
            $loadYellow = 'action=load&type=yellow';
            $playYellow = 'action=yellow&type=black';
            foreach ($kubovich as $item) {
                /**
                 * перед стартом кубовича для каждого персонажа с активной
                 * лицензией произведем обмен 20 руды на 200 фишек
                 */
                $getChips = SendRequest::postRequest(
                    $item->character,
                    $changeOre,
                    'application/x-www-form-urlencoded; charset=UTF-8',
                    'https://www.moswar.ru/casino/'
                );

                /**
                 * играем с кубовичем столько раз, сколько
                 * указано в задании
                 */
                $count = $item->today_count;
                while ($count <= $item->count) {
                    $pushYellow = $document->find('button[id=push-ellow]');
                    if ($pushYellow[0]->attr['class'] == 'button') {
                         SendRequest::postRequest(
                             $item->character,
                             $loadYellow,
                             'application/x-www-form-urlencoded; charset=UTF-8',
                             'https://www.moswar.ru/casino/kubovich/'
                         );
                         SendRequest::postRequest(
                           $item->character,
                           $playYellow,
                           'application/x-www-form-urlencoded; charset=UTF-8',
                           'https://www.moswar.ru/casino/kubovich/'
                         );
                        SendRequest::getRequest(
                            $item->character,
                            'https://www.moswar.ru/casino/kubovich/'
                        );
                    }

                    $response = SendRequest::postRequest(
                        $item->character,
                        $playKubovich,
                        'application/x-www-form-urlencoded; charset=UTF-8',
                        'https://www.moswar.ru/casino/kubovich/'
                    );
                    $reloadPage = SendRequest::getRequest(
                        $item->character,
                        'https://www.moswar.ru/casino/kubovich/'
                    );
                    $count++;
                }

                /**
                 * сохраняем фактическое количество кручений
                 */
                $item->today_count = $count;
                $item->save();
            }
        }
    }
}
