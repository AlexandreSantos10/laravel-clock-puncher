<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class usercontroller extends Controller
{
    public function index(Request $request)
    {
        if ($request->name == "")
            $users = User::all();
        else {
            $users = User::where("name", "LIKE", "$request->name%")->get();
        }
        return view('userlist', compact('users'));
    }

    public function indexa()
    {
        $users = User::all();
        return view('createpost', compact('users'));
    }

    public function create()
    {
        return view("createuser");
    }



    public function usercreate(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', Rules\Password::defaults()],
            'type' => ['required', 'string', 'max:255'],
            'lunch' => ['required', 'max:255'],

        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'tipo' => $request->type,
            'inicio_almoco' => $request->lunch,

        ]);


        return redirect(route('userlist', absolute: false));
    }
    public function exportusers(Request $request)
    {
        $users = User::all();
        $format = $request->format;


        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Name');
        $sheet->setCellValue('C1', 'Email');
        $sheet->setCellValue('D1', 'Start Lunch');
        $sheet->setCellValue('E1', 'Type');
        $sheet->setCellValue('F1', 'Created At');

        $row = 2;
        foreach ($users as $user) {
            $sheet->setCellValue('A' . $row, $user->id);
            $sheet->setCellValue('B' . $row, $user->name);
            $sheet->setCellValue('C' . $row, $user->email);
            $sheet->setCellValue('D' . $row, $user->inicio_almoco);
            $sheet->setCellValue('E' . $row, $user->tipo);
            $sheet->setCellValue('F' . $row, $user->created_at);
            $row++;
        }
        if ($format == 'xlsx') {
            $writer = new Xlsx($spreadsheet);

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Users.xlsx"');
            header('Cache-Control: max-age=0');
        }




        if ($format == 'csv') {



            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);

            $writer->setDelimiter(';');
            $writer->setEnclosure('"');
            $writer->setLineEnding("\r\n");


            header('Content-Type: text/csv; charset=UTF-8');
            header('Content-Disposition: attachment; filename="Users.csv"');
            header('Cache-Control: max-age=0');
        }
        $writer->save('php://output');
        exit;
    }
}
