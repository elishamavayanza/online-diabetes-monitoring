<?php

namespace App\Enum\Medical;

enum FollowUpReportElement: string
{
    case GLUCOSE = 'glucose';
    case HBA1C = 'hba1c';
    case BLOOD_PRESSURE = 'blood_pressure';
    case WEIGHT = 'weight';
    case TREATMENT = 'treatment';
    case PHYSICAL_ACTIVITY = 'physical_activity';
    case NUTRITION = 'nutrition';
    case LABORATORY = 'laboratory';

    public function label(): string
    {
        return match ($this) {
            self::GLUCOSE => 'Glycémie',
            self::HBA1C => 'HbA1c',
            self::BLOOD_PRESSURE => 'Tension artérielle',
            self::WEIGHT => 'Poids / IMC',
            self::TREATMENT => 'Traitement / observance',
            self::PHYSICAL_ACTIVITY => 'Activité physique',
            self::NUTRITION => 'Repas / nutrition',
            self::LABORATORY => 'Résultats de laboratoire',
        };
    }

    /**
     * @param string[] $values
     * @return self[]
     */
    public static function fromValues(array $values): array
    {
        $elements = [];

        foreach ($values as $value) {
            $element = self::tryFrom($value);
            if ($element !== null) {
                $elements[] = $element;
            }
        }

        return $elements;
    }
}
