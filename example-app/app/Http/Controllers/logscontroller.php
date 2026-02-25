<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\logs;
use \App\Models\user;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\usercontroller;
use Mehradsadeghi\FilterQueryString\FilterQueryString;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
class logscontroller extends Controller
{
    public function index()
    {
  
       // $logs = Logs::with('Users')->get();
        $logs = DB::table("users")
        ->join("logs","user_id","=","users.id")
        ->orderBy('data', 'DESC')
        //->where("name","like","%" . $request->name - "%")
        ->get()
        ->toArray();
        return view('dashboard', compact('logs'));
    }

    public function look(Logs $logs){
    $id= $logs->id;
    $logs = Logs::findOrFail($id);
   
        return view("look",["logs"=>$logs]);
    }
    public function indexuser()
    {
  
       // $logs = Logs::with('Users')->get();
        $id = Auth::user()->id;
        $logs = DB::table("users")
        ->join("logs","user_id","=","users.id")
        ->where("logs.user_id",$id)
        ->orderBy('data', 'DESC')
        ->get()
        ->toArray();
        
        return view('mylogs', compact('logs'));
    }


    public function create()
    {
        return view("createpost");
    }
    public function homepage(Request $request)
    {
        $id = Auth::user()->id;
        $users = User::findOrFail($id);
        $data = Carbon::now()->format('Y-m-d');
        $logs = Logs::all();
        $aux = 0;
        foreach($logs as $log)
        {
            if($log->data==$data)
                {
                    if($log->user_id==$id)
                        {
                            
                            $aux=1;
                            
                            if($log->saida == "00:00:00"){
                            return view("clockfinish",['logs' => $log],['users' => $users]);
                            }
                            else 
                            return view("clockfinished",['logs' => $log]);
                        }
                }
        }
        if($aux==0)
            {
                return view("home");
            }
    }
    public function logcreate(Request $request)
    {

    $id = Auth::user()->id;
    $user = User::findOrFail($id);
    $data = Carbon::now()->format('Y/m/d');
    $entrada=Carbon::now()->format('H:i');
    $endlunch = Carbon::parse($user->inicio_almoco);
    $aux = $endlunch -> hour;
    $aux = $aux + 1;
    $endlunch -> hour = $aux;


       $logs = Logs::create([
            'user_id' => $id,
            'data' => $data,
            'entrada' => $entrada,
            'final_almoço' => $endlunch,
            'saida'=>"00:00",
            "total_horas"=>"00:00",
            "obs"=>"",
            "created_by"=>$user->name,
            "updated_by"=>"",
        ]);
        $id = $logs->id;
        
       return redirect(route('clockfinish' ,['logs' => $logs],['users' => $user]));
    }
    
    public function logup(Logs $logs)
    {
        
        return view("clockfinish", compact('logs'));
    }
    public function logupdate(Logs $logs)
    {
    
    $entrada = Carbon::parse($logs->entrada);
    $saida=Carbon::now()->format('H:i');
    $sai = Carbon::parse($saida);
    $total = $entrada->diff($sai)->format('%H:%i');
    

       $data =([
            'saida'=>$saida,         
            "total_horas"=>$total,
            "obs"=>"",
            "created_by"=>"",
            "updated_by"=>"",
        ]);
        

        $logs->update($data);
        return view("clockfinished",['logs' => $logs]);
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
        $adm = Auth::user()->name;
        $logs = Logs::create([
            'user_id' => $request->user_id,
            'data' => $request->data,
            'entrada' => $request->entrada,
            'final_almoço' => $endlunch,
            'saida' => $request->saida,
            'total_horas' => $total,
            'obs' => $request->obs,
            'created_by' => $adm,
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
        $adm = Auth::user()->name;
        $exitstr = $exit->format("H:i");
        if($exitstr != "00:00"){
        $aux = $exit -> hour;
        $aux = $aux - 1;
        $exit -> hour = $aux;

        $total = $entry->diff($exit)->format('%H:%i');
        }
        else
        $total="00:00:00";
        $data = $data + ([
            'total_horas' => $total,
            'final_almoço' => $endlunch,
            'updated_by' => $adm,
        ]);
        $logs->update($data);
        return redirect(route('dashboard'));
    
    }
    
     public function delete(Logs $logs)
    {
        $logs->DELETE();
        return redirect()->route("dashboard")->with("message","The log has been sucessfully removed");
    }

}
