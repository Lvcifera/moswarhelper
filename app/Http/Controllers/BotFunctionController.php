<?php

namespace App\Http\Controllers;

use App\Http\Requests\PatrolRequest;
use App\Http\Requests\ShaurburgersRequest;
use App\Models\Licence;
use App\Models\Patrol;
use App\Models\Shaurburgers;
use Carbon\Carbon;

class BotFunctionController extends Controller
{
    public function botFunctions()
    {
        $players = Licence::with('characters')
            ->where('user_id', '=', auth()->id())
            ->where('end', '>', Carbon::now())
            ->get();
        $patrols = Patrol::with('character')
            ->where('user_id', '=', auth()->id())
            ->get();
        $shaurburgers = Shaurburgers::with('character')
            ->where('user_id', '=', auth()->id())
            ->get();
        return view('modules.botFunctions', compact('players', 'patrols', 'shaurburgers'));
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
}
