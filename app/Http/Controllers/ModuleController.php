<?php

namespace App\Http\Controllers;

use App\Classes\Request;
use App\Http\Requests\GiftsRequest;
use App\Http\Requests\GypsyRequest;
use App\Http\Requests\MoscowpolyRequest;
use App\Http\Requests\PetriksRequest;
use App\Http\Requests\TeethRequest;
use App\Models\Character;
use Carbon\Carbon;
use simplehtmldom\HtmlDocument;

class ModuleController extends Controller
{
    public function teeth()
    {
        $characters = Character::where('user_id', '=', auth()->id())
            ->whereHas('licence', function ($query) {
            $query->where('end', '>', Carbon::now());
        })->get();
        return view('modules.teeth', compact('characters'));
    }

    public function teethWork(TeethRequest $request)
    {
        $character = Character::where('player', '=', $request->get('player'))->first();

            /**
             * проверяем, не находится ли персонаж
             * в стенке в данный момент времени
             */
            $campPage = Request::getRequest(
                $character,
                'https://www.moswar.ru/camp/'
            );
            $document = new HtmlDocument();
            $document->load($campPage->body());
            $title = $document->find('title');

            if ($title[0]->_[5] == 'Стенка на стенку') {
                return response()->json(['fight' => true]);
            } else {
                /**
                 * покупаем зубной ящик
                 */
                $content = 'key=' . $character->param . '&' .
                    'action=buy&' . 'item=6603&amount=&return_url=%2Fberezka%2Fsection%2Fmixed%2F&' .
                    'type=&ajax_ext=2&autochange_honey=0';
                $buy = Request::postRequest(
                    $character,
                    $content,
                    'application/x-www-form-urlencoded; charset=UTF-8',
                    'https://www.moswar.ru/shop/json/'
                );

                /**
                 * если покупка ящика была успешной
                 */
                if ($buy->json('result') == 1) {
                    /**
                     * открываем купленный зубной ящик,
                     * используя его уникальный ID,
                     * предварительно обновив страницу
                     */
                    $reloadPage = Request::getRequest(
                        $character,
                        'https://www.moswar.ru/player/'
                    );
                    $document = new HtmlDocument();
                    $document->load($reloadPage->body());
                    $getBoxID = $document->find('div[id=inventory-box_teeth-btn]');
                    foreach ($getBoxID as $item) {
                        $openBox = Request::getRequest(
                            $character,
                            'https://www.moswar.ru/player/json/use/' . $item->attr['data-id'] . '/'
                        );
                    }
                } elseif ($buy->json('result') == 0) {
                    return response()->json(['teethLost' => true]);
                }
            }

        return response()->json(['success' => true]);
    }

    public function moscowpoly()
    {
        $characters = Character::where('user_id', '=', auth()->id())
            ->whereHas('licence', function ($query) {
                $query->where('end', '>', Carbon::now());
            })->get();
        return view('modules.moscowpoly', compact('characters'));
    }

    public function moscowpolyWork(MoscowpolyRequest $request)
    {
        $character = Character::where('player', '=', $request->get('player'))->first();

        $start_time = new Carbon();
        $count = 0;
        $contentRoll = 'action=moscowpoly_roll&ajax=1&__referrer=%2Fhome%2F&return_url=%2Fhome%2F';
        $contentGetPrize = 'action=moscowpoly_activate&ajax=1&__referrer=%2Fhome%2F&return_url=%2Fhome%2F';
        while ($count < $request->get('cubesCount')) {
            /**
             * проверяем, не находится ли персонаж
             * в стенке в данный момент времени
             */
            $campPage = Request::getRequest($character, 'https://www.moswar.ru/camp/');
            $document = new HtmlDocument();
            $document->load($campPage->body());
            $title = $document->find('title');

            if ($title[0]->_[5] == 'Стенка на стенку') {
                $end_time = new Carbon();
                $time = $end_time->diffInSeconds($start_time);
                return redirect()->route('moscowpoly')->with('danger', 'Действие выполнено частично, персонаж находится в стенке.
                Брошено ' . $count . '. Затраченное время ' . $time . ' секунд');
            } else {
                /**
                 * бросаем кубик
                 */
                $roll = Request::postRequest(
                    $character,
                    $contentRoll,
                    'application/x-www-form-urlencoded; charset=UTF-8',
                    'https://www.moswar.ru/home/moscowpoly_roll/'
                );
                if (!$roll->json('result')) {
                    $end_time = new Carbon();
                    $time = $end_time->diffInSeconds($start_time);
                    return redirect()->route('moscowpoly')->with('danger', 'У вас закончились кубики.
                Брошено ' . $count . '. Затраченное время ' . $time . ' секунд');
                    break;
                }
                $count++;

                /**
                 * забираем приз
                 */
                $get_prize = Request::postRequest(
                    $character,
                    $contentGetPrize,
                    'application/x-www-form-urlencoded; charset=UTF-8',
                    'https://www.moswar.ru/home/moscowpoly_activate/'
                );
            }
        }
        $end_time = new Carbon();
        $time = $end_time->diffInSeconds($start_time);

        return redirect()->route('moscowpoly')->with('success', 'Действие успешно выполнено, затраченное время ' . gmdate('H:i:s', $time));
    }

    public function gypsy()
    {
        $characters = Character::where('user_id', '=', auth()->id())
            ->whereHas('licence', function ($query) {
                $query->where('end', '>', Carbon::now());
            })->get();
        return view('modules.gypsy', compact('characters'));
    }

    public function gypsyWork(GypsyRequest $request)
    {
        $character = Character::where('player', '=', $request->get('player'))->first();

        $start_time = new Carbon();
        $count = 0;
        $contentStartGame = 'action=gypsyStart&gametype=1';
        $contentAutoGame = 'action=gypsyAuto';
        while ($count < $request->get('gypsyCount')) {
            /**
             * проверяем, не находится ли персонаж
             * в стенке в данный момент времени
             */
            $campPage = Request::getRequest($character, 'https://www.moswar.ru/camp/');
            $document = new HtmlDocument();
            $document->load($campPage->body());
            $title = $document->find('title');

            if ($title[0]->_[5] == 'Стенка на стенку') {
                $end_time = new Carbon();
                $time = $end_time->diffInSeconds($start_time);
                return redirect()->route('gypsy')->with('danger', 'Действие выполнено частично, персонаж находится в стенке.
                Сыграно ' . $count . ' раз. Затраченное время ' . $time . ' секунд');
            } else {
                /**
                 * начинаем игру
                 */
                $start_game = Request::postRequest(
                    $character,
                    $contentStartGame,
                    'application/x-www-form-urlencoded; charset=UTF-8',
                    'https://www.moswar.ru/camp/gypsy/'
                );

                /**
                 * ставим автоматическую игру
                 */
                $auto_game = Request::postRequest(
                    $character,
                    $contentAutoGame,
                    'application/x-www-form-urlencoded; charset=UTF-8',
                    'https://www.moswar.ru/camp/gypsy/'
                );
                $count++;
            }
        }
        $end_time = new Carbon();
        $time = $end_time->diffInSeconds($start_time);

        return redirect()->route('gypsy')->with('success', 'Действие успешно выполнено, затраченное время ' . gmdate('H:i:s', $time));
    }

    public function petriks()
    {
        $characters = Character::where('user_id', '=', auth()->id())
            ->whereHas('licence', function ($query) {
                $query->where('end', '>', Carbon::now());
            })->get();
        return view('modules.petriks', compact('characters'));
    }

    public function petriksWork(PetriksRequest $request)
    {
        $character = Character::where('player', '=', $request->get('player'))->first();

        $start_time = new Carbon();
        $count = 0;
        $content = 'player=' . $character->player_id . '&__ajax=1&return_url=/factory/';
        while ($count < $request->get('nanoCount')) {
            /**
             * проверяем, не находится ли персонаж
             * в стенке в данный момент времени
             */
            $campPage = Request::getRequest($character, 'https://www.moswar.ru/camp/');
            $document = new HtmlDocument();
            $document->load($campPage->body());
            $title = $document->find('title');

            if ($title[0]->_[5] == 'Стенка на стенку') {
                $end_time = new Carbon();
                $time = $end_time->diffInSeconds($start_time);
                return redirect()->route('gypsy')->with('danger', 'Действие выполнено частично, персонаж находится в стенке.
                Сыграно ' . $count . ' раз. Затраченное время ' . $time . ' секунд');
            } else {
                $doPetriks = Request::postRequest(
                    $character,
                    $content,
                    'application/x-www-form-urlencoded; charset=UTF-8',
                    'https://www.moswar.ru/factory/start-petriks/'
                );
                $count++;
            }
        }
        $end_time = new Carbon();
        $time = $end_time->diffInSeconds($start_time);

        return redirect()->route('petriks')->with('success', 'Действие успешно выполнено, затраченное время ' . gmdate('H:i:s', $time));
    }

    public function gifts()
    {
        $characters = Character::where('user_id', '=', auth()->id())
            ->whereHas('licence', function ($query) {
                $query->where('end', '>', Carbon::now());
            })->get();
        return view('modules.gifts', compact('characters'));
    }

    public function giftsWork(GiftsRequest $request)
    {
        $character = Character::where('player', '=', $request->get('player'))->first();

        $start_time = new Carbon();
        /**
         * проверяем, существует ли персонаж с
         * указанным именем
         */
        $checkPlayerExist = Request::getRequest(
            $character,
            'https://www.moswar.ru/shop/playerexists/' . $request->get('reciever') . '/'
        );
        if ($checkPlayerExist->json() == 0) {
            return redirect()->route('gifts')->with('danger', 'Игрока с таким именем не существует');
        }

        $count = 0;
        $content = 'action=buy&return_url=%2Fshop%2Fsection%2Fgifts%2F%23negative&item=' .
            $request->get('gift') . '&playerid=&key=' . $character->param . '&player=' .
            $request->get('reciever') . '&comment=' . $request->get('comment') . '&';
        if ($request->get('private') != null) {
            $content .= 'private=on&';
        }
        if ($request->get('anonimous') != null) {
            $content .= 'anonimous=on&__ajax=1';
        }
        $content .= '&__ajax=1';
        while ($count < $request->get('giftCount')) {
            /**
             * дарим подарок
             */
            $gift = Request::postRequest(
                $character,
                $content,
                'application/x-www-form-urlencoded; charset=UTF-8',
                'https://www.moswar.ru/shop/'
            );
            $count++;
        }
        $end_time = new Carbon();
        $time = $end_time->diffInSeconds($start_time);

        return redirect()->route('gifts')->with('success', 'Действие успешно выполнено, затраченное время ' . gmdate('H:i:s', $time));
    }
}
