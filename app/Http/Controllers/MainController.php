<?php

namespace App\Http\Controllers;

use App\Http\Requests\CharacterRequest;
use App\Http\Requests\LicenceRequest;
use App\Models\Character;
use App\Models\Licence;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Http;
use simplehtmldom\HtmlDocument;

class MainController extends Controller
{
    public function characters()
    {
        $characters = Character::where('user_id', '=', auth()->id())
            ->get();

        return view('characters', compact('characters'));
    }

    public function characterAdd(CharacterRequest $request)
    {
        /**
         * авторизация персонажа
         */
        $content = 'action=login&email=' . $request->get('email') . '&password=' . $request->get('password') . '&remember=on';
        $contentType = 'application/x-www-form-urlencoded';
        $response = Http::withBody($content, $contentType)
            ->withHeaders(
                [
                    'User-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.77 Safari/537.36',
                ])->post('https://www.moswar.ru/');
        if ($response->cookies()->count() == 3) {
            return redirect()->route('characters')->with('danger', 'Некорректные данные для авторизации');
        }
        $cookies = $response->cookies()->toArray();

        /**
         * проверка на существование у пользователя
         * лицензии на данного персонажа
         */
        try {
            $licence = Licence::with('user')
                ->where('user_id', '=', auth()->id())
                ->where('player', '=', urldecode($cookies[3]['Value']))
                ->firstOrFail();
        } catch (ModelNotFoundException $exception) {
            return redirect()->route('characters')->with('danger', 'У вас нет лицензии на этого персонажа');
        }

        /**
         * открываем страницу магазина и
         * вырезаем параметр для покупки
         */
        $shopPage = Http::withHeaders(
            [
                'User-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.77 Safari/537.36'
            ])->withCookies(
            [
                'PHPSESSID' => $cookies[0]['Value'],
                'authkey' => $cookies[1]['Value'],
                'userid' => $cookies[2]['Value'],
                'player' => $cookies[3]['Value'],
                'player_id' => $cookies[4]['Value'],
            ], 'moswar.ru')->get('https://www.moswar.ru/berezka/section/mixed/');
        $document = new HtmlDocument();
        $document->load($shopPage->body());
        $param = $document->find('div[id=box_teeth] span[class=f]');
        $param = mb_strcut($param[0]->attr['onclick'], 45, 40);

        $character = Character::updateOrCreate(
            [
                'player' => $cookies[3]['Value'],
            ],
            [
                'user_id' => auth()->id(),
                'licence_id' => $licence->id,
                'PHPSESSID' => $cookies[0]['Value'],
                'authkey' => $cookies[1]['Value'],
                'userid' => $cookies[2]['Value'],
                'player' => urldecode($cookies[3]['Value']),
                'player_id' => $cookies[4]['Value'],
                'param' => $param,
                'email' => $request->get('email'),
                'password' => $request->get('password'),
            ]
        );
        if ($character->wasRecentlyCreated) {
            return redirect()->route('characters')->with('success', 'Персонаж успешно авторизован');
        } else {
            return redirect()->route('characters')->with('success', 'Данные персонажа успешно обновлены');
        }
    }

    public function characterDelete($id)
    {
        $character = Character::find($id);
        $character->delete();

        return redirect()->route('characters')->with('success', 'Персонаж успешно удален');
    }

    public function licences()
    {
        $licences = Licence::where('user_id', '=', auth()->user()->id)->get();
        return view('licences', compact('licences'));
    }

    public function licenceAdd(LicenceRequest $request)
    {
        if ($request->monthCount * 50 > auth()->user()->balance) {
            return redirect()->route('licences')->with('danger', 'На вашем балансе недостаточно средств для создания лицензии');
        }
        $licence = Licence::firstOrCreate(
            [
                'user_id' => auth()->id(),
                'player' => $request->get('player'),
            ],
            [
                'user_id' => auth()->id(),
                'player' => $request->get('player'),
                'start' => Carbon::now(),
                'end' => Carbon::now()->addMonths($request->get('monthCount'))
            ]
        );
        if ($licence->wasRecentlyCreated) {
            $user = User::find(auth()->id());
            $user->balance -= $request->get('monthCount') * 50;
            $user->update();
            return redirect()->route('licences')->with('success', 'Лицензия успешно добавлена');
        } else {
            return redirect()->route('licences')->with('danger', 'Лицензия на этого персонажа уже существует');
        }
    }

    public function manual()
    {
        return view('manual');
    }

    public function news()
    {
        return view('news');
    }
}
