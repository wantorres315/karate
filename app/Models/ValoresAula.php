<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ValoresAula extends Model
{
    use HasFactory;

    // Nome da tabela (opcional se seguir convenção)
    protected $table = 'valores_aulas';

    // Campos que podem ser preenchidos em mass assignment
    protected $fillable = [
        'nome',
        'valor_normal',
        'valor_2_membros',
        'valor_3_ou_mais_membros',
        'data_inicial',
        'data_final'
    ];

    // Se quiser, pode tratar datas automaticamente
    protected $dates = [
        'data_inicial',
        'data_final',
        'created_at',
        'updated_at'
    ];

    /**
     * Verifica se uma data está dentro do período de validade da configuração
     */
    public function isValidoParaAluno($profile)
    {
        $dataMatricula = $profile->data_matricula; // data de matrícula do aluno
        return $dataMatricula >= $this->data_inicial && $dataMatricula <= $this->data_final;
    }
}
