<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FornecedoresController extends Controller
{
    private function Verificar(Request $request)
    {
        $dadosValidados = Validator::make($request->all(), [
            'NOME' => 'nullable|string|max:100',
            'CNPJ' => 'nullable|size:14|unique:FORNECEDORES',
            'ENDERECO' => 'nullable|string|max:30',
            'NUMERO' => 'nullable|integer',
            'BAIRRO' => 'nullable|string|max:30',
            'CIDADE' => 'nullable|string|max:30',
            'ESTADO' => 'nullable|string|size:2',
        ], [
            'NOME.max' => 'O nome deve ter no máximo 100 caracteres.',
            'CNPJ.size' => 'O CNPJ deve ter exatamente 14 caracteres.',
            'CNPJ.unique' => 'Este CNPJ já está cadastrado.',
            'ENDERECO.max' => 'O endereço deve ter no máximo 30 caracteres.',
            'BAIRRO.max' => 'O bairro deve ter no máximo 30 caracteres.',
            'CIDADE.max' => 'A cidade deve ter no máximo 30 caracteres.',
            'ESTADO.size' => 'O estado deve ter exatamente 2 caracteres.',
        ]);

        return $dadosValidados->fails() ? response()->json(['Erro' => $dadosValidados->errors()], 422) : $dadosValidados;
    }

    public function Selecionar($cnpj = null)
    {
        if ($cnpj == null) {
            $fornecedores = DB::table('FORNECEDORES')->get();
            return response()->json($fornecedores);
        } else {
            $fornecedor = DB::table('FORNECEDORES')->where('CNPJ', $cnpj)->first();
            return $fornecedor ? response()->json($fornecedor) : response()->json(['Erro' => 'Fornecedor não encontrado'], 404);
        }
    }

    public function Criar(Request $request)
    {
        $dadosValidados = $this->Verificar($request);

        if ($dadosValidados instanceof \Illuminate\Http\JsonResponse) {
            return $dadosValidados;
        }

        try {
            DB::table('FORNECEDORES')->insert($dadosValidados->validated());
            return response()->json(['Sucesso' => 'Fornecedor criado com sucesso!'], 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Erro ao criar fornecedor: ' . $e->getMessage()], 500);
        }
    }

    public function Atualizar($cnpj = null, Request $request)
    {
        $dadosValidados = $this->Verificar($request);

        if ($dadosValidados instanceof \Illuminate\Http\JsonResponse) {
            return $dadosValidados;
        }

        try {
            $dadosParaAtualizar = array_filter($dadosValidados->validated());

            if (empty($dadosParaAtualizar)) {
                return response()->json(['Erro' => 'Nenhum dado fornecido para atualização'], 422);
            }

            $atualizado = DB::table('FORNECEDORES')->where('CNPJ', $cnpj)->update($dadosParaAtualizar);
            if ($atualizado) {
                return response()->json(['Sucesso' => 'Fornecedor atualizado com sucesso!'], 200);
            } else {
                return response()->json(['Erro' => 'Fornecedor não encontrado'], 404);
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'Erro ao atualizar fornecedor: ' . $e->getMessage()], 500);
        }
    }

    public function Deletar($cnpj)
    {
        try {
            $fornecedor = $this->Selecionar($cnpj);

            if ($fornecedor->getStatusCode() == 404) {
                return $fornecedor;
            }

            $excluidoComSucesso = DB::table('FORNECEDORES')->where('CNPJ', $cnpj)->delete() > 0;

            if (!$excluidoComSucesso) {
                return response()->json(['Erro' => 'Nenhum registro encontrado para excluir.'], 404);
            } else {
                return response()->json(['Sucesso' => 'Fornecedor excluído com sucesso!']);
            }
        } catch (Exception $e) {
            return response()->json(['Erro' => 'Erro ao excluir fornecedor: ' . $e->getMessage()], 500);
        }
    }
}
