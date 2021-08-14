<?php

namespace App\Http\Controllers;

use App\Classes\Request;
use App\Http\Requests\CasinoRequest;
use App\Http\Requests\PatriotRequest;
use App\Http\Requests\PatrolRequest;
use App\Http\Requests\PotionRequest;
use App\Http\Requests\ShaurburgersRequest;
use App\Http\Requests\TaxesRequest;
use App\Models\Kubovich;
use App\Models\Character;
use App\Models\Patriot;
use App\Models\Patrol;
use App\Models\Potion;
use App\Models\Shaurburgers;
use App\Models\Taxes;
use Carbon\Carbon;
use simplehtmldom\HtmlDocument;

class BotFunctionController extends Controller
{
    public function botFunctions()
    {
        $characters = Character::where('user_id', '=', auth()->id())
            ->whereHas('licence', function ($query) {
                $query->where('end', '>', Carbon::now());
            })->get();
        $patrols = Patrol::with('character.licence')
            ->where('user_id', '=', auth()->id())
            ->get();
        $shaurburgers = Shaurburgers::with('character')
            ->where('user_id', '=', auth()->id())
            ->get();
        $taxes = Taxes::with('character')
            ->where('user_id', '=', auth()->id())
            ->get();
        $casino = Kubovich::with('character')
            ->where('user_id', '=', auth()->id())
            ->get();
        $patriots = Patriot::with('character')
            ->where('user_id', '=', auth()->id())
            ->get();
        $potions = Potion::with('character')
            ->where('user_id', '=', auth()->id())
            ->get();
        return view('modules.botFunctions', compact('characters', 'patrols', 'shaurburgers', 'taxes', 'casino', 'patriots', 'potions'));
    }

    public function patrolCreate(PatrolRequest $request)
    {
        $task = Patrol::updateOrCreate(
            [
                'character_id' => $request->get('player'),
            ],
            [
                'user_id' => auth()->id(),
                'character_id' => $request->get('player'),
                'time' => $request->get('time'),
                'first_region' => $request->get('first_region'),
                'second_region' => $request->get('second_region'),
                'third_region' => $request->get('third_region'),
            ]
        );
        if ($task->wasRecentlyCreated) {
            return redirect()->route('botFunctions')->with('success', 'Задание успешно добавлено');
        } else {
            return redirect()->route('botFunctions')->with('success', 'Задание успешно обновлено');
        }
    }

    public function patrolDelete($id)
    {
        $patrol = Patrol::with('character')
            ->find($id);
        $patrol->delete();

        return redirect()->route('botFunctions')->with('success', 'Задача патруля успешно удалена');
    }

    public function shaurburgersCreate(ShaurburgersRequest $request)
    {
        $task = Shaurburgers::updateOrCreate(
            [
                'character_id' => $request->get('player'),
            ],
            [
                'user_id' => auth()->id(),
                'character_id' => $request->get('player'),
                'time' => $request->get('time'),
            ]
        );
        if ($task->wasRecentlyCreated) {
            return redirect()->route('botFunctions')->with('success', 'Задание успешно добавлено');
        } else {
            return redirect()->route('botFunctions')->with('success', 'Задание успешно обновлено');
        }
    }

    public function shaurburgersDelete($id)
    {
        $shaurburgers = Shaurburgers::with('character')
            ->find($id);
        $shaurburgers->delete();

        return redirect()->route('botFunctions')->with('success', 'Задача шаурбургерса успешно удалена');
    }

    public function taxesCreate(TaxesRequest $request)
    {
        /**
         * получаем данные персонажа для запроса
         */
        $characterData = Character::where('user_id', '=', auth()->id())
            ->where('id', '=', $request->get('player'))
            ->first();

        /**
         * зайдем на страницу хаты и получим массив всех машин игрока
         */
        $homePage = Request::getRequest($characterData, 'https://www.moswar.ru/home/');
        $document = new HtmlDocument();
        $document->load($homePage->body());
        $carsInfo = $document->find('div[id=home-garage] div[class=object-thumb] div[class=padding] a');

        /**
         * если у игрока вообще нет машин
         * (редкость, но заглушка нужна)
         */
        if (empty($carsInfo)) {
            return redirect()->route('botFunctions')->with('danger', 'У вас нет ни одной машины');
        } elseif (!isset($carsInfo[$request->get('carNumber') - 1])) {
            return redirect()->route('botFunctions')->with('danger', 'У вас нет машины с таким номером');
        }

        /**
         * получим айди указанной машины
         */
        $carID = mb_strcut($carsInfo[$request->get('carNumber') - 1]->href, 16, 6);


        $task = Taxes::updateOrCreate(
            [
                'character_id' => $request->get('player'),
            ],
            [
                'user_id' => auth()->id(),
                'character_id' => $request->get('player'),
                'carID' => $carID,
                'car_number' => $request->get('carNumber'),
            ]
        );
        if ($task->wasRecentlyCreated) {
            return redirect()->route('botFunctions')->with('success', 'Задание успешно добавлено');
        } else {
            return redirect()->route('botFunctions')->with('success', 'Задание успешно обновлено');
        }
    }

    public function taxesDelete($id)
    {
        $taxes = Taxes::find($id);
        $taxes->delete();

        return redirect()->route('botFunctions')->with('success', 'Задача бомбления успешно удалена');
    }

    public function casinoCreate(CasinoRequest $request)
    {
        $task = Kubovich::updateOrCreate(
            [
                'character_id' => $request->get('player'),
            ],
            [
                'user_id' => auth()->id(),
                'character_id' => $request->get('player'),
                'count' => $request->get('count')
            ]
        );
        if ($task->wasRecentlyCreated) {
            return redirect()->route('botFunctions')->with('success', 'Задание успешно добавлено');
        } else {
            return redirect()->route('botFunctions')->with('success', 'Задание успешно обновлено');
        }
    }

    public function casinoDelete($id)
    {
        $casino = Kubovich::find($id);
        $casino->delete();

        return redirect()->route('botFunctions')->with('success', 'Задача бомбления успешно удалена');
    }

    public function patriotCreate(PatriotRequest $request)
    {
        /**
         * получаем данные персонажа для запроса
         */
        $characterData = Character::where('user_id', '=', auth()->id())
            ->where('id', '=', $request->get('player'))
            ->first();

        /**
         * зайдем на страницу закоулков и узнаем, есть ли на странице форма просмотра ТВ
         */
        $alleyPage = Request::getRequest($characterData, 'https://www.moswar.ru/alley/');
        $document = new HtmlDocument();
        $document->load($alleyPage->body());
        $issetPatriot = $document->find('form[id=patriottvForm]');

        /**
         * если у игрока вообще нет патриота ТВ
         */
        if (empty($issetPatriot)) {
            return redirect()->route('botFunctions')->with('danger', 'У вас нет Патриот ТВ');
        }

        $task = Patriot::updateOrCreate(
            [
                'character_id' => $request->get('player'),
            ],
            [
                'user_id' => auth()->id(),
                'character_id' => $request->get('player'),
                'time' => $request->get('time'),
                'time_start' => $request->get('time_start'),
            ]
        );
        if ($task->wasRecentlyCreated) {
            return redirect()->route('botFunctions')->with('success', 'Задание успешно добавлено');
        } else {
            return redirect()->route('botFunctions')->with('success', 'Задание успешно обновлено');
        }
    }

    public function patriotDelete($id)
    {
        $patriot = Patriot::with('character')
            ->find($id);
        $patriot->delete();

        return redirect()->route('botFunctions')->with('success', 'Задача просмотра ТВ успешно удалена');
    }

    public function potionCreate(PotionRequest $request)
    {
        $task = Potion::updateOrCreate(
            [
                'character_id' => $request->get('player'),
            ],
            [
                'user_id' => auth()->id(),
                'character_id' => $request->get('player'),
                'money_left' => $request->get('moneyLeft'),
            ]
        );
        if ($task->wasRecentlyCreated) {
            return redirect()->route('botFunctions')->with('success', 'Задание успешно добавлено');
        } else {
            return redirect()->route('botFunctions')->with('success', 'Задание успешно обновлено');
        }
    }

    public function potionDelete($id)
    {
        $potion = Potion::with('character')
            ->find($id);
        $potion->delete();

        return redirect()->route('botFunctions')->with('success', 'Задача покупки микстур успешно удалена');
    }
}
