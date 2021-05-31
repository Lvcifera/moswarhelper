<?php

namespace App\Http\Controllers;

use App\Classes\SendRequest;
use App\Http\Requests\LicenceRequest;
use App\Models\Character;
use App\Models\Licence;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use simplehtmldom\HtmlDocument;

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
        $player = Character::find(1);

        $playerPage = SendRequest::getRequest($player, 'https://www.moswar.ru/alley/');

        $document = new HtmlDocument();
        $document->load($playerPage->body());
        //$result = $document->find('button[id=alley-patrol-button]');
        $result = $document->find('p[class=timeleft]');
        //$result = $document->find('div[id=home-garage] div[class=object-thumb] div[class=padding] a');
        dd($result[0]->plaintext);
    }
}
