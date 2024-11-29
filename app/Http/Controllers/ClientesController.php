<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ClientesController extends Controller
{
    private function Verificar(Request $request, $cpf = null)
    {
        $RegraCPF = 'nullable|string|size:11|regex:/^\d+$/';
        $RegraCPF .= $cpf ? "|unique:CLIENTES,CPF,$cpf,CPF" : '|unique:CLIENTES';

        $dadosValidados = Validator::make($request->all(), [
            'CPF' => $RegraCPF,
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
            $cliente = DB::table('CLIENTES')->get();
            return response()->json($cliente);
        } else {
            $Cliente = DB::table('CLIENTES')->where('CPF', $cpf)->first();
            return $Cliente ? response()->json($Cliente) : response()->json(['Erro' => 'Cliente não encontrado'], 404);
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
            return response()->json(['Sucesso' => 'Cliente criado com sucesso!'], 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Erro ao criar Cliente: ' . $e->getMessage()], 500);
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
                return response()->json(['Erro' => 'Cliente não encontrado'], 404);
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'Erro ao atualizar Cliente: ' . $e->getMessage()], 500);
        }
    }

    public function Deletar($cpf = null)
    {
        try {
            $cliente = $this->Selecionar($cpf);

            if ($cliente->getStatusCode() == 404) {
                return $cliente;
            }

            $dadosUsuario = DB::table('LOGINS_CLIENTES')->where('cpf', $cpf)->delete() > 0;
            $dadosCliente = DB::table('CLIENTES')->where('cpf', $cpf)->delete() > 0;


            if (!$dadosCliente && !$dadosUsuario) {
                return response()->json(['Erro' => 'Nenhum registro encontrado para excluir.'], 404);
            } else {
                return response()->json(['Sucesso' => 'Cliente excluído com sucesso!']);
            }
        } catch (Exception $e) {
            return response()->json(['Erro' => 'Erro ao excluir Cliente: ' . $e->getMessage()], 500);
        }
    }
}
