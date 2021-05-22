<?php

namespace App\Http\Controllers;

use App\Http\Requests\GypsyRequest;
use App\Http\Requests\LicenceRequest;
use App\Http\Requests\MoscowpolyRequest;
use App\Http\Requests\PetriksRequest;
use App\Http\Requests\TeethRequest;
use App\Models\Player;
use App\Models\Licence;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MainController extends Controller
{
    public function authForm()
    {
        return view('auth');
    }

    public function authorizeTry(Request $request)
    {
        /**
         * запрос на авторизацию персонажа
         */
        $response = Http::asForm()->post('https://www.moswar.ru', [
            'action' => $request->action,
            'email' => $request->email,
            'password' => $request->password,
            'remember' => $request->remember
        ]);
        if ($response->cookies()->toArray()[2]['Value'] == 'deleted') {
            return redirect()->route('auth')->with('danger', 'Некорректные данные для авторизации');
        }

        /**
         * запрос на получение страницы магазина
         * после авторизации
         */
        $playerPage = Http::withCookies(
            [
                'PHPSESSID' => $response->cookies()->toArray()[0]['Value'],
                'authkey' => $response->cookies()->toArray()[1]['Value'],
                'userid' => $response->cookies()->toArray()[2]['Value'],
                'player' => $response->cookies()->toArray()[3]['Value'],
                'player_id' => $response->cookies()->toArray()[4]['Value'],
            ], 'moswar.ru')->get('https://www.moswar.ru/berezka/section/mixed/');

        /**
         * проверяем, есть ли у текущего пользователя
         * лицензия на авторизованного персонажа
         */
        $userLicences = User::find(auth()->id())->licences;
        if (!$userLicences->contains('player', urldecode($response->cookies()->toArray()[3]['Value']))) {
            return redirect()->route('auth')->with('danger', 'У вас нет лицензии на этого персонажа');
        }

        /**
         * вырезаем param из страницы
         * (понадобится для покупки
         * зубного ящика в березке)
         */
        $string = explode("params:['", $playerPage->body());
        $param = mb_strcut($string[1], 0, 40);

        $character = Player::where('userid', '=', $response->cookies()->toArray()[2]['Value'])
            ->where('user_id', '=', auth()->id())
            ->first();
        if ($character == null) {
            $character = new Player();
            $character->user_id = auth()->user()->id;
            $character->PHPSESSID = $response->cookies()->toArray()[0]['Value'];
            $character->authkey = $response->cookies()->toArray()[1]['Value'];
            $character->userid = $response->cookies()->toArray()[2]['Value'];
            $character->player = urldecode($response->cookies()->toArray()[3]['Value']);
            $character->player_id = $response->cookies()->toArray()[4]['Value'];
            $character->param = $param;
            $character->save();
        } else {
            $character = Player::where('userid', '=', $response->cookies()->toArray()[2]['Value'])->first();
            $character->PHPSESSID = $response->cookies()->toArray()[0]['Value'];
            $character->authkey = $response->cookies()->toArray()[1]['Value'];
            $character->userid = $response->cookies()->toArray()[2]['Value'];
            $character->player = urldecode($response->cookies()->toArray()[3]['Value']);
            $character->player_id = $response->cookies()->toArray()[4]['Value'];
            $character->update();
        }

        return redirect()->back()->with('success', 'Успешная авторизация');
    }

    public function teeth()
    {
        $players = User::find(auth()->id())->players;
        return view('modules.teeth', compact('players'));
    }

    public function teethWork(TeethRequest $request)
    {
        $playerData = Player::where('player', '=', $request->player)->first();

        $start_time = new Carbon();
        $count = 0;
        while ($count < $request->teethCount) {
            /**
             * покупаем зубной ящик
             */
            $buy = Http::withBody('key=' . $playerData->param . '&' .
                'action=buy&' . 'item=6603&amount=&return_url=%2Fberezka%2Fsection%2Fmixed%2F&' .
                'type=&ajax_ext=2&autochange_honey=0', 'application/x-www-form-urlencoded; charset=UTF-8')
                ->withCookies(
                    [
                        'PHPSESSID' => $playerData->PHPSESSID,
                        'authkey' => $playerData->authkey,
                        'userid' => $playerData->userid,
                        'player' => urlencode($playerData->player),
                        'player_id' => $playerData->player_id,
                    ], 'moswar.ru')->post('https://www.moswar.ru/shop/json/');
            /**
             * получаем ID купленного зубного ящика
             */
            $getBoxID = Http::withCookies(
                    [
                        'PHPSESSID' => $playerData->PHPSESSID,
                        'authkey' => $playerData->authkey,
                        'userid' => $playerData->userid,
                        'player' => urlencode($playerData->player),
                        'player_id' => $playerData->player_id,
                    ], 'moswar.ru')->get('https://www.moswar.ru/player');
            $boxesData = explode('id="inventory-box_teeth-btn" data-action="use" data-id="', $getBoxID->body());
            $boxID = mb_strcut(array_pop($boxesData), 0, 10);
            /**
             * открываем купленный зубной ящик,
             * используя его уникальный ID
             */
            $getBoxID = Http::withCookies(
                    [
                        'PHPSESSID' => $playerData->PHPSESSID,
                        'authkey' => $playerData->authkey,
                        'userid' => $playerData->userid,
                        'player' => urlencode($playerData->player),
                        'player_id' => $playerData->player_id,
                    ], 'moswar.ru')->get('https://www.moswar.ru/player/json/use/' . $boxID . '/');
            if ($buy->json('result') == 1) {
                $count++;
            }
            if ($buy->json('result') == 0) {
                $end_time = new Carbon();
                $time = $end_time->diffInSeconds($start_time);
                break;
                return redirect()->route('teeth')->with('success', 'Действие выполнено частично, закончились зубы,
                 куплено ' . $count . ' зубных ящиков. Затраченное время ' . gmdate('H:i:s', $time) . ' секунд');
            }
        }
        $end_time = new Carbon();
        $time = $end_time->diffInSeconds($start_time);

        return redirect()->route('teeth')->with('success', 'Действие успешно выполнено, затраченное время ' . gmdate('H:i:s', $time));
    }

    public function licences()
    {
        $licences = Licence::where('user_id', '=', auth()->user()->id)->get();
        return view('licences', compact('licences'));
    }

    public function licenceAdd(LicenceRequest $request)
    {
        /**
         * создаем лицензию
         */
        $license = new Licence();
        $license->user_id = auth()->user()->id;
        $license->player = $request->player;
        $license->start = Carbon::now();
        $license->end = Carbon::now()->addMonths($request->monthCount);
        $license->save();

        return redirect()->route('licences')->with('success', 'Лицензия успешно добавлена');
    }

    public function manual()
    {
        return view('manual');
    }

    public function moscowpoly()
    {
        $players = User::find(auth()->id())->players;
        return view('modules.moscowpoly', compact('players'));
    }

    public function moscowpolyWork(MoscowpolyRequest $request)
    {
        $playerData = Player::where('player', '=', $request->player)->first();

        $start_time = new Carbon();
        $count = 0;
        while ($count < $request->cubesCount) {
            /**
             * бросаем кубик
             */
            $roll = Http::withBody('action=moscowpoly_roll&ajax=1&__referrer=%2Fhome%2F&return_url=%2Fhome%2F',
                'application/x-www-form-urlencoded; charset=UTF-8')
                ->withCookies(
                    [
                        'PHPSESSID' => $playerData->PHPSESSID,
                        'authkey' => $playerData->authkey,
                        'userid' => $playerData->userid,
                        'player' => urlencode($playerData->player),
                        'player_id' => $playerData->player_id,
                    ], 'moswar.ru')->post('https://www.moswar.ru/home/moscowpoly_roll/');
            if (!$roll->json('result')) {
                $end_time = new Carbon();
                $time = $end_time->diffInSeconds($start_time);
                break;
                return redirect()->route('moscowpoly')->with('danger', 'У вас закончились кубики.
                Брошено ' . $count . '. Затраченное время ' . $time . ' секунд');
            }
            $count++;
            /**
             * забираем приз
             */
            $get_prize = Http::withBody('action=moscowpoly_activate&ajax=1&__referrer=%2Fhome%2F&return_url=%2Fhome%2F',
                'application/x-www-form-urlencoded; charset=UTF-8')
                ->withCookies(
                    [
                        'PHPSESSID' => $playerData->PHPSESSID,
                        'authkey' => $playerData->authkey,
                        'userid' => $playerData->userid,
                        'player' => urlencode($playerData->player),
                        'player_id' => $playerData->player_id,
                    ], 'moswar.ru')->post('https://www.moswar.ru/home/moscowpoly_activate/');
        }
        $end_time = new Carbon();
        $time = $end_time->diffInSeconds($start_time);

        return redirect()->route('moscowpoly')->with('success', 'Действие успешно выполнено, затраченное время ' . gmdate('H:i:s', $time));
    }

    public function gypsy()
    {
        $players = User::find(auth()->id())->players;
        return view('modules.gypsy', compact('players'));
    }

    public function gypsyWork(GypsyRequest $request)
    {
        $playerData = Player::where('player', '=', $request->player)->first();

        $start_time = new Carbon();
        $count = 0;
        while ($count < $request->gypsyCount) {
            /**
             * начинаем игру
             */
            $start_game = Http::withBody('action=gypsyStart&gametype=1',
                'application/x-www-form-urlencoded; charset=UTF-8')
                ->withCookies(
                    [
                        'PHPSESSID' => $playerData->PHPSESSID,
                        'authkey' => $playerData->authkey,
                        'userid' => $playerData->userid,
                        'player' => urlencode($playerData->player),
                        'player_id' => $playerData->player_id,
                        'cmpstate' => 'old'
                    ], 'moswar.ru')->post('https://www.moswar.ru/camp/gypsy/');
            /**
             * ставим автоматическую игру
             */
            $auto_game = Http::withBody('action=gypsyAuto',
                'application/x-www-form-urlencoded; charset=UTF-8')
                ->withCookies(
                    [
                        'PHPSESSID' => $playerData->PHPSESSID,
                        'authkey' => $playerData->authkey,
                        'userid' => $playerData->userid,
                        'player' => urlencode($playerData->player),
                        'player_id' => $playerData->player_id,
                    ], 'moswar.ru')->post('https://www.moswar.ru/camp/gypsy/');
            $count++;
        }
        $end_time = new Carbon();
        $time = $end_time->diffInSeconds($start_time);

        return redirect()->route('gypsy')->with('success', 'Действие успешно выполнено, затраченное время ' . gmdate('H:i:s', $time));
    }

    public function petriks()
    {
        $players = User::find(auth()->id())->players;
        return view('modules.petriks', compact('players'));
    }

    public function petriksWork(PetriksRequest $request)
    {
        $playerData = Player::where('player', '=', $request->player)->first();

        $start_time = new Carbon();
        $count = 0;
        while ($count < $request->nanoCount) {
            $doPetriks = Http::withBody('player=' . $playerData->player_id . '&__ajax=1&return_url=/factory/',
                'application/x-www-form-urlencoded; charset=UTF-8')
                ->withCookies(
                    [
                        'PHPSESSID' => $playerData->PHPSESSID,
                        'authkey' => $playerData->authkey,
                        'userid' => $playerData->userid,
                        'player' => urlencode($playerData->player),
                        'player_id' => $playerData->player_id,
                    ], 'moswar.ru')->post('https://www.moswar.ru/factory/start-petriks/');
            $count++;
        }
        $end_time = new Carbon();
        $time = $end_time->diffInSeconds($start_time);

        return redirect()->route('petriks')->with('success', 'Действие успешно выполнено, затраченное время ' . gmdate('H:i:s', $time));
    }
}
