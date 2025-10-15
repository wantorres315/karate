<?php

namespace App\Http\Controllers;

use App\Models\Boleto;
use App\Models\Profile;
use App\Models\ValoresAula;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BoletoController extends Controller
{
    // Listar boletos
    public function index(Request $request)
    {
        $mes = $request->mes ?? date('m');
        $ano = $request->ano ?? date('Y');
        $ateData = $request->ate_data ?? null;

        $boletos = Boleto::with('profile')
            ->where('mes', $mes)
            ->where('ano', $ano)
            ->when($ateData, function ($query, $ateData) {
                $query->where('data_vencimento', '<=', $ateData);
            })
            ->paginate();

        return view('boletos.index', compact('boletos', 'mes', 'ano', 'ateData'));
    }

    // Gerar boletos
    public function gerar(Request $request)
    {
        $mes = $request->mes ?? date('m');
        $ano = $request->ano ?? date('Y');
        $dataVencimento = $request->data_vencimento ?? date('Y-m-d', strtotime('+7 days'));

        $profiles = Profile::all();

        foreach ($profiles as $profile) {

            // Ignora se o boleto já foi gerado para este mês/ano
            $exists = Boleto::where('profile_id', $profile->id)
                ->where('mes', $mes)
                ->where('ano', $ano)
                ->first();
            if ($exists) continue;

            // Pegar configuração de valor válida para a data de matrícula do aluno
            $configuracao = ValoresAula::all()->first(function ($valor) use ($profile) {
                return $valor->isValidoParaAluno($profile);
            });

            // Se não encontrar configuração, pular ou usar valor padrão
            if (!$configuracao) continue;

            // Definir valor baseado na quantidade de membros
            $quantidade = $profile->quantidade_membros ?? 1;
            if ($quantidade == 2 && $configuracao->valor_2_membros) {
                $valor = $configuracao->valor_2_membros;
            } elseif ($quantidade >= 3 && $configuracao->valor_3_ou_mais_membros) {
                $valor = $configuracao->valor_3_ou_mais_membros;
            } else {
                $valor = $configuracao->valor_normal;
            }

            // Criar boleto
            Boleto::create([
                'profile_id' => $profile->id,
                'mes' => $mes,
                'ano' => $ano,
                'valor' => $valor,
                'data_geracao' => date('Y-m-d'),
                'data_vencimento' => $dataVencimento,
                'arquivo_boleto_url' => null,
            ]);
        }

        return redirect()->back()->with('success', 'Boletos gerados com sucesso!');
    }

    // Marcar como pago
    public function marcarPago(Boleto $boleto)
    {
        $boleto->update([
            'status_pagamento' => 'pago',
            'data_pagamento' => date('Y-m-d')
        ]);

        return redirect()->back()->with('success', 'Boleto marcado como pago!');
    }

    // Upload do comprovante
    public function uploadComprovante(Request $request, Boleto $boleto)
    {
        if ($request->hasFile('comprovante')) {
            $path = $request->file('comprovante')->store('comprovantes');
            $boleto->update(['arquivo_comprovante_url' => $path]);
        }

        return redirect()->back()->with('success', 'Comprovante enviado!');
    }

    // Download boleto
    public function downloadBoleto(Boleto $boleto)
    {
        return Storage::download($boleto->arquivo_boleto_url);
    }

    // Download comprovante
    public function downloadComprovante(Boleto $boleto)
    {
        return Storage::download($boleto->arquivo_comprovante_url);
    }
}
