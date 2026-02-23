<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\logs;
use \App\Models\user;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\usercontroller;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
class logscontroller extends Controller
{
    public function index()
    {
  
       // $logs = Logs::with('Users')->get();
        $logs = DB::table("users")
        ->join("logs","user_id","=","users.id")
        ->orderBy('data', 'DESC')
        ->get()
        ->toArray();
        
        return view('dashboard', compact('logs'));
    }
    public function create()
    {
        return view("createpost");
    }

    public function postcreate(Request $request)
    {
        
        $data = $request->validate([
            'user_id' => ['required'],
            'data' => ['required'],
            'entrada' => ['required'],
            'saida' => ['required'],
            'obs' => ['required'],

        ]); 
        $id = $request->user_id;
        $user = User::findOrFail($id);

        $endlunch = Carbon::parse($user->inicio_almoco);
        $aux = $endlunch -> hour;
        $aux = $aux + 1;
        $endlunch -> hour = $aux;
        $entry = Carbon::parse($data["entrada"]);
        $exit = Carbon::parse($data["saida"]);
        $aux = $exit -> hour;
        $aux = $aux - 1;
        $exit -> hour = $aux;
        $total = $entry->diff($exit)->format('%H:%i');
   
        $logs = Logs::create([
            'user_id' => $request->user_id,
            'data' => $request->data,
            'entrada' => $request->entrada,
            'final_almoço' => $endlunch,
            'saida' => $request->saida,
            'total_horas' => $total,
            'obs' => $request->obs,
            'created_by' => "Alexandre Santos",
            'updated_by' => "0",
        ]);
        

        
       return redirect(route('dashboard', absolute: false));
    }
    public function editlog(Logs $logs){
    $users = User::all();
        return view("editlog",["logs"=>$logs],["users"=>$users]);

    }
    public function update (Logs $logs, Request $request)
    {
        $data = $request->validate([
            'data' => ['required'],
            'obs' => ['required'],
            'saida' => ['required'],
            'entrada' => ['required'],
        ]);

        $id = $request->user_id;
        $user = User::findOrFail($id);

        $endlunch = Carbon::parse($user->inicio_almoco);
        $aux = $endlunch -> hour;
        $aux = $aux + 1;
        $endlunch -> hour = $aux;
        $entry = Carbon::parse($data["entrada"]);
        $exit = Carbon::parse($data["saida"]);
        $aux = $exit -> hour;
        $aux = $aux - 1;
        $exit -> hour = $aux;
        $total = $entry->diff($exit)->format('%H:%i');
        $data = $data + ([
            'total_horas' => $total,
            'final_almoço' => $endlunch,
        ]);
        $logs->update($data);
        return redirect(route('dashboard'));
    
    }
}
