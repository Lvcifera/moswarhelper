<?php

namespace App\Http\Controllers;

use App\Classes\SendRequest;
use App\Http\Requests\LicenceRequest;
use App\Models\Character;
use App\Models\Licence;
use App\Models\Log;
use App\Models\News;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
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

    public function characterAdd(Request $request)
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
            return redirect()->route('characters')->with('danger', 'Некорректные данные для авторизации');
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
            return redirect()->route('characters')->with('danger', 'У вас нет лицензии на этого персонажа');
        }

        /**
         * вырезаем param из страницы
         * (понадобится для покупки
         * зубного ящика в березке)
         */
        $document = new HtmlDocument();
        $document->load($playerPage->body());
        $param = $document->find('div[id=box_teeth] span[class=f]');
        $param = mb_strcut($param[0]->attr['onclick'], 45, 40);

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
            $character->password = $request->password;
            $character->email = $request->email;
            $character->save();
        } else {
            $character = Character::where('userid', '=', $response->cookies()->toArray()[2]['Value'])->first();
            $character->PHPSESSID = $response->cookies()->toArray()[0]['Value'];
            $character->authkey = $response->cookies()->toArray()[1]['Value'];
            $character->userid = $response->cookies()->toArray()[2]['Value'];
            $character->player = urldecode($response->cookies()->toArray()[3]['Value']);
            $character->player_id = $response->cookies()->toArray()[4]['Value'];
            $character->param = $param;
            $character->password = $request->password;
            $character->email = $request->email;
            $character->update();
        }

        return redirect()->back()->with('success', 'Успешная авторизация');
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
        /**
         * проверяем, создана ли уже лицензия
         * на выбранного персонажа
         */
        $licence = Licence::where('user_id', '=', auth()->id())
            ->where('player', '=', $request->player)
            ->first();
        if ($request->monthCount * 50 > auth()->user()->balance) {
            return redirect()->route('licences')->with('danger', 'На вашем балансе недостаточно средств для создания лицензии');
        }

        if ($licence == null) {
            $user = User::find(auth()->id());
            $user->balance -= $request->monthCount * 50;
            $user->update();

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

    public function news() {
        return view('news');
    }

    public function feedbackForm()
    {
        return view('feedback');
    }

    public function test()
    {
        $patrols = \App\Models\Patrol::with('character.licence')
            ->whereHas('character.licence', function ($query) {
                $query->where('end', '>', Carbon::now());
            })->get();

        foreach ($patrols as $patrol) {
            $alleyPage = SendRequest::getRequest($patrol->character, 'https://www.moswar.ru/alley/');
            $document = new HtmlDocument();
            $document->load($alleyPage->body());
            $first_region = isset($document->find('div[class=regions-choose] li[data-metro-id='. $patrol->getRawOriginal('first_region') . ']')[0]);
            $second_region = isset($document->find('div[class=regions-choose] li[data-metro-id='. $patrol->getRawOriginal('second_region') . ']')[0]);
            $third_region = isset($document->find('div[class=regions-choose] li[data-metro-id='. $patrol->getRawOriginal('third_region') . ']')[0]);
            /**
             * если на сегодня израсходовано все
             * время на патрулирование или персонаж
             * сейчас патрулирует
             */
            $button = isset($document->find("button[onclick=$('#patrolForm').trigger('submit');]")[0]);
            if ($button) {
                if ($first_region) {
                    $content = 'action=patrol&region=' . $patrol->getRawOriginal('first_region') . '&time=' . $patrol->time . '&__ajax=1&return_url=/alley/';
                    $patrol_start = SendRequest::postRequest(
                        $patrol->character,
                        $content,
                        'application/x-www-form-urlencoded; charset=UTF-8',
                        'https://www.moswar.ru/alley/'
                    );
                } elseif (!$first_region && $second_region) {
                    $content = 'action=patrol&region=' . $patrol->getRawOriginal('second_region') . '&time=' . $patrol->time . '&__ajax=1&return_url=/alley/';
                    $patrol_start = SendRequest::postRequest(
                        $patrol->character,
                        $content,
                        'application/x-www-form-urlencoded; charset=UTF-8',
                        'https://www.moswar.ru/alley/'
                    );
                } elseif (!$first_region && !$second_region && $third_region) {
                    $content = 'action=patrol&region=' . $patrol->getRawOriginal('third_region') . '&time=' . $patrol->time . '&__ajax=1&return_url=/alley/';
                    $patrol_start = SendRequest::postRequest(
                        $patrol->character,
                        $content,
                        'application/x-www-form-urlencoded; charset=UTF-8',
                        'https://www.moswar.ru/alley/'
                    );
                }
                $patrol->last_start = Carbon::now();
                $patrol->save();
            }
        }
    }
}
