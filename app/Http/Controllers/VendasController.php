<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VendasController extends Controller
{
    private function verificar(Request $request)
    {
        $dadosValidados = Validator::make($request->all(), [
            'CPF' => 'nullable|digits:11|exists:CLIENTES,CPF',
            'ID_PRODUTO' => 'nullable|integer|exists:PRODUTOS,ID',
            'QUANTIDADE' => 'nullable|integer|min:1',
            'FORMA_PAGAMENTO' => 'nullable|integer|exists:FORMAS_PAGAMENTO,ID',
            'DATA_VENDA' => 'nullable|date',
        ], [
            'CPF.exists' => 'O CPF fornecido não é de nenhum cliente existente.',
            'ID_PRODUTO.exists' => 'O produto fornecido não existe.',
            'FORMA_PAGAMENTO.exists' => 'A forma de pagamento fornecida não existe.',
        ]);

        return $dadosValidados->fails() ? response()->json(['Erro' => $dadosValidados->errors()], 422) : $dadosValidados;
    }

    public function selecionar($id = null)
    {
        if ($id === null) {
            $vendas = DB::table('VENDAS')->get();
            return response()->json($vendas);
        } else {
            $venda = DB::table('VENDAS')->where('ID', $id)->first();
            return $venda ? response()->json($venda) : response()->json(['Erro' => 'Venda não encontrada'], 404);
        }
    }

    public function criar(Request $request)
    {
        $dadosValidados = $this->verificar($request);

        if ($dadosValidados instanceof \Illuminate\Http\JsonResponse) {
            return $dadosValidados;
        }

        try {
            DB::table('VENDAS')->insert($dadosValidados->validated());
            return response()->json(['Sucesso' => 'Venda criada com sucesso!'], 201);
        } catch (Exception $e) {
            return response()->json(['Erro' => 'Erro ao criar venda: ' . $e->getMessage()], 500);
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

            $atualizado = DB::table('VENDAS')->where('ID', $id)->update($dadosParaAtualizar);

            if ($atualizado) {
                return response()->json(['Sucesso' => 'Venda atualizada com sucesso!'], 200);
            } else {
                return response()->json(['Erro' => 'Venda não encontrada'], 404);
            }
        } catch (Exception $e) {
            return response()->json(['Erro' => 'Erro ao atualizar venda: ' . $e->getMessage()], 500);
        }
    }

    public function deletar($id)
    {
        try {
            $excluidoComSucesso = DB::table('VENDAS')->where('ID', $id)->delete();

            if (!$excluidoComSucesso) {
                return response()->json(['Erro' => 'Nenhum registro encontrado para excluir.'], 404);
            } else {
                return response()->json(['Sucesso' => 'Venda excluída com sucesso!']);
            }
        } catch (Exception $e) {
            return response()->json(['Erro' => 'Erro ao excluir venda: ' . $e->getMessage()], 500);
        }
    }
}
