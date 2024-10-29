<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UsuariosController extends Controller
{
    private function Verificar(Request $request, $cpf = null)
    {
        // Regras de validação ajustadas para permitir atualizações parciais
        $cpfRule = 'nullable|string|size:11|regex:/^\d+$/';
        $cpfRule .= $cpf ? '|unique:CLIENTES,CPF,' . $cpf . ',CPF' : '|unique:CLIENTES';

        $dadosValidados = Validator::make($request->all(), [
            'CPF' => $cpfRule,
            'NOME' => 'nullable|string|max:30',
            'TELEFONE' => 'nullable|string|size:11'
        ], [
            'CPF.size' => 'Deve ter exatamente 11 caracteres.',
            'CPF.unique' => 'Já está cadastrado.',
            'CPF.regex' => 'Digite apenas numerais.'
        ]);

        return $dadosValidados->fails() ? response()->json(['Erro' => $dadosValidados->errors()], 422) : $dadosValidados;
    }

    public function Selecionar($cpf = null)
    {
        if ($cpf == null) {
            $usuarios = DB::table('CLIENTES')->get();
            return response()->json($usuarios);
        } else {
            $usuario = DB::table('CLIENTES')->where('CPF', $cpf)->first();
            return $usuario ? response()->json($usuario) : response()->json(['Erro' => 'Usuário não encontrado'], 404);
        }
    }

    public function Criar(Request $request)
    {
        $dadosValidados = $this->Verificar($request);

        if ($dadosValidados instanceof \Illuminate\Http\JsonResponse) {
            return $dadosValidados;
        }

        try {
            DB::table('CLIENTES')->insert($dadosValidados->validated());
            return response()->json(['Sucesso' => 'Usuário criado com sucesso!'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao criar usuário: ' . $e->getMessage()], 500);
        }
    }

    public function Atualizar(Request $request, $cpf)
    {
        $dadosValidados = $this->Verificar($request, $cpf);

        if ($dadosValidados instanceof \Illuminate\Http\JsonResponse) {
            return $dadosValidados;
        }

        try {
            $dadosParaAtualizar = array_filter($dadosValidados->validated());

            if (empty($dadosParaAtualizar)) {
                return response()->json(['Erro' => 'Nenhum dado fornecido para atualização'], 422);
            }

            $atualizado = DB::table('CLIENTES')->where('CPF', $cpf)->update($dadosParaAtualizar);
            if ($atualizado) {
                return response()->json(['Sucesso' => 'Atualizado com sucesso!'], 200);
            } else {
                return response()->json(['Erro' => 'Usuário não encontrado'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao atualizar usuário: ' . $e->getMessage()], 500);
        }
    }

    public function Deletar($cpf)
{
    try {
        $usuario = $this->Selecionar($cpf);

        if ($usuario->getStatusCode() == 404)
        {
            return $usuario;
        }
        
        DB::table('LOGINS')->where('cpf', $cpf)->delete();
        DB::table('CLIENTES')->where('cpf', $cpf)->delete();
        

        return response()->json(['Sucesso' => 'Usuário excluído com sucesso!']);
    } catch (\Exception $e) {
        return response()->json(['Erro' => 'Erro ao excluir usuário: ' . $e->getMessage()], 500);
    }
}

}
