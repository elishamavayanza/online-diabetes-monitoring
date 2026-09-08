// ============================================================
// followUpClinicalInterpretation.ts
// Interprétations cliniques & repères applicables au rapport
// de suivi patient. Référentiels : ADA (glycémie, HbA1c,
// hypertension & diabète), OMS (IMC, activité physique),
// consensus francophone sur l'observance thérapeutique.
// ============================================================

export type InterpretationLevel = 'good' | 'warning' | 'critical' | 'info';

export interface ClinicalInterpretation {
    level: InterpretationLevel;
    status: string;
    note: string;
    recommendation: string;
}

export interface ClinicalTarget {
    metric: string;
    target: string;
    reference: string;
}

interface MeasurableStats {
    average: number | null;
    minimum: number | null;
    maximum: number | null;
    count: number;
    unit?: string | null;
}

export const CLINICAL_TARGETS: ClinicalTarget[] = [
    { metric: 'Glycémie (mg/dL)', target: '70 – 180', reference: 'ADA – objectif général pour patients diabétiques' },
    { metric: 'HbA1c (%)', target: '< 7,0', reference: 'ADA – objectif de contrôle glycémique de la plupart des adultes' },
    { metric: 'Tension artérielle (mmHg)', target: '< 130 / 80', reference: 'ADA – cible tensionnelle des personnes diabétiques' },
    { metric: 'IMC (kg/m²)', target: '18,5 – 24,9', reference: 'OMS – corpulence normale' },
    { metric: 'Observance', target: '≥ 80 %', reference: 'Seuil usuels d’adhésion thérapeutique (Morisky)' },
    { metric: 'Activité physique', target: '150 min/sem.', reference: 'OMS – activité d’intensité modérée' },
];

export function interpretGlucose(average: number | null): ClinicalInterpretation | null {
    if (average === null || average === undefined) {
        return null;
    }

    if (average < 70) {
        return {
            level: 'critical',
            status: 'Hypoglycémie',
            note: 'La glycémie moyenne de la période est inférieure à la cible (70 mg/dL).',
            recommendation: 'Signaler au prescripteur ; réévaluer le schéma thérapeutique et les horaires de prise.',
        };
    }

    if (average <= 180) {
        return {
            level: 'good',
            status: 'Dans la cible',
            note: 'La glycémie moyenne de la période est comprise dans la cible générale (70 – 180 mg/dL).',
            recommendation: 'Maintenir le suivi et l’autosurveillance glycémique au rythme recommandé.',
        };
    }

    return {
        level: 'warning',
        status: 'Hyperglycémie',
        note: 'La glycémie moyenne de la période dépasse la cible générale (180 mg/dL).',
        recommendation: 'Vérifier l’observance et le schéma thérapeutique ; envisager une consultation de suivi.',
    };
}

export function interpretHbA1c(average: number | null): ClinicalInterpretation | null {
    if (average === null || average === undefined) {
        return null;
    }

    if (average < 5.7) {
        return {
            level: 'info',
            status: 'Valeur normale',
            note: 'L’HbA1c moyenne est dans la plage des personnes non diabétiques (< 5,7 %).',
            recommendation: 'Poursuivre le suivi habituel du contrôle glycémique.',
        };
    }

    if (average < 6.5) {
        return {
            level: 'warning',
            status: 'Zone de pré-diabète',
            note: 'L’HbA1c moyenne se situe dans la zone de pré-diabète (5,7 – 6,4 %).',
            recommendation: 'Renforcer les mesures hygiéno-diététiques et contrôler l’évolution.',
        };
    }

    if (average <= 7) {
        return {
            level: 'good',
            status: 'Objectif atteint',
            note: 'L’HbA1c moyenne respecte l’objectif général (< 7,0 %) de la plupart des adultes diabétiques.',
            recommendation: 'Poursuivre la stratégie actuelle et le rythme de contrôle HbA1c recommandé.',
        };
    }

    return {
        level: 'warning',
        status: 'Au-dessus de l’objectif',
        note: 'L’HbA1c moyenne dépasse l’objectif général (< 7,0 %).',
        recommendation: 'Réévaluer la stratégie thérapeutique avec le clinicien traitant.',
    };
}

export function interpretBloodPressure(systolic: number | null, diastolic: number | null): ClinicalInterpretation | null {
    if (systolic === null || systolic === undefined) {
        return null;
    }

    const dia = diastolic ?? 0;

    if (systolic < 120 && dia < 80) {
        return {
            level: 'good',
            status: 'Pression optimale',
            note: 'La tension artérielle moyenne est dans la plage optimale (< 120 / 80 mmHg).',
            recommendation: 'Maintenir le suivi tensionnel et le mode de vie actuel.',
        };
    }

    if (systolic < 130 && dia < 80) {
        return {
            level: 'warning',
            status: 'Pression élevée',
            note: 'La tension artérielle moyenne est élevée (120 – 129 / < 80 mmHg).',
            recommendation: 'Surveiller les mesures et favoriser les règles hygiéno-diététiques.',
        };
    }

    if (systolic < 140 && dia < 90) {
        return {
            level: 'warning',
            status: 'Hypertension stade 1',
            note: 'La tension artérielle moyenne correspond à une hypertension de stade 1 (130 – 139 / 80 – 89 mmHg).',
            recommendation: 'À confirmer par des mesures répétées : consultation médicale conseillée.',
        };
    }

    return {
        level: 'critical',
        status: 'Hypertension stade 2',
        note: 'La tension artérielle moyenne est ≥ 140 / 90 mmHg (stade 2).',
        recommendation: 'Prise en charge médicale à prévoir dans les meilleurs délais.',
    };
}

export function interpretBmi(bmi: number | null): ClinicalInterpretation | null {
    if (bmi === null || bmi === undefined) {
        return null;
    }

    if (bmi < 18.5) {
        return {
            level: 'warning',
            status: 'Insuffisance pondérale',
            note: 'L’IMC moyen est inférieur à 18,5 kg/m².',
            recommendation: 'Évaluer les apports nutritionnels et rechercher une cause associée.',
        };
    }

    if (bmi < 25) {
        return {
            level: 'good',
            status: 'Corpulence normale',
            note: 'L’IMC moyen est dans la plage de corpulence normale (18,5 – 24,9 kg/m²).',
            recommendation: 'Poursuivre les équilibres alimentaires et l’activité physique.',
        };
    }

    if (bmi < 30) {
        return {
            level: 'warning',
            status: 'Surpoids',
            note: 'L’IMC moyen correspond à un surpoids (25 – 29,9 kg/m²).',
            recommendation: 'Privilégier une stratégie nutritionnelle et d’activité physique adaptée.',
        };
    }

    return {
        level: 'critical',
        status: 'Obésité',
        note: 'L’IMC moyen est ≥ 30 kg/m².',
        recommendation: 'Proposer un accompagnement nutritionnel et médical renforcé.',
    };
}

export function interpretAdherence(rate: number | null): ClinicalInterpretation | null {
    if (rate === null || rate === undefined) {
        return null;
    }

    if (rate >= 80) {
        return {
            level: 'good',
            status: 'Bonne observance',
            note: `L’observance est de ${rate} %, au-dessus du seuil de 80 %.`,
            recommendation: 'Poursuivre la prise des traitements selon la prescription.',
        };
    }

    if (rate >= 50) {
        return {
            level: 'warning',
            status: 'Observance moyenne',
            note: `L’observance est de ${rate} % (50 – 79 %) : des prises sont manquées ou retardées.`,
            recommendation: 'Identifier les freins à la prise et simplifier le schéma si possible.',
        };
    }

    return {
        level: 'critical',
        status: 'Forte inobservance',
        note: `L’observance est de ${rate} %, très inférieure au seuil thérapeutique.`,
        recommendation: 'Mettre en place un accompagnement rapproché et réévaluer le traitement.',
    };
}

export function interpretActivity(totalMinutes: number, from: string, to: string): ClinicalInterpretation {
    const weeks = Math.max(1, daysBetween(from, to) / 7);
    const weekly = totalMinutes / weeks;
    const target = 150;

    if (weekly >= target) {
        return {
            level: 'good',
            status: 'Objectif atteint',
            note: `Activité moyenne de ${Math.round(weekly)} min/sem., conforme à la recommandation OMS (≥ 150 min/sem.).`,
            recommendation: 'Maintenir ce niveau d’activité physique régulière.',
        };
    }

    return {
        level: 'warning',
        status: 'Sous l’objectif',
        note: `Activité moyenne de ${Math.round(weekly)} min/sem., inférieure à la cible OMS (150 min/sem.).`,
        recommendation: 'Encourager une augmentation progressive de l’activité physique.',
    };
}

export function interpretMeals(totalMeals: number, from: string, to: string): ClinicalInterpretation {
    const days = Math.max(1, daysBetween(from, to));
    const perDay = totalMeals / days;

    if (perDay >= 2.5) {
        return {
            level: 'info',
            status: 'Suivi régulier',
            note: `En moyenne ${perDay.toFixed(1)} repas enregistrés par jour : le suivi nutritionnel est régulier.`,
            recommendation: 'Poursuivre la tenue du journal alimentaire.',
        };
    }

    return {
        level: 'warning',
        status: 'Suivi partiel',
        note: `En moyenne ${perDay.toFixed(1)} repas par jour enregistré : la tenue du journal alimentaire est partielle.`,
        recommendation: 'Encourager l’enregistrement quotidien des repas pour mieux cibler les conseils.',
    };
}

// ─────────────────────────────────────────────────────────
// Utilitaires
// ─────────────────────────────────────────────────────────

export function daysBetween(from: string, to: string): number {
    const start = new Date(from);
    const end = new Date(to);
    if (Number.isNaN(start.getTime()) || Number.isNaN(end.getTime())) {
        return 0;
    }

    return Math.max(0, Math.round((end.getTime() - start.getTime()) / 86_400_000)) + 1;
}

export function computeAge(dateOfBirth?: string | null): number | null {
    if (!dateOfBirth) {
        return null;
    }

    const birth = new Date(dateOfBirth);
    if (Number.isNaN(birth.getTime())) {
        return null;
    }

    const now = new Date();
    let age = now.getFullYear() - birth.getFullYear();
    const monthDiff = now.getMonth() - birth.getMonth();
    if (monthDiff < 0 || (monthDiff === 0 && now.getDate() < birth.getDate())) {
        age -= 1;
    }

    return age;
}

export function levelColor(level: InterpretationLevel, colors: Record<string, [number, number, number]>): [number, number, number] {
    switch (level) {
        case 'good':
            return colors.good;
        case 'warning':
            return colors.warning;
        case 'critical':
            return colors.critical;
        default:
            return colors.info;
    }
}

export interface StatsLike { average: number | null; }
export function isMeasurable(stats: MeasurableStats): boolean {
    return stats.count > 0;
}