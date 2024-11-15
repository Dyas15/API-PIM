<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EstoqueController extends Controller
{
    private function formatarData($data)
    {
        $data = str_replace('/', '-', $data);
        
        if (!empty($data)) {
            return date('Y-m-d\TH:i:s', strtotime($data));
        }

        return null;
    }

    private function verificar(Request $request)
    {
        $dadosValidados = Validator::make($request->all(), [
            'ID_PRODUTO' => 'nullable|integer|exists:PRODUTOS,ID',
            'QUANTIDADE' => 'nullable|integer|min:0',
            'DATA_VALIDADE' => 'nullable|date',
        ], [
            'ID_PRODUTO.exists' => 'O produto fornecido não existe na tabela PRODUTOS.',
        ]);

        return $dadosValidados->fails() ? response()->json(['Erro' => $dadosValidados->errors()], 422) : $dadosValidados;
    }

    public function criar(Request $request)
    {
        $dadosValidados = $this->verificar($request);

        if ($dadosValidados instanceof \Illuminate\Http\JsonResponse) {
            return $dadosValidados;
        }

        try {
            $dados = $dadosValidados->validated();

            $dados['DATA_VALIDADE'] = $this->formatarData($dados['DATA_VALIDADE'] ?? null);

            DB::table('ESTOQUE')->insert($dados);
            return response()->json(['Sucesso' => 'Produto colocado em estoque com sucesso!'], 201);
        } catch (Exception $e) {
            return response()->json(['Erro' => 'Erro ao colocar produto em estoque: ' . $e->getMessage()], 500);
        }
    }

    public function atualizar(Request $request, $id)
    {
        $dadosValidados = $this->verificar($request);

        if ($dadosValidados instanceof \Illuminate\Http\JsonResponse) {
            return $dadosValidados;
        }

        try {
            $dadosParaAtualizar = array_filter($dadosValidados->validated());

            if (empty($dadosParaAtualizar)) {
                return response()->json(['Erro' => 'Nenhum dado fornecido para atualização'], 422);
            }

            $dadosParaAtualizar['DATA_VALIDADE'] = $this->formatarData($dadosParaAtualizar['DATA_VALIDADE'] ?? null);

            $atualizado = DB::table('ESTOQUE')
                ->where('ID', $id)
                ->update($dadosParaAtualizar);

            if ($atualizado) {
                return response()->json(['Sucesso' => 'Produto em estoque foi atualizado com sucesso!'], 200);
            } else {
                return response()->json(['Erro' => 'Produto em estoque não encontrado'], 404);
            }
        } catch (Exception $e) {
            return response()->json(['Erro' => 'Erro ao atualizar estoque: ' . $e->getMessage()], 500);
        }
    }

    public function selecionar($id = null)
    {
        if ($id === null) {
            $estoques = DB::table('ESTOQUE')->get();
            return response()->json($estoques);
        } else {
            $estoque = DB::table('ESTOQUE')->where('ID', $id)->first();
            return $estoque ? response()->json($estoque) : response()->json(['Erro' => 'Estoque não encontrado'], 404);
        }
    }

    public function deletar($id)
    {
        try {
            $excluidoComSucesso = DB::table('ESTOQUE')->where('ID', $id)->delete();

            if (!$excluidoComSucesso) {
                return response()->json(['Erro' => 'Nenhum registro encontrado para excluir.'], 404);
            } else {
                return response()->json(['Sucesso' => 'Estoque excluído com sucesso!']);
            }
        } catch (Exception $e) {
            return response()->json(['Erro' => 'Erro ao excluir estoque: ' . $e->getMessage()], 500);
        }
    }
}