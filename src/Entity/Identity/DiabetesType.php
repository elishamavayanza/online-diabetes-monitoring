<?php

namespace App\Entity\Identity;

enum DiabetesType: string
{
    case TYPE_1 = 'TYPE_1';
    case TYPE_2 = 'TYPE_2';
    case GESTATIONAL = 'GESTATIONAL';
    case OTHER = 'OTHER';
}
