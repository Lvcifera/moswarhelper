<?php

namespace App\Http\Controllers;

use App\Classes\SendRequest;
use App\Http\Requests\GiftsRequest;
use App\Http\Requests\GypsyRequest;
use App\Http\Requests\MoscowpolyRequest;
use App\Http\Requests\PetriksRequest;
use App\Http\Requests\TeethRequest;
use App\Models\Licence;
use App\Models\Character;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class ModuleController extends Controller
{
    public function teeth()
    {
        $players = Licence::with('characters')
            ->where('user_id', '=', auth()->id())
            ->where('end', '>', Carbon::now())
            ->get();
        return view('modules.teeth', compact('players'));
    }

    public function teethWork(TeethRequest $request)
    {
        $playerData = Character::where('player', '=', $request->player)->first();

        $start_time = new Carbon();
        $count = 0;
        $content = 'key=' . $playerData->param . '&' .
            'action=buy&' . 'item=6603&amount=&return_url=%2Fberezka%2Fsection%2Fmixed%2F&' .
            'type=&ajax_ext=2&autochange_honey=0';
        while ($count < $request->teethCount) {
            /**
             * покупаем зубной ящик
             */
            $buy = SendRequest::postRequest(
                $playerData,
                $content,
                'application/x-www-form-urlencoded; charset=UTF-8',
                'https://www.moswar.ru/shop/json/'
            );

            /**
             * получаем ID купленного зубного ящика
             */
            $getBoxID = SendRequest::getRequest(
                $playerData,
                'https://www.moswar.ru/player'
            );
            $boxesData = explode('id="inventory-box_teeth-btn" data-action="use" data-id="', $getBoxID->body());
            $boxID = mb_strcut(array_pop($boxesData), 0, 10);

            /**
             * открываем купленный зубной ящик,
             * используя его уникальный ID
             */
            $getBoxID = SendRequest::getRequest(
                $playerData,
                'https://www.moswar.ru/player/json/use/' . $boxID . '/'
            );
            if ($buy->json('result') == 1) {
                $count++;
            }
            if ($buy->json('result') == 0) {
                $end_time = new Carbon();
                $time = $end_time->diffInSeconds($start_time);
                return redirect()->route('teeth')->with('success', 'Действие выполнено частично, закончились зубы,
                 куплено ' . $count . ' зубных ящиков. Затраченное время ' . gmdate('H:i:s', $time) . ' секунд');
                break;
            }
        }
        $end_time = new Carbon();
        $time = $end_time->diffInSeconds($start_time);

        return redirect()->route('teeth')->with('success', 'Действие успешно выполнено, затраченное время ' . gmdate('H:i:s', $time));
    }

    public function moscowpoly()
    {
        $players = Licence::with('characters')
            ->where('user_id', '=', auth()->id())
            ->where('end', '>', Carbon::now())
            ->get();
        return view('modules.moscowpoly', compact('players'));
    }

    public function moscowpolyWork(MoscowpolyRequest $request)
    {
        $playerData = Character::where('player', '=', $request->player)->first();

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
                return redirect()->route('moscowpoly')->with('danger', 'У вас закончились кубики.
                Брошено ' . $count . '. Затраченное время ' . $time . ' секунд');
                break;
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
        $players = Licence::with('characters')
            ->where('user_id', '=', auth()->id())
            ->where('end', '>', Carbon::now())
            ->get();
        return view('modules.gypsy', compact('players'));
    }

    public function gypsyWork(GypsyRequest $request)
    {
        $playerData = Character::where('player', '=', $request->player)->first();

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
        $players = Licence::with('characters')
            ->where('user_id', '=', auth()->id())
            ->where('end', '>', Carbon::now())
            ->get();
        return view('modules.petriks', compact('players'));
    }

    public function petriksWork(PetriksRequest $request)
    {
        $playerData = Character::where('player', '=', $request->player)->first();

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

    public function gifts()
    {
        $players = Licence::with('characters')
            ->where('user_id', '=', auth()->id())
            ->where('end', '>', Carbon::now())
            ->get();
        return view('modules.gifts', compact('players'));
    }

    public function giftsWork(GiftsRequest $request)
    {
        $playerData = Character::where('player', '=', $request->player)->first();

        $start_time = new Carbon();
        /**
         * проверяем, существует ли персонаж с
         * указанным именем
         */
        $checkPlayerExist = Http::withCookies(
            [
                'PHPSESSID' => $playerData->PHPSESSID,
                'authkey' => $playerData->authkey,
                'userid' => $playerData->userid,
                'player' => urlencode($playerData->player),
                'player_id' => $playerData->player_id,
            ], 'moswar.ru')->get('https://www.moswar.ru/shop/playerexists/' . $request->reciever . '/');
        if ($checkPlayerExist->json() == 0) {
            return redirect()->route('gifts')->with('danger', 'Игрока с таким именем не существует');
        }

        $count = 0;
        while ($count < $request->giftCount) {
            /**
             * дарим подарок
             */
            $content = 'action=buy&return_url=%2Fshop%2Fsection%2Fgifts%2F%23negative&item=' .
                $request->gift . '&playerid=&key=' . $playerData->param . '&player=' .
                $request->reciever . '&comment=' . $request->comment . '&';
            if ($request->private != null) {
                $content .= 'private=on&';
            }
            if ($request->anonimous != null) {
                $content .= 'anonimous=on&__ajax=1';
            }
            $content .= '&__ajax=1';

            $gift = Http::withBody($content,'application/x-www-form-urlencoded; charset=UTF-8')
                ->withCookies(
                    [
                        'PHPSESSID' => $playerData->PHPSESSID,
                        'authkey' => $playerData->authkey,
                        'userid' => $playerData->userid,
                        'player' => urlencode($playerData->player),
                        'player_id' => $playerData->player_id,
                    ], 'moswar.ru')->post('https://www.moswar.ru/shop/');
            $count++;
        }
        $end_time = new Carbon();
        $time = $end_time->diffInSeconds($start_time);

        return redirect()->route('gifts')->with('success', 'Действие успешно выполнено, затраченное время ' . gmdate('H:i:s', $time));
    }
}
