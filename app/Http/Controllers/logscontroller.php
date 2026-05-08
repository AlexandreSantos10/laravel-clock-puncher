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
        $id = Auth::user()->id;
        $user = User::findOrFail($id);
        
        $entry = Carbon::parse($logs->entrada);
        $saida = Carbon::now()->format('H:i');
        $exit = Carbon::parse($saida);

        $inicio_almoco = Carbon::parse($user->inicio_almoco);
        $fim_almoco = $inicio_almoco->copy()->addHour(); 

        $totalMinutos = $entry->diffInMinutes($exit);

        if ($entry->lessThan($fim_almoco) && $exit->greaterThan($inicio_almoco)) {
            $inicio_sobreposicao = $entry->greaterThan($inicio_almoco) ? $entry : $inicio_almoco;
            $fim_sobreposicao = $exit->lessThan($fim_almoco) ? $exit : $fim_almoco;
            $minutosDesconto = $inicio_sobreposicao->diffInMinutes($fim_sobreposicao);
            $totalMinutos -= $minutosDesconto;
        }

        if ($totalMinutos < 0) { $totalMinutos = 0; }
        
        $horas = floor($totalMinutos / 60);
        $minutos = $totalMinutos % 60;
        $total = sprintf('%02d:%02d', $horas, $minutos);

        $data = [
            'saida' => $saida,
            "total_horas" => $total,
            "updated_by" => $user->name,
        ];

        $logs->tipo_acao_custom = 'EXIT';
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
        return view("admin/createlogview", compact('users'));
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
            $entry = Carbon::parse($data["entrada"]);
            $exit = Carbon::parse($data["saida"]);

            $inicio_almoco = Carbon::parse($user->inicio_almoco);
            $fim_almoco = $inicio_almoco->copy()->addHour(); 
            $endlunch = $fim_almoco->format('H:i'); 

            $totalMinutos = $entry->diffInMinutes($exit);

            // Lógica à prova de bala do almoço
            if ($entry->lessThan($fim_almoco) && $exit->greaterThan($inicio_almoco)) {
                $inicio_sobreposicao = $entry->greaterThan($inicio_almoco) ? $entry : $inicio_almoco;
                $fim_sobreposicao = $exit->lessThan($fim_almoco) ? $exit : $fim_almoco;
                $minutosDesconto = $inicio_sobreposicao->diffInMinutes($fim_sobreposicao);
                $totalMinutos -= $minutosDesconto;
            }

            if ($totalMinutos < 0) { $totalMinutos = 0; }
            
            $horas = floor($totalMinutos / 60);
            $minutos = $totalMinutos % 60;
            $total = sprintf('%02d:%02d', $horas, $minutos);

            $adm = Auth::user()->name;
            
            Logs::create([
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
            $entry = Carbon::parse($data["entrada"]);
            $exit = Carbon::parse($data["saida"]);

            $inicio_almoco = Carbon::parse($user->inicio_almoco);
            $fim_almoco = $inicio_almoco->copy()->addHour(); 
            $endlunch = $fim_almoco->format('H:i'); 

            $adm = Auth::user()->name;

            if ($exit->format("H:i") != "00:00") {
                $totalMinutos = $entry->diffInMinutes($exit);

                // A MAGIA ACONTECE AQUI:
                if ($entry->lessThan($fim_almoco) && $exit->greaterThan($inicio_almoco)) {
                    // Descobre a que horas começou a coincidir (o que for mais tarde: a entrada ou o inicio do almoço)
                    $inicio_sobreposicao = $entry->greaterThan($inicio_almoco) ? $entry : $inicio_almoco;
                    
                    // Descobre a que horas deixou de coincidir (o que for mais cedo: a saída ou o fim do almoço)
                    $fim_sobreposicao = $exit->lessThan($fim_almoco) ? $exit : $fim_almoco;
                    
                    // Subtrai APENAS os minutos em que os horários se cruzaram
                    $minutosDesconto = $inicio_sobreposicao->diffInMinutes($fim_sobreposicao);
                    $totalMinutos -= $minutosDesconto;
                }

                if ($totalMinutos < 0) {
                    $totalMinutos = 0;
                }

                $horas = floor($totalMinutos / 60);
                $minutos = $totalMinutos % 60;
                $total = sprintf('%02d:%02d', $horas, $minutos);
            } else {
                $total = "00:00:00";
            }
            
            $dadosPreparados = $data + [
                'total_horas' => $total,
                'final_almoço' => $endlunch,
                'updated_by' => $adm,
            ];

            if (Auth::user()->tipo === 'admin') {
                $logs->acao_personalizada = 'EDIT';
                $logs->update($dadosPreparados);
                return redirect()->route('adminlogs')->with("message", "Log atualizado com sucesso!");
            } else {
                $approval = \App\Models\LogApproval::create([
                    'log_id' => $logs->id,
                    'user_id' => Auth::user()->id,
                    'dados_novos' => $dadosPreparados,
                    'status' => 'pending'
                ]);
                $admins = User::query()->where('tipo', 'admin')->get();
                foreach ($admins as $admin) {
                    \Illuminate\Support\Facades\Mail::to($admin->email)
                        ->send(new \App\Mail\LogEditRequestMail(Auth::user(), $logs, $dadosPreparados, $approval->id));
                }
                return redirect()->route('userlogs')->with('message', 'O teu pedido de alteração foi enviado para aprovação dos administradores.');
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

            $query->where(function ($q) use ($mesComTraco, $mesComBarra) {
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

        if ($logs->user_id !== Auth::user()->id && Auth::user()->tipo !== 'admin') {
            return redirect()->back()->with('error', 'Unauthorized access');
        }

        $logs->delete();

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
        
   public function approveLog($id)
    {
        if (Auth::user()->tipo !== 'admin') {
            return redirect()->route('userlogs')->with('error', 'Não tens permissão para aprovar logs.');
        }

        $approval = \App\Models\LogApproval::findOrFail($id);

        if ($approval->status !== 'pending') {
            return redirect()->route('adminlogs')->with('error', 'Este pedido já foi processado anteriormente.');
        }

        if ($approval->created_at->copy()->addMinutes(60)->isPast()) {
            
            $approval->update(['status' => 'rejected']); 
            return redirect()->route('adminlogs')->with('error', 'Link expirado! O pedido foi feito há mais de 1 hora e foi cancelado.');
        }

        $logOriginal = \App\Models\logs::findOrFail($approval->log_id);
        
        $logOriginal->autor_personalizado = $approval->user_id;
        $logOriginal->acao_personalizada = 'APPROVED';

        $logOriginal->update($approval->dados_novos);
        $approval->update(['status' => 'approved']);

        return redirect()->route('adminlogs')->with('message', 'Alteração de log aprovada com sucesso!');
    }

   public function rejectLog($id)
    {
        if (Auth::user()->tipo !== 'admin') {
            return redirect()->route('userlogs')->with('error', 'Não tens permissão para rejeitar logs.');
        }

        $approval = \App\Models\LogApproval::findOrFail($id);

        if ($approval->status !== 'pending') {
            return redirect()->route('adminlogs')->with('error', 'Este pedido já foi processado anteriormente.');
        }

        
        if ($approval->created_at->copy()->addMinutes(60)->isPast()) {
            $approval->update(['status' => 'rejected']);
            return redirect()->route('adminlogs')->with('error', 'Link expirado! O pedido foi feito há mais de 1 hora e foi cancelado.');
        }

        $approval->update(['status' => 'rejected']);

        return redirect()->route('adminlogs')->with('message', 'Pedido de alteração rejeitado.');
    }

}
