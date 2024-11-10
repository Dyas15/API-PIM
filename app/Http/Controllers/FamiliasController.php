<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FamiliasController extends Controller
{

    private function Verificar(Request $request)
    {
        $dadosValidados = Validator::make($request->all(), [
            'ID' => 'nullable|integer',
            'NOME' => 'nullable|string|unique:FAMILIA_PRODUTOS|max:30',
        ], [
            'NOME.unique' => 'O nome já está cadastrado.',
            'NOME.max' => 'O nome deve ter no máximo 30 caracteres.',
        ]);

        return $dadosValidados->fails() ? response()->json(['Erro' => $dadosValidados->errors()], 422) : $dadosValidados;
    }

    public function Selecionar($nome = null)
    {
        if ($nome == null) {
            $familia = DB::table('FAMILIA_PRODUTOS')->get();
            return response()->json($familia);
        } else {
            $familia = DB::table('FAMILIA_PRODUTOS')->where('NOME', 'like', "%$nome%")->get();
            return $familia ? response()->json($familia) : response()->json(['Erro' => 'Familia não encontrada'], 404);
        }
    }

    public function Criar(Request $request)
    {
        $dadosValidados = $this->Verificar($request);

        if ($dadosValidados instanceof \Illuminate\Http\JsonResponse) {
            return $dadosValidados;
        }

        try {
            DB::table('FAMILIA_PRODUTOS')->insert($dadosValidados->validated());
            return response()->json(['Sucesso' => 'Familia criada com sucesso!'], 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Erro ao criar Familia: ' . $e->getMessage()], 500);
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

            $atualizado = DB::table('FAMILIA_PRODUTOS')->whereRaw('CAST(ID AS NVARCHAR) = ?', [$valor])->orWhereRaw('LOWER(NOME) = ?', [$valor])->update($dadosParaAtualizar);
            if ($atualizado) {
                return response()->json(['Sucesso' => 'Atualizado com sucesso!'], 200);
            } else {
                return response()->json(['Erro' => 'Familia não encontrada'], 404);
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'Erro ao atualizar Familia: ' . $e->getMessage()], 500);
        }
    }

    public function Deletar($valor = null)
    {
        try {
            $familia = $this->Selecionar($valor);

            if ($familia->getStatusCode() == 404) {
                return $familia;
            }

            $excluidoComSucesso = DB::table('FAMILIA_PRODUTOS')->whereRaw('CAST(ID AS NVARCHAR) = ?', [$valor])->orWhereRaw('LOWER(NOME) = ?', [$valor])->delete() > 0;

            if (!$excluidoComSucesso) {
                return response()->json(['Erro' => 'Nenhum registro encontrado para excluir.'], 404);
            } else {
                return response()->json(['Sucesso' => 'Familia excluído com sucesso!']);
            }
        } catch (Exception $e) {
            return response()->json(['Erro' => 'Erro ao excluir Familia: ' . $e->getMessage()], 500);
        }
    }
}
