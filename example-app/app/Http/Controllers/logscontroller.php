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
use Illuminate\Support\Facades\Auth;
class logscontroller extends Controller
{
    public function index(Request $request)
{
    $users = User::all();
    $logs = Logs::with('User');
    if ($request->name != "") {
        $logs->whereHas('user', function ($query) use ($request) {
            $query->where('name', $request->name);
        });
    }
    if ($request->filter != "") {

        switch ($request->filter) {

            case "today":
                $today = Carbon::now()->format('Y-m-d');
                $logs->where('data', 'like', "$today%");
            break;

            case "week":
                $logs->whereBetween('data', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()
                ]);
            break;

            case "month":
                $logs->whereBetween('data', [
                    Carbon::now()->startOfMonth(),
                    Carbon::now()
                ]);
            break;

            case "all":
            default:
            break;
        }
    }
    if ($request->time != "") {
        $date = $request->time;
        $logs->where('data', 'like', "$date%");
    }

    $logs = $logs->orderBy('data', 'DESC')->get();

    return view('dashboard', compact('logs', 'users'));
}

    public function look(Logs $logs){
    $id= $logs->id;
    $logs = Logs::findOrFail($id);
   
        return view("look",["logs"=>$logs]);
    }
    public function indexuser()
    {
  
        $id = Auth::user()->id;
        $logs = Logs::with('User')->where('user_id',$id)->orderBy('data', 'DESC')->get();
        
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
        $users= User::all();
        $logss = Logs::with('User')->where("user_id",$id)->get();
        $datevalid=0;
        
        foreach($logss as $log)
            {
                if($log->data==$data["data"])
                    {
                        $datevalid=$datevalid + 1;
                    }
            }
            
        
        
        if($datevalid==0){
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
        else 
            {
                return view("createpost",["users"=>$users])->with("message","This user was already registered on this day");
            }
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
        $user=User::findOrFail($id);
        $logss = Logs::with('User')->where("user_id",$id)->get();
        $datevalid=0;
        
        foreach($logss as $log)
            {
                if($log->data==$data["data"])
                    {
                        $datevalid=$datevalid + 1;
                        
                    }
            }
            
                if($logs->data==$data["data"])
                    {
                        $datevalid=0;
                        
                    }
            
        
        
        if($datevalid==0){

        
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

        else
            {
                return view('editlog',["logs"=>$log],["users"=>$user])->with("message","This user was already registered on this day");
            
}
    }

    
    
     public function delete(Logs $logs)
    {
        $logs->DELETE();
        return redirect()->route("dashboard")->with("message","The log has been sucessfully removed");
    }

}
