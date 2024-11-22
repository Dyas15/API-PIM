<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Laravel\Lumen\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RedefinicaoController extends Controller
{
    public function EnviarEmailDeReset(Request $request)
    {
        try {
            $user = DB::table('LOGINS_CLIENTES')->select('email')->where('cpf', $request->cpf)->first();
            if (!$user) {
                return response()->json(['message' => 'CPF não encontrado.'], 404);
            }

            $token = Str::random(255);
            $data = Carbon::now()->format('Y-m-d\TH:i:s');
            DB::table('RESET_SENHA')->insert(['cpf' => $request->cpf, 'token' => $token, 'data' => DB::raw("CAST(N'{$data}' AS DATETIME)")]);

            Mail::to($user->email)->send(new ResetPasswordMail($token));

            return response()->json(['message' => 'Link de redefinição enviado com sucesso.'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao processar a solicitação.', 'error' => $e->getMessage()], 500);
        }
    }

public function ResetarSenha(Request $request)
{
    try {
        $passwordReset = DB::table('RESET_SENHA')
            ->where([['token', $request->token], ['cpf', $request->cpf]])
            ->first();

        if (!$passwordReset) {
            return redirect('/api/redefinicao/token-invalido');
        }

        $user = DB::table('LOGINS_CLIENTES')->where('cpf', $request->cpf)->first();
        if (!$user) {
            return redirect()->back()->with('error', 'Usuário não encontrado.');
        }

        DB::table('LOGINS_CLIENTES')->where('cpf', $request->cpf)->update([
            'senha' => Hash::make($request->senha)
        ]);

        DB::table('RESET_SENHA')->where('cpf', $request->cpf)->delete();

        return redirect('/api/redefinicao/senha-alterada');
    } 
    catch (\Exception $e) {
        return response()->json(['message' => 'Erro ao processar a solicitação.', 'error' => $e->getMessage()], 500);
    }
}

}
