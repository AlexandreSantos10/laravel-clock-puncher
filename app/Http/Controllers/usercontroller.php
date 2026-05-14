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
        if ($request->input('name') == "") {
            $users = User::query()->paginate(10);
        } else {
           $users = User::query()
                ->where("name", "LIKE", "%" . $request->input('name') . "%")
                ->paginate(10)
                ->withQueryString();
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

    public function enroll(Request $request, $id)
    {
        $user = User::findOrFail($id);

        try {
            $settings = (new \PhpMqtt\Client\ConnectionSettings)
                ->setUseTls(true)
                ->setTlsVerifyPeer(false)
                ->setUsername(config('mqtt.username'))
                ->setPassword(config('mqtt.password'));

            $mqtt = new \PhpMqtt\Client\MqttClient(config('mqtt.host'), (int) config('mqtt.port'), 'enroll_web_client_' . $id);
            $mqtt->connect($settings, true);

            $mqtt->publish('Enroll/UserID', (string)$id, 0, false);
            $mqtt->publish('Enroll/Nome', $user->name, 0, false);
            $mqtt->disconnect();

            // Se for um pedido AJAX (JavaScript), devolve JSON para não refrescar a página
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true]);
            }

            // Fallback caso a página seja acedida normalmente
            return back()->with('success', "Comando enviado para o sensor! Peça ao funcionário {$user->name} para colocar o dedo na máquina.");

        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
            }
            return back()->with('error', 'Erro ao comunicar com o Broker: ' . $e->getMessage());
        }
    }

    // ADICIONAR ESTA FUNÇÃO NOVA PARA O JAVASCRIPT CONSEGUIR LER O ESTADO
    public function checkFingerStatus($id)
    {
        $user = User::findOrFail($id);
        return response()->json([
            'finger' => $user->finger
        ]);
    }

    public function receberStatusEnroll(Request $request)
    {
        $userId = $request->input('user_id');
        $status = $request->input('status');

        if ($userId === null || $status === null) {
            return response()->json(['erro' => 'Dados incompletos'], 400);
        }

        $user = \App\Models\User::findOrFail($userId);

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
    
    public function deleteFinger($id)
    {
        $user = \App\Models\User::findOrFail($id);

        try {
            
            $settings = (new \PhpMqtt\Client\ConnectionSettings)
                ->setUseTls(true)
                ->setTlsVerifyPeer(false)
                ->setUsername(config('mqtt.username'))
                ->setPassword(config('mqtt.password'));

            $mqtt = new \PhpMqtt\Client\MqttClient(config('mqtt.host'), (int) config('mqtt.port'), 'delete_web_client_' . $id);
            $mqtt->connect($settings, true);

            
            $mqtt->publish('Delete/UserID', (string)$id, 0, false);
            $mqtt->disconnect();

            return back()->with('success', "Comando enviado! O sensor vai apagar a biometria de {$user->name}.");

        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao comunicar com o Broker MQTT: ' . $e->getMessage());
        }
    }


    
    public function receberStatusDeleteFinger(Request $request)
    {
        $userId = $request->input('user_id');
        $status = $request->input('status'); 

        if ($userId === null || $status === null) {
            return response()->json(['erro' => 'Dados incompletos'], 400);
        }

        $user = \App\Models\User::findOrFail($userId);

        if (!$user) {
            return response()->json(['erro' => 'Utilizador não encontrado'], 404);
        }

        if ($status == 1) {
          
            $user->update([
                'finger' => 0 
            ]);

            return response()->json(['sucesso' => true, 'mensagem' => 'Biometria removida com sucesso da base de dados!'], 200);
        } else {
            return response()->json(['sucesso' => false, 'mensagem' => 'Erro do ESP32 ao tentar apagar a biometria'], 200);
        }
    }
}
