<?php

namespace App;

enum DocumentoIdentificacao : string
{
    case CARTAO_CIDADAO = 'Cartão de Cidadão';
    case PASSAPORTE = 'Passaporte';
    case TITULO_RESIDENCIA = 'Título de Residência';
    case ID_ESTRANGEIRO = 'ID de Estrangeiro';
    case OUTRO = 'Outro';
    case CEDULA_PESSOAL = 'Cédula Pessoal';
}
