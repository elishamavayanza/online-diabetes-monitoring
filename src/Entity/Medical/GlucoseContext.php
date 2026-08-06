<?php

namespace App\Entity\Medical;

enum GlucoseContext: string
{
    case FASTING = 'FASTING';
    case BEFORE_MEAL = 'BEFORE_MEAL';
    case AFTER_MEAL = 'AFTER_MEAL';
    case BEDTIME = 'BEDTIME';
    case RANDOM = 'RANDOM';
}
