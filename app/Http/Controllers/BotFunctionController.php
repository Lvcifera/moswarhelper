<?php

namespace App\Http\Controllers;

use App\Classes\SendRequest;
use App\Http\Requests\CasinoRequest;
use App\Http\Requests\PatrolRequest;
use App\Http\Requests\ShaurburgersRequest;
use App\Http\Requests\TaxesRequest;
use App\Models\Kubovich;
use App\Models\Character;
use App\Models\Licence;
use App\Models\Patrol;
use App\Models\Shaurburgers;
use App\Models\Taxes;
use Carbon\Carbon;
use simplehtmldom\HtmlDocument;

class BotFunctionController extends Controller
{
    public function botFunctions()
    {
        $players = Licence::with('characters')
            ->where('user_id', '=', auth()->id())
            ->where('end', '>', Carbon::now())
            ->get();
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
        return view('modules.botFunctions', compact('players', 'patrols', 'shaurburgers', 'taxes', 'casino'));
    }

    public function patrolCreate(PatrolRequest $request)
    {
        $task = Patrol::where('user_id', '=', auth()->id())
            ->where('character_id', '=', $request->player)
            ->first();

        if ($task == null) {
            $task = new Patrol();
            $task->user_id = auth()->id();
            $task->character_id = $request->player;
            $task->region = $request->region;
            $task->time = $request->time;
            $task->save();

            return redirect()->route('botFunctions')->with('success', 'Задание успешно добавлено');
        } else {
            $task = Patrol::where('user_id', '=', auth()->id())
                ->where('character_id', '=', $request->player)
                ->first();
            $task->user_id = auth()->id();
            $task->character_id = $request->player;
            $task->region = $request->region;
            $task->time = $request->time;
            $task->update();

            return redirect()->route('botFunctions')->with('success', 'Задание успешно обновлено');
        }

    }

    public function patrolDelete($id)
    {
        $patrol = Patrol::find($id);
        $patrol->delete();

        return redirect()->route('botFunctions')->with('success', 'Задача патруля успешно удалена');
    }

    public function shaurburgersCreate(ShaurburgersRequest $request)
    {
        $task = Shaurburgers::where('user_id', '=', auth()->id())
            ->where('character_id', '=', $request->player)
            ->first();

        if ($task == null) {
            $task = new Shaurburgers();
            $task->user_id = auth()->id();
            $task->character_id = $request->player;
            $task->time = $request->time;
            $task->save();

            return redirect()->route('botFunctions')->with('success', 'Задание успешно добавлено');
        } else {
            $task = Shaurburgers::where('user_id', '=', auth()->id())
                ->where('character_id', '=', $request->player)
                ->first();
            $task->user_id = auth()->id();
            $task->character_id = $request->player;
            $task->time = $request->time;
            $task->update();

            return redirect()->route('botFunctions')->with('success', 'Задание успешно обновлено');
        }
    }

    public function shaurburgersDelete($id)
    {
        $shaurburgers = Shaurburgers::find($id);
        $shaurburgers->delete();

        return redirect()->route('botFunctions')->with('success', 'Задача шаурбургерса успешно удалена');
    }

    public function taxesCreate(TaxesRequest $request)
    {
        $task = Taxes::where('user_id', '=', auth()->id())
            ->where('character_id', '=', $request->player)
            ->first();

        /**
         * получаем данные персонажа для запроса
         */
        $playerData = Character::where('user_id', '=', auth()->id())
            ->where('id', '=', $request->player)
            ->first();

        /**
         * зайдем на страницу хаты и получим массив всех машин игрока
         */
        $playerPage = SendRequest::getRequest($playerData, 'https://www.moswar.ru/home/');
        $document = new HtmlDocument();
        $document->load($playerPage->body());
        $carsInfo = $document->find('div[id=home-garage] div[class=object-thumb] div[class=padding] a');

        /**
         * если у игрока вообще нет машин
         * (редкость, но заглушка нужна)
         */
        if (empty($carsInfo)) {
            return redirect()->route('botFunctions')->with('danger', 'У вас нет ни одной машины');
        }

        /**
         * получим айди указанной машины
         */
        $carID = mb_strcut($carsInfo[$request->carNumber - 1]->href, 16, 6);

        if ($task == null) {
            $task = new Taxes();
            $task->user_id = auth()->id();
            $task->character_id = $request->player;
            $task->carID = $carID;
            $task->car_number = $request->carNumber;
            $task->save();

            return redirect()->route('botFunctions')->with('success', 'Задание успешно добавлено');
        } else {
            $task = Taxes::where('user_id', '=', auth()->id())
                ->where('character_id', '=', $request->player)
                ->first();
            $task->user_id = auth()->id();
            $task->character_id = $request->player;
            $task->carID = $carID;
            $task->car_number = $request->carNumber;
            $task->update();

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
        $task = Kubovich::where('user_id', '=', auth()->id())
            ->where('character_id', '=', $request->player)
            ->first();

        if ($task == null) {
            $task = new Kubovich();
            $task->user_id = auth()->id();
            $task->character_id = $request->player;
            $task->count = $request->count;
            $task->today_active = 1;
            $task->save();

            return redirect()->route('botFunctions')->with('success', 'Задание успешно добавлено');
        } else {
            $task = Kubovich::where('user_id', '=', auth()->id())
                ->where('character_id', '=', $request->player)
                ->first();
            $task->user_id = auth()->id();
            $task->character_id = $request->player;
            $task->count = $request->count;
            $task->today_active = 1;
            $task->update();

            return redirect()->route('botFunctions')->with('success', 'Задание успешно обновлено');
        }
    }

    public function casinoDelete($id)
    {
        $casino = Kubovich::find($id);
        $casino->delete();

        return redirect()->route('botFunctions')->with('success', 'Задача бомбления успешно удалена');
    }
}
