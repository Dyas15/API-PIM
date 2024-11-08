<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ColaboradoresController extends Controller
{
    private function Verificar(Request $request, $cpf = null)
    {
        $RegraCPF = 'nullable|string|size:11|regex:/^\d+$/';
        $RegraCPF .= $cpf ? "|unique:COLABORADORES,CPF,$cpf,CPF" : '|unique:COLABORADORES';

        $dadosValidados = Validator::make($request->all(), [
            'CPF' => $RegraCPF,
            'NOME' => 'nullable|string|max:30',
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
            $colaborador = DB::table('COLABORADORES')->get();
            return response()->json($colaborador);
        } else {
            $colaborador = DB::table('COLABORADORES')->where('CPF', $cpf)->first();
            return $colaborador ? response()->json($colaborador) : response()->json(['Erro' => 'Colaborador não encontrado'], 404);
        }
    }

    public function Criar(Request $request)
    {
        $dadosValidados = $this->Verificar($request);

        if ($dadosValidados instanceof \Illuminate\Http\JsonResponse) {
            return $dadosValidados;
        }

        try {
            DB::table('COLABORADORES')->insert($dadosValidados->validated());
            return response()->json(['Sucesso' => 'Colaborador criado com sucesso!'], 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Erro ao criar Colaborador: ' . $e->getMessage()], 500);
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

            $atualizado = DB::table('COLABORADORES')->where('CPF', $cpf)->update($dadosParaAtualizar);
            if ($atualizado) {
                return response()->json(['Sucesso' => 'Atualizado com sucesso!'], 200);
            } else {
                return response()->json(['Erro' => 'Colaborador não encontrado'], 404);
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'Erro ao atualizar Colaborador: ' . $e->getMessage()], 500);
        }
    }

    public function Deletar($cpf = null)
    {
        try {
            $colaborador = $this->Selecionar($cpf);

            // Verifica se o Colaborador foi encontrado
            if ($colaborador->getStatusCode() == 404) {
                return $colaborador;
            }

            $excluidoComSucesso = DB::table('COLABORADORES')->where('cpf', $cpf)->delete() > 0;

            // Verifica se pelo menos uma exclusão ocorreu
            if (!$excluidoComSucesso) {
                return response()->json(['Erro' => 'Nenhum registro encontrado para excluir.'], 404);
            } else {
                return response()->json(['Sucesso' => 'Colaborador excluído com sucesso!']);
            }
        } catch (Exception $e) {
            return response()->json(['Erro' => 'Erro ao excluir Colaborador: ' . $e->getMessage()], 500);
        }
    }
}
