<?php

namespace App;

enum Role : string
{
    case TREINADOR_GRAU_I = 'treinador grau I';
    case TREINADOR_GRAU_II = 'treinador grau II';
    case TREINADOR_GRAU_III = 'treinador grau III';
    case ARBITRATOR = 'arbitrator';
    case PRATICANTE = 'praticante';
    case SUPER_ADMIN = 'super admin';
    
}