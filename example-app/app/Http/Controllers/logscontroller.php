<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\logs;
use Illuminate\Http\RedirectResponse;
class logscontroller extends Controller
{
    public function index()
    {
        $logs = logs::all();
        return view('dashboard', compact('logs'));
    }
    public function create()
    {
        return view("createpost");
    }

    public function postcreate(Request $request): RedirectResponse
    {
        $request->validate([
            'user' => ['required'],
            'date' => ['required'],
            'entry' => ['required'],
            'lunch' => ['required'],
            'left' => ['required'],
            'total' => ['required'],
            'obs' => ['required'],

        ]);

        $logs = Logs::create([
            'user_id' => $request->user,
            'data' => $request->date,
            'entrada' => $request->entry,
            'final_almoço' => $request->lunch,
            'saida' => $request->left,
            'total_horas' => $request->total,
            'obs' => $request->obs,
            'created_by' => "Alexandre Santos",
            'updated_by' => "0",
        ]);

        
        return redirect(route('dashboard', absolute: false));
    }
    public function editlog(Logs $logs){
        return view("editlog",["logs"=>$logs]);

    }
}
