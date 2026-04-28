<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Cache;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

class usercontroller extends Controller
{
    public function userlist(Request $request)
    {
        if ($request->name == "") {
            $users = User::paginate(10);
        } else {
            $users = User::where("name", "LIKE", "$request->name%")->get();
        }
        return view('admin/users', compact('users'));
    }

    public function exportusers(Request $request)
    {
        $users = User::all();
        $format = $request->format;

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Name');
        $sheet->setCellValue('B1', 'Email');
        $sheet->setCellValue('C1', 'Start Lunch');
        $sheet->setCellValue('D1', 'Type');
        $sheet->setCellValue('E1', 'Created At');

        $row = 2;
        foreach ($users as $user) {
            $sheet->setCellValue('A' . $row, $user->name);
            $sheet->setCellValue('B' . $row, $user->email);
            $sheet->setCellValue('C' . $row, $user->inicio_almoco);
            $sheet->setCellValue('D' . $row, $user->tipo);
            $sheet->setCellValue('E' . $row, $user->created_at);
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


    public function createuserview()
    {
        return view("admin/createuserview");
    }

    public function createuser(Request $request): RedirectResponse
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
    public function changeusertype(User $user)
    {
        if ($user->tipo == "user") {
            $user->update(["tipo" => "admin"]);
        } else {
            $user->update(["tipo" => "user"]);
        }

        return redirect()->back();
    }

    public function enroll($id)
    {
        
        set_time_limit(90);

        $user = User::findOrFail($id);
        $statusKey = "enroll_status_{$id}";
        $activeKey = "active_enrollment_id";

        Cache::forget($statusKey);
        Cache::put($activeKey, $id, 120);

        try {
     
            $settings = (new ConnectionSettings)
                ->setUseTls(true)
                ->setTlsVerifyPeer(false)
                ->setUsername(config('mqtt.username'))
                ->setPassword(config('mqtt.password'));

            $mqtt = new MqttClient(config('mqtt.host'), (int) config('mqtt.port'), 'enroll_web_client_' . $id);
            $mqtt->connect($settings, true);

            $mqtt->publish('Enroll/UserID', (string)$id, 0, false);
            $mqtt->publish('Enroll/Nome', $user->name, 0, false);
            $mqtt->disconnect();

            $timeout = 60;
            $start = time();

            while (time() - $start < $timeout) {
                
                if (Cache::has($statusKey)) {
                    $resultado = Cache::get($statusKey);

                    Cache::forget($statusKey);
                    Cache::forget($activeKey);

                    if ($resultado === "1") {
                        
                        $user->update(['finger' => true]);
                        return back()->with('success', "A impressão digital de {$user->name} foi registada com sucesso!");
                    } else {
                        return back()->with('error', "O sensor comunicou uma falha na leitura do dedo.");
                    }
                }

                usleep(500000);
            }

            // Se o tempo esgotar
            Cache::forget($activeKey);
            return back()->with('error', 'O tempo limite de 1 minuto foi atingido. O sensor não respondeu.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao comunicar com o Broker: ' . $e->getMessage());
        }
    }
    public function receberStatusEnroll(Request $request)
    {
        $userId = $request->input('user_id');
        $status = $request->input('status');

        if ($userId === null || $status === null) {
            return response()->json(['erro' => 'Dados incompletos'], 400);
        }

        $user = \App\Models\User::find($userId);

        if (!$user) {
            return response()->json(['erro' => 'Utilizador não encontrado'], 404);
        }

        if ($status == 1) {
           
            $user->update([
                'finger' => 1 
            ]);

            return response()->json(['sucesso' => true, 'mensagem' => 'Biometria ativa com sucesso!'], 200);
        } else {
            return response()->json(['sucesso' => false, 'mensagem' => 'Erro na leitura do dedo'], 200);
        }
    }
}
