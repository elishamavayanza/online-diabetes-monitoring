<?php

namespace App\Entity\Common;

enum Gender: string
{
    case MALE = 'MALE';
    case FEMALE = 'FEMALE';
    case OTHER = 'OTHER';
    case UNSPECIFIED = 'UNSPECIFIED';
}
