<?php

namespace App\Http\Controllers;

use App\Http\Requests\GypsyRequest;
use App\Http\Requests\LicenceRequest;
use App\Http\Requests\MoscowpolyRequest;
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

        $character = Player::where('userid', '=', $response->cookies()->toArray()[2]['Value'])->first();
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
        /**
         * если пользователь не авторизован
         */
        if (auth()->user() != null) {
            $players = User::find(auth()->id())->players;
            return view('modules.teeth', compact('players'));
        } else {
            return redirect()->route('login');
        }
    }

    public function teethWork(TeethRequest $request)
    {
        $playerData = Player::where('player', '=', $request->player)->first();

        $start_time = new Carbon();
        for ($i = 0; $i < $request->teethCount; $i++) {
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
        }
        $end_time = new Carbon();
        $time = $end_time->diffInSeconds($start_time);

        return redirect()->route('teeth')->with('success', 'Действие успешно выполнено, затраченное время ' . $time . ' секунд');
    }

    public function licences()
    {
        $licences = Licence::where('user_id', '=', auth()->user()->id)->get();

        if (auth()->user() != null) {
            return view('licences', compact('licences'));
        } else {
            return redirect()->route('login');
        }
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
        /**
         * если пользователь не авторизован
         */
        if (auth()->user() != null) {
            $players = User::find(auth()->id())->players;
            return view('modules.moscowpoly', compact('players'));
        } else {
            return redirect()->route('login');
        }
    }

    public function moscowpolyWork(MoscowpolyRequest $request)
    {
        $playerData = Player::where('player', '=', $request->player)->first();

        $start_time = new Carbon();
        for ($i = 0; $i < $request->cubesCount; $i++) {
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

        return redirect()->route('moscowpoly')->with('success', 'Действие успешно выполнено, затраченное время ' . $time . ' секунд');
    }

    public function gypsy()
    {
        /**
         * если пользователь не авторизован
         */
        if (auth()->user() != null) {
            $players = User::find(auth()->id())->players;
            return view('modules.gypsy', compact('players'));
        } else {
            return redirect()->route('login');
        }
    }

    public function gypsyWork(GypsyRequest $request)
    {
        $playerData = Player::where('player', '=', $request->player)->first();

        $start_time = new Carbon();
        for ($i = 0; $i < $request->gypsyCount; $i++) {
            $start_game = Http::withBody('action=gypsyStart&gametype=1',
                'application/x-www-form-urlencoded; charset=UTF-8')
                ->withCookies(
                    [
                        'PHPSESSID' => $playerData->PHPSESSID,
                        'authkey' => $playerData->authkey,
                        'userid' => $playerData->userid,
                        'player' => urlencode($playerData->player),
                        'player_id' => $playerData->player_id,
                    ], 'moswar.ru')->post('https://www.moswar.ru/camp/gypsy/');
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
        }
        $end_time = new Carbon();
        $time = $end_time->diffInSeconds($start_time);

        return redirect()->route('gypsy')->with('success', 'Действие успешно выполнено, затраченное время ' . $time . ' секунд');
    }

    public function petriks()
    {
        if (auth()->user() != null) {
            return view('modules.petriks');
        } else {
            return redirect()->route('login');
        }
    }
}
