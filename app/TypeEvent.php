<?php

namespace App;

enum TypeEvent : string
{
    case T = 'Treinos Tecnicos';
    case TC = 'Treinos de Competição Kata e Kumite';
    case F = 'Formação Cursos, Reuniões';
    case EN = 'Encontros: Treino/Exames/Torneio';
    case C = 'Competições/Torneios/Provas';
    case E = 'Estágios Técnicos/Competição';
    case EX = 'Exames de Graduação;';
}