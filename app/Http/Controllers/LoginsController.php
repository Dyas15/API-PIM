<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Validator;

class LoginsController extends Controller
{
    private function Verificar(Request $request,$tipo, $cpf = null)
    {
        $tipo = strtoupper($tipo);
        $RegraCPF = 'nullable|string|size:11|regex:/^\d+$/';
        $RegraCPF .= $cpf ? "|unique:$tipo,CPF,$cpf,CPF" : "|unique:$tipo";

        $dadosValidados = Validator::make($request->all(), [
            'CPF' => $RegraCPF,
            'SENHA' => 'nullable|string|max:100|min:5',
            'STATUS' => 'nullable|boolean|size:1'
        ], [
            'CPF.size' => 'Deve ter exatamente 11 caracteres.',
            'CPF.unique' => 'CPF já está cadastrado.',
            'CPF.regex' => 'Digite apenas numerais.',
            'STATUS.size' => 'Digite apenas verdadeiro ou falso em bit (1 ou 0)'
        ]);

        return $dadosValidados->fails() ? response()->json(['Erro' => $dadosValidados->errors()], 422) : $dadosValidados;
    }

    public function Selecionar($tipo, $cpf = null)
    {
        $tabela = $tipo == "cliente" ? 'LOGINS_CLIENTES' : ($tipo == "colaborador" ? 'LOGINS_COLABORADOR' : null);
        ucfirst($tipo);

        if ($tabela) {
            if ($cpf == null) {
                $usuarios = DB::table($tabela)->get();
                return response()->json($usuarios);
            } else {
                $usuario = DB::table($tabela)->where('CPF', $cpf)->first();
                return $usuario ? response()->json($usuario) : response()->json(['Erro' => "$tipo não encontrado"], 404);
            }
        }
        return response()->json(['Erro' => 'Tipo inválido. Insira apenas Cliente ou Colaborador.'], 400);
    }

    public function Criar(Request $request, $tipo)
    {
        $tabela = $tipo == "cliente" ? 'LOGINS_CLIENTES' : ($tipo == "colaborador" ? 'LOGINS_COLABORADOR' : null);
        $tipo = ucfirst($tipo);

        $dadosValidados = $this->Verificar($request, $tabela);

        if ($dadosValidados instanceof \Illuminate\Http\JsonResponse) {
            return $dadosValidados;
        }

        if ($tabela) {
            try {
                DB::table($tabela)->insert($dadosValidados->validated());
                return response()->json(['Sucesso' => "Login de $tipo criado com sucesso!"], 201);
            } catch (Exception $e) {
                if ($e->getCode() == 23000) {
                    return response()->json(['error' => "Erro ao criar login de $tipo: Necessário criar o $tipo primeiro! "], 500);
                } else {
                    return response()->json(['error' => "Erro ao criar login de $tipo: " . $e->getMessage()], 500);
                }
            }
        }
    }

    public function Atualizar(Request $request, $tipo, $cpf)
    {
        $tabela = $tipo == "cliente" ? 'LOGINS_CLIENTES' : ($tipo == "colaborador" ? 'LOGINS_COLABORADOR' : null);

        ucfirst($tipo);
        $dadosValidados = $this->Verificar($request, $tipo, $cpf);

        if ($dadosValidados instanceof \Illuminate\Http\JsonResponse) {
            return $dadosValidados;
        }

        try {
            $dadosParaAtualizar = array_filter($dadosValidados->validated());

            if (empty($dadosParaAtualizar)) {
                return response()->json(['Erro' => 'Nenhum dado fornecido para atualização'], 422);
            }

            $atualizado = DB::table($tabela)->where('CPF', $cpf)->update($dadosParaAtualizar);
            if ($atualizado) {
                return response()->json(['Sucesso' => "$tipo Atualizado com sucesso!"], 200);
            } else {
                return response()->json(['Erro' => "$tipo não encontrado"], 404);
            }
        } catch (Exception $e) {
            return response()->json(['error' => "Erro ao atualizar: " . $e->getMessage()], 500);
        }
    }

    public function Desativar($tipo, $cpf)
    {
        try {
            $tabela = $tipo == "cliente" ? 'LOGINS_CLIENTES' : ($tipo == "colaborador" ? 'LOGINS_COLABORADOR' : null);
            $colaborador = $this->Selecionar($tipo, $cpf);
            $tipo = ucfirst($tipo);

            if ($colaborador->getStatusCode() == 404) {
                return $colaborador;
            } else {
                $excluidoComSucesso = DB::table($tabela)->where('CPF', $cpf)->update(['STATUS' => 0]);

                if ($excluidoComSucesso) {
                    return response()->json(['Sucesso' => "$tipo desativado com sucesso!"]);
                }
            }
        } catch (Exception $e) {
            return response()->json(['Erro' => "Erro ao excluir $tipo: " . $e->getMessage()], 500);
        }
    }
}
