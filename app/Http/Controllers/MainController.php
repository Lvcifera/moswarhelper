<?php

namespace App\Http\Controllers;

use App\Models\Shaurburgers;
use App\Http\Requests\LicenceRequest;
use App\Models\Character;
use App\Models\Licence;
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
        $userLicence = Licence::with('user')
            ->where('user_id', '=', auth()->id())
            ->where('player', '=', urldecode($response->cookies()->toArray()[3]['Value']))
            ->first();
        if ($userLicence == null) {
            return redirect()->route('auth')->with('danger', 'У вас нет лицензии на этого персонажа');
        }

        /**
         * вырезаем param из страницы
         * (понадобится для покупки
         * зубного ящика в березке)
         */
        $string = explode("params:['", $playerPage->body());
        $param = mb_strcut($string[1], 0, 40);

        $character = Character::where('userid', '=', $response->cookies()->toArray()[2]['Value'])
            ->where('user_id', '=', auth()->id())
            ->first();
        if ($character == null) {
            $character = new Character();
            $character->user_id = auth()->user()->id;
            $character->licence_id = $userLicence->id;
            $character->PHPSESSID = $response->cookies()->toArray()[0]['Value'];
            $character->authkey = $response->cookies()->toArray()[1]['Value'];
            $character->userid = $response->cookies()->toArray()[2]['Value'];
            $character->player = urldecode($response->cookies()->toArray()[3]['Value']);
            $character->player_id = $response->cookies()->toArray()[4]['Value'];
            $character->param = $param;
            $character->save();
        } else {
            $character = Character::where('userid', '=', $response->cookies()->toArray()[2]['Value'])->first();
            $character->PHPSESSID = $response->cookies()->toArray()[0]['Value'];
            $character->authkey = $response->cookies()->toArray()[1]['Value'];
            $character->userid = $response->cookies()->toArray()[2]['Value'];
            $character->player = urldecode($response->cookies()->toArray()[3]['Value']);
            $character->player_id = $response->cookies()->toArray()[4]['Value'];
            $character->update();
        }

        return redirect()->back()->with('success', 'Успешная авторизация');
    }

    public function licences()
    {
        $licences = Licence::where('user_id', '=', auth()->user()->id)->get();
        return view('licences', compact('licences'));
    }

    public function licenceAdd(LicenceRequest $request)
    {
        /**
         * проверяем, создана ли уже лицензия
         * на выбранного персонажа
         */
        $licence = Licence::where('user_id', '=', auth()->id())
            ->where('player', '=', $request->player)
            ->first();

        if ($licence == null) {
            /**
             * создаем лицензию
             */
            $licence = new Licence();
            $licence->user_id = auth()->user()->id;
            $licence->player = $request->player;
            $licence->start = Carbon::now();
            $licence->end = Carbon::now()->addMonths($request->monthCount);
            $licence->save();

            return redirect()->route('licences')->with('success', 'Лицензия успешно добавлена');
        } else {
            return redirect()->route('licences')->with('danger', 'Лицензия на этого персонажа уже существует');
        }


    }

    public function manual()
    {
        return view('manual');
    }

    public function test()
    {
        $shaurburgers = Shaurburgers::with('character')
            ->get();

        foreach ($shaurburgers as $shaurburger) {
            /**
             * зайдем в шаурбургерс, проверим активна
             * ли работа или на сегодня
             * больше нет времени
             */
            $flag = true;
            $work = Http::withBody('','application/x-www-form-urlencoded; charset=UTF-8')
                ->withCookies(
                    [
                        'PHPSESSID' => $shaurburgers[2]->character->PHPSESSID,
                        'authkey' => $shaurburgers[2]->character->authkey,
                        'userid' => $shaurburgers[2]->character->userid,
                        'player' => $shaurburgers[2]->character->player,
                        'player_id' => $shaurburgers[2]->character->player_id,
                    ], 'moswar.ru')->get('https://www.moswar.ru/shaurburgers/');
            $time_lost = explode("На сегодня вы отработали свою максимальную смену", $work->body());
            $shaurburger_active = explode("1 час", $work->body());
            if (count($time_lost) == 2) { // на сегодня больше нет времени
                $flag = false;
            } elseif (count($shaurburger_active) == 1) { // на данный момент персонаж уже работает
                $flag = false;
            }

            /**
             * если время для патрулирования еще есть
             * и персонаж на данный момент не патрулирует,
             * отправляем его в патруль
             */
            if ($flag) {
                $content = 'action=work&time=' . $shaurburger->getRawOriginal('time') . '&__ajax=1&return_url=/shaurburgers/';
                $shaurburgers_start = Http::withBody($content,
                    'application/x-www-form-urlencoded; charset=UTF-8')
                    ->withCookies(
                        [
                            'PHPSESSID' => $shaurburger->character->PHPSESSID,
                            'authkey' => $shaurburger->character->authkey,
                            'userid' => $shaurburger->character->userid,
                            'player' => $shaurburger->character->player,
                            'player_id' => $shaurburger->character->player_id,
                        ], 'webhook.site')->post('https://webhook.site/aa368925-bd49-444c-b81c-1cdff8553542');
                $shaurburger->last_start = Carbon::now();
                $shaurburger->save();
            }
        }
    }
}
