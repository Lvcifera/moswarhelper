<?php

namespace App\Http\Controllers;

use App\Models\Licence;
use Carbon\Carbon;

class BotFunctionController extends Controller
{
    public function botFunctions()
    {
        $players = Licence::with('characters')
            ->where('user_id', '=', auth()->id())
            ->where('end', '>', Carbon::now())
            ->get();
        return view('modules.botFunctions', compact('players'));
    }
}
