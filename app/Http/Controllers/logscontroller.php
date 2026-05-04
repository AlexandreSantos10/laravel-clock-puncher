<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\logs;
use \App\Models\User;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use App\Models\AdminLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;

class logscontroller extends Controller
{

    public function userlogs(Request $request)
    {

        $id = Auth::user()->id;
        $logs = Logs::with('User')->where('user_id', $id);

        if ($request->month != "") {
            $date = $request->month;
            $logs->where('data', 'like', "$date%");
        }
        if ($request->time != "") {
            $date = $request->time;
            $logs->whereDay('data', "$date%");
        }
        $logs = $logs->orderBy('data', 'DESC')->paginate(10);

        return view('user/logs', compact('logs'));
    }
    public function homepage(Request $request)
    {
        $id = Auth::user()->id;
        $users = User::findOrFail($id);
        $data = Carbon::now()->format('Y-m-d');
        $logs = Logs::all();
        $aux = 0;
        foreach ($logs as $log) {
            if ($log->data == $data) {
                if ($log->user_id == $id) {

                    $aux = 1;

                    if ($log->saida == "00:00:00") {
                        return view("user/clockfinish", ['logs' => $log], ['users' => $users]);
                    } else
                        return view("user/clockfinished", ['logs' => $log]);
                }
            }
        }
        if ($aux == 0) {
            return view("user/home");
        }
    }

    public function userlogcreate(Request $request)
    {

        $id = Auth::user()->id;
        $user = User::findOrFail($id);
        $data = Carbon::now()->format('Y/m/d');
        $entrada = Carbon::now()->format('H:i');
        $endlunch = Carbon::parse($user->inicio_almoco);
        $aux = $endlunch->hour;
        $aux = $aux + 1;
        $endlunch->hour = $aux;


        $logs = Logs::create([
            'user_id' => $id,
            'data' => $data,
            'entrada' => $entrada,
            'final_almoço' => $endlunch,
            'saida' => "00:00",
            "total_horas" => "00:00",
            "obs" => "Manual Log",
            "created_by" => $user->name,
            "updated_by" => "Not Updated",
        ]);
        $id = $logs->id;

        return redirect(route('clockfinish', ['logs' => $logs], ['users' => $user]));
    }
    public function userlogup(Logs $logs)
    {
        return view("user/clockfinish", compact('logs'));
    }
    public function userlogupdate(Logs $logs)
    {
        $entrada = Carbon::parse($logs->entrada);
        $saida = Carbon::now()->format('H:i');
        $sai = Carbon::parse($saida);
        $total = $entrada->diff($sai)->format('%H:%i');

        $id = Auth::user()->id;
        $user = User::findOrFail($id);
        $name = $user->name;
        $data = ([
            'saida' => $saida,
            "total_horas" => $total,
            "updated_by" => $name,
        ]);


        $logs->update($data);
        return view("user/clockfinished", ['logs' => $logs]);
    }

    public function adminlogs(Request $request)
    {
        $users = User::all();
        $logs = Logs::with('User');
        if ($request->name != "") {
            $logs->whereHas('user', function ($query) use ($request) {
                $query->where('name', $request->name);
            });
        }
        if ($request->month != "") {
            $date = $request->month;
            $logs->where('data', 'like', "$date%");
        }
        if ($request->time != "") {
            $date = $request->time;
            $logs->whereDay('data', "$date%");
        }

        $logs = $logs->orderBy('data', 'DESC')->orderBy('entrada', 'DESC')->paginate(10);

        return view('admin/logs', compact('logs', 'users'));
    }
    public function createlogview()
    {
        $users = User::all();
        return view("admin/createlogview",compact('users'));
    }
     public function createlog(Request $request)
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
        $users = User::all();
        $logss = Logs::with('User')->where("user_id", $id)->get();
        $datevalid = 0; 
        
    
        foreach ($logss as $log) {
            if ($log->data == $data["data"]) {
                $datevalid = $datevalid + 1;
            }
        }



        if ($datevalid == 0) {
            $endlunch = Carbon::parse($user->inicio_almoco);
            $aux = $endlunch->hour;
            $aux = $aux + 1;
            $endlunch->hour = $aux;
            $entry = Carbon::parse($data["entrada"]);
            $exit = Carbon::parse($data["saida"]);
            $aux = $exit->hour;
            $aux = $aux - 1;
            $exit->hour = $aux;
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
                'updated_by' => "Not Updated",
            ]);



            return redirect(route('adminlogs', absolute: false));
        } else {
            return view("admin/createlogview", ["users" => $users])->with("message", "This user was already registered on this day");
        }
    }

    public function looklog($logs)
    {
        $logs = \App\Models\logs::findOrFail($logs);
        
        if ($logs->user_id !== Auth::user()->id && Auth::user()->tipo !== 'admin') {
            return redirect()->back()->with('error', 'Unauthorized access');
        }

        // Devolve a vista consoante o link em que estavas
        if (request()->is('admin/*')) {
            return view("admin/looklog", ["logs" => $logs]);
        } else {
            return view("user/looklog", ["logs" => $logs]);
        }
    }
    
    public function editlog($logs)
    {
        $logs = \App\Models\logs::findOrFail($logs);

        if ($logs->user_id !== Auth::user()->id && Auth::user()->tipo !== 'admin') {
            return redirect()->back()->with('error', 'Unauthorized access');
        }
        
        $users = \App\Models\User::all();
        
        
        if (request()->is('admin/*')) {
            return view("admin/editlog", ["logs" => $logs, "users" => $users]);
        } else {
            return view("user/editlog", ["logs" => $logs, "users" => $users]);
        }
    }
    public function updatelog(Logs $logs, Request $request)
    {
        // Verificar se o utilizador é o proprietário do log ou é admin
        if ($logs->user_id !== Auth::user()->id && Auth::user()->tipo !== 'admin') {
            return redirect()->back()->with('error', 'Unauthorized access');
        }
        
        $data = $request->validate([
            'data' => ['required'],
            'obs' => ['required'],
            'saida' => ['required'],
            'entrada' => ['required'],
        ]);

        $id = $request->user_id;
        $user = User::findOrFail($id);
        $logss = Logs::with('User')->where("user_id", $id)->get();
        $datevalid = 0;

        foreach ($logss as $log) {
            if ($log->data == $data["data"]) {
                $datevalid = $datevalid + 1;
            }
        }

        if ($logs->data == $data["data"]) {
            $datevalid = 0;
        }



        if ($datevalid == 0) {


            $endlunch = Carbon::parse($user->inicio_almoco);
            $aux = $endlunch->hour;
            $aux = $aux + 1;
            $endlunch->hour = $aux;
            $entry = Carbon::parse($data["entrada"]);
            $exit = Carbon::parse($data["saida"]);
            $adm = Auth::user()->name;
            $exitstr = $exit->format("H:i");
            if ($exitstr != "00:00") {
                $aux = $exit->hour;
                $aux = $aux - 1;
                $exit->hour = $aux;

                $total = $entry->diff($exit)->format('%H:%i');
            } else
                $total = "00:00:00";
            $data = $data + ([
                'total_horas' => $total,
                'final_almoço' => $endlunch,
                'updated_by' => $adm,
            ]);
            $logs->update($data);
            
            if (request()->is('admin/*')) {
                return redirect()->route('adminlogs');
            } else {
                return redirect()->route('userlogs');
            }
            
        } else {
            
            if (request()->is('admin/*')) {
                return view('admin/editlog', ["logs" => $logs, "users" => $user])->with("message", "This user was already registered on this day");
            } else {
                return view('user/editlog', ["logs" => $logs, "users" => $user])->with("message", "This user was already registered on this day");
            }
            
        }
    }


public function adminLogsAudit(Request $request)
    {
        $users = \App\Models\User::all();
        $query = \App\Models\AdminLog::with('autor');

        if ($request->filled('name')) {
            $targetUser = \App\Models\User::query()->where('name', $request->name)->first();
            
            if ($targetUser) {
                $query->where('dados_antigos->user_id', $targetUser->id);
            }
        }

        if ($request->filled('month')) {
            
            $mesComTraco = $request->month; 
            
            $mesComBarra = str_replace('-', '/', $request->month); 

            $query->where(function($q) use ($mesComTraco, $mesComBarra) {
                $q->where('dados_antigos->data', 'like', $mesComTraco . '%')
                  ->orWhere('dados_antigos->data', 'like', $mesComBarra . '%');
            });
        }

        
        $admin_logs = $query->orderBy('created_at', 'desc')->paginate(10);
        
        return view('admin/admin_logs', compact('admin_logs', 'users'));
    }
    public function deletelog($logs)
    {
        $logs = \App\Models\logs::findOrFail($logs);
        
        // Segurança: Bloqueia se não for admin E não for dono do log
        if ($logs->user_id !== Auth::user()->id && Auth::user()->tipo !== 'admin') {
            return redirect()->back()->with('error', 'Unauthorized access');
        }
        
        $logs->delete();
        
        // A MÁGICA AQUI: O sistema verifica o link em que estás
        if (request()->is('admin/*')) {
            return redirect()->route("adminlogs")->with("message", "The log has been sucessfully removed");
        } else {
            return redirect()->route("userlogs")->with("message", "The log has been sucessfully removed");
        }
    }

    public function export(Request $request)
    {
        $logs = Logs::with('User');

        if ($request->name != "") {
            $logs->whereHas('user', function ($query) use ($request) {
                $query->where('name', $request->name);
            });
        }
        if ($request->month != "") {
            $logs->where('data', 'like', $request->month . '%');
        }
        if ($request->time != "") {
            $logs->whereDay('data', $request->time);
        }
        $logs = $logs->orderBy('data', 'DESC')->get();
        $format = $request->format;


        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'User');
        $sheet->setCellValue('B1', 'Date');
        $sheet->setCellValue('C1', 'Entry');
        $sheet->setCellValue('D1', 'Exit');
        $sheet->setCellValue('E1', 'Total Hours');
        $sheet->setCellValue('F1', 'Obs');

        $row = 2;
        foreach ($logs as $log) {
            $sheet->setCellValue('A' . $row, $log->user->name);
            $sheet->setCellValue('B' . $row, $log->data);
            $sheet->setCellValue('C' . $row, $log->entrada);
            $sheet->setCellValue('D' . $row, $log->saida);
            $sheet->setCellValue('E' . $row, $log->total_horas);
            $sheet->setCellValue('F' . $row, $log->obs);
            $row++;
        }
        if ($format == 'xlsx') {
            $writer = new Xlsx($spreadsheet);

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="logs.xlsx"');
            header('Cache-Control: max-age=0');
        }
        if ($format == 'csv') {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);

            $writer->setDelimiter(';');
            $writer->setEnclosure('"');
            $writer->setLineEnding("\r\n");


            header('Content-Type: text/csv; charset=UTF-8');
            header('Content-Disposition: attachment; filename="logs.csv"');
            header('Cache-Control: max-age=0');
        }
        $writer->save('php://output');
        exit;
    }

    public function exportuserlog(Request $request)
    {
        $id = Auth::user()->id;
        $logs = Logs::with('User')->where('user_id', $id);

        if ($request->name != "") {
            $logs->whereHas('user', function ($query) use ($request) {
                $query->where('name', $request->name);
            });
        }
        if ($request->month != "") {
            $logs->where('data', 'like', $request->month . '%');
        }
        if ($request->time != "") {
            $logs->whereDay('data', $request->time);
        }
        $logs = $logs->orderBy('data', 'DESC')->get();
        $format = $request->format;


        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'User');
        $sheet->setCellValue('B1', 'Date');
        $sheet->setCellValue('C1', 'Entry');
        $sheet->setCellValue('D1', 'Exit');
        $sheet->setCellValue('E1', 'Total Hours');
        $sheet->setCellValue('F1', 'Obs');

        $row = 2;
        foreach ($logs as $log) {
            $sheet->setCellValue('A' . $row, $log->user->name);
            $sheet->setCellValue('B' . $row, $log->data);
            $sheet->setCellValue('C' . $row, $log->entrada);
            $sheet->setCellValue('D' . $row, $log->saida);
            $sheet->setCellValue('E' . $row, $log->total_horas);
            $sheet->setCellValue('F' . $row, $log->obs);
            $row++;
        }
        if ($format == 'xlsx') {
            $writer = new Xlsx($spreadsheet);

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Mylogs.xlsx"');
            header('Cache-Control: max-age=0');
        }
        if ($format == 'csv') {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);

            $writer->setDelimiter(';');
            $writer->setEnclosure('"');
            $writer->setLineEnding("\r\n");


            header('Content-Type: text/csv; charset=UTF-8');
            header('Content-Disposition: attachment; filename="Mylogs.csv"');
            header('Cache-Control: max-age=0');
        }
        $writer->save('php://output');
        exit;
    }
    /*
    public function receberPontoDoEsp32(Request $request)
    {
       
        $userId = $request->input('user_id');

        if ($userId === null) {
            return response()->json(['erro' => 'UserID não recebido'], 400);
        }

        $user = \App\Models\User::find($userId);

        if (!$user) {
            return response()->json(['erro' => 'Utilizador não existe na base de dados'], 404);
        }

        $dataAtual = Carbon::now()->format('Y/m/d');
        $horaAtual = Carbon::now()->format('H:i');

        $logDeHoje = \App\Models\logs::where('user_id', $user->id)
                                     ->where('data', $dataAtual)
                                     ->first();

        if (!$logDeHoje) {
           
            $endlunch = Carbon::parse($user->inicio_almoco);
            $aux = $endlunch->hour + 1;
            $endlunch->hour = $aux;

            \App\Models\logs::create([
                'user_id' => $user->id,
                'data' => $dataAtual,
                'entrada' => $horaAtual,
                'final_almoço' => $endlunch,
                'saida' => "00:00",
                "total_horas" => "00:00",
                "obs" => "Biometria (ESP32)",
                "created_by" => $user->name,
                "updated_by" => "Not Updated",
            ]);

            return response()->json(['sucesso' => true, 'mensagem' => 'Entrada de ' . $user->name . ' registada!'], 200);

        } else {
            
            $entrada = Carbon::parse($logDeHoje->entrada);
            $sai = Carbon::parse($horaAtual);
            
            $total = $entrada->diff($sai)->format('%H:%i');

            $logDeHoje->update([
                'saida' => $horaAtual,
                'total_horas' => $total,
                'updated_by' => $user->name,
            ]);

            return response()->json(['sucesso' => true, 'mensagem' => 'Saída de ' . $user->name . ' registada!'], 200);
        }
    }*/
}
