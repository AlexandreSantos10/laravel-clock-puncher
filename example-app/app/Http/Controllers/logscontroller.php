<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\logs;
use \App\Models\user;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

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
            "obs" => "",
            "created_by" => $user->name,
            "updated_by" => "",
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


        $data = ([
            'saida' => $saida,
            "total_horas" => $total,
            "obs" => "",
            "created_by" => "",
            "updated_by" => "",
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

        $logs = $logs->orderBy('data', 'DESC')->paginate(10);

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
                'updated_by' => "0",
            ]);



            return redirect(route('adminlogs', absolute: false));
        } else {
            return view("admin/createlogview", ["users" => $users])->with("message", "This user was already registered on this day");
        }
    }

    public function looklog(Logs $logs)
    {
        $id = $logs->id;
        $logs = Logs::findOrFail($id);

        return view("admin/looklog", ["logs" => $logs]);
    }
    
    public function editlog(Logs $logs)
    {
        $users = User::all();
        return view("admin/editlog", ["logs" => $logs], ["users" => $users]);
    }
    public function updatelog(Logs $logs, Request $request)
    {
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
            return redirect(route('adminlogs'));
        } else {
            return view('admin/editlog', ["logs" => $log], ["users" => $user])->with("message", "This user was already registered on this day");
        }
    }



    public function deletelog(Logs $logs)
    {
        $logs->DELETE();
        return redirect()->route("adminlogs")->with("message", "The log has been sucessfully removed");
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
}
