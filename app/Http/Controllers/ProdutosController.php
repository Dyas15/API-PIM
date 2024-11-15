<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProdutosController extends Controller
{
    private function verificar(Request $request)
    {
        $dadosValidados = Validator::make($request->all(), [
            'ID_FORNECEDOR' => 'nullable|integer|exists:FORNECEDORES,ID',
            'NOME_PRODUTO' => 'nullable|string|max:30',
            'DESCRICAO' => 'nullable|string|max:50',
            'FAMILIA' => 'nullable|integer|exists:FAMILIA_PRODUTOS,ID',
            'PRECO_UNITARIO' => 'nullable|numeric|between:0,999.99',
        ], [
            'ID_FORNECEDOR.exists' => 'O fornecedor não existe!',
            'FAMILIA.exists' => 'A família de produtos fornecida não existe!',
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

            DB::table('PRODUTOS')->insert($dados);
            return response()->json(['Sucesso' => 'Produto criado com sucesso!'], 201);
        } catch (Exception $e) {
            return response()->json(['Erro' => 'Erro ao criar produto: ' . $e->getMessage()], 500);
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

            $atualizado = DB::table('PRODUTOS')->where('ID', $id)->update($dadosParaAtualizar);

            if ($atualizado) {
                return response()->json(['Sucesso' => 'Produto atualizado com sucesso!'], 200);
            } else {
                return response()->json(['Erro' => 'Produto não encontrado'], 404);
            }
        } catch (Exception $e) {
            return response()->json(['Erro' => 'Erro ao atualizar produto: ' . $e->getMessage()], 500);
        }
    }

    public function selecionar($id = null)
    {
        if ($id === null) {
            $produtos = DB::table('PRODUTOS')->get();
            return response()->json($produtos);
        } else {
            $produto = DB::table('PRODUTOS')->where('ID', $id)->first();
            return $produto ? response()->json($produto) : response()->json(['Erro' => 'Produto não encontrado'], 404);
        }
    }

    public function deletar($id)
    {
        try {
            $excluidoComSucesso = DB::table('PRODUTOS')->where('ID', $id)->delete();

            if (!$excluidoComSucesso) {
                return response()->json(['Erro' => 'Nenhum registro encontrado para excluir.'], 404);
            } else {
                return response()->json(['Sucesso' => 'Produto excluído com sucesso!']);
            }
        } catch (Exception $e) {
            return response()->json(['Erro' => 'Erro ao excluir produto: ' . $e->getMessage()], 500);
        }
    }
}
