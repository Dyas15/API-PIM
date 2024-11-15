<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PagamentosController extends Controller
{
    private function Verificar(Request $request)
    {
        $dadosValidados = Validator::make($request->all(), [
            'ID' => 'nullable|integer',
            'PAGAMENTO' => 'nullable|string|unique:FORMAS_PAGAMENTO|max:10',
        ], [
            'PAGAMENTO.unique' => 'A forma de pagamento já está cadastrada.',
            'PAGAMENTO.max' => 'A forma de pagamento deve ter no máximo 10 caracteres.',
        ]);

        return $dadosValidados->fails() ? response()->json(['Erro' => $dadosValidados->errors()], 422) : $dadosValidados;
    }

    public function Selecionar($valor = null)
    {
        if ($valor == null) {
            $familia = DB::table('FORMAS_PAGAMENTO')->get();
            return response()->json($familia);
        } else {
            $familia = DB::table('FORMAS_PAGAMENTO')->where('ID', $valor)->get();
            return $familia ? response()->json($familia) : response()->json(['Erro' => 'Forma de pagamento não encontrada'], 404);
        }
    }

    public function Criar(Request $request)
    {
        $dadosValidados = $this->Verificar($request);

        if ($dadosValidados instanceof \Illuminate\Http\JsonResponse) {
            return $dadosValidados;
        }

        try {
            DB::table('FORMAS_PAGAMENTO')->insert($dadosValidados->validated());
            return response()->json(['Sucesso' => 'Pagamento criado com sucesso!'], 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Erro ao criar Pagamento: ' . $e->getMessage()], 500);
        }
    }

    public function Atualizar(Request $request, $valor)
    {
        $valor = strtolower($valor);
        $dadosValidados = $this->Verificar($request);

        if ($dadosValidados instanceof \Illuminate\Http\JsonResponse) {
            return $dadosValidados;
        }

        try {
            $dadosParaAtualizar = array_filter($dadosValidados->validated());

            if (empty($dadosParaAtualizar)) {
                return response()->json(['Erro' => 'Nenhum dado fornecido para atualização'], 422);
            }

            $atualizado = DB::table('FORMAS_PAGAMENTO')
                ->whereRaw('CAST(ID AS NVARCHAR) = ?', [$valor])
                ->orWhereRaw('LOWER(PAGAMENTO) LIKE ?', ["%{$valor}%"])
                ->update($dadosParaAtualizar);

            if ($atualizado) {
                return response()->json(['Sucesso' => 'Atualizado com sucesso!'], 200);
            } else {
                return response()->json(['Erro' => 'Pagamento não encontrado'], 404);
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'Erro ao atualizar Pagamento: ' . $e->getMessage()], 500);
        }
    }

    public function Deletar($valor = null)
    {
        try {
            $familia = $this->Selecionar($valor);

            if ($familia->getStatusCode() == 404) {
                return $familia;
            }

            $excluidoComSucesso = DB::table('FORMAS_PAGAMENTO')
                ->whereRaw('CAST(ID AS NVARCHAR) = ?', [$valor])
                ->orWhereRaw('LOWER(PAGAMENTO) = ?', [$valor])
                ->delete() > 0;

            if (!$excluidoComSucesso) {
                return response()->json(['Erro' => 'Nenhum registro encontrado para excluir.'], 404);
            } else {
                return response()->json(['Sucesso' => 'Pagamento excluído com sucesso!']);
            }
        } catch (Exception $e) {
            return response()->json(['Erro' => 'Erro ao excluir Pagamento: ' . $e->getMessage()], 500);
        }
    }
}
