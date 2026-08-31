const CONSENT_TYPE_LABELS: Record<string, string> = {
    DATA_PROCESSING: 'Traitement des données',
    TELEMONITORING: 'Télémonitoring',
    DATA_SHARING_WITH_ORG: 'Partage avec l\'organisation',
};

const ALLERGY_SEVERITY_LABELS: Record<string, string> = {
    MILD: 'Légère',
    MODERATE: 'Modérée',
    SEVERE: 'Sévère',
};

const DIAGNOSIS_STATUS_LABELS: Record<string, string> = {
    CONFIRMED: 'Confirmé',
    SUSPECTED: 'Suspecté',
    RULED_OUT: 'Écarté',
    IN_REMISSION: 'En rémission',
};

const MEAL_TYPE_LABELS: Record<string, string> = {
    BREAKFAST: 'Petit-déjeuner',
    LUNCH: 'Déjeuner',
    DINNER: 'Dîner',
    SNACK: 'Collation',
};

export function getConsentTypeLabel(type?: string): string {
    if (!type) return '—';
    return CONSENT_TYPE_LABELS[type] ?? type;
}

export function getAllergySeverityLabel(severity?: string): string {
    if (!severity) return '—';
    return ALLERGY_SEVERITY_LABELS[severity] ?? severity;
}

export function getDiagnosisStatusLabel(status?: string): string {
    if (!status) return '—';
    return DIAGNOSIS_STATUS_LABELS[status] ?? status;
}

export function getMealTypeLabel(type?: string): string {
    if (!type) return '—';
    return MEAL_TYPE_LABELS[type] ?? type;
}

export function formatSchedule(morning: boolean, noon: boolean, evening: boolean): string {
    const parts: string[] = [];
    if (morning) parts.push('Matin');
    if (noon) parts.push('Midi');
    if (evening) parts.push('Soir');
    return parts.length > 0 ? parts.join(', ') : '—';
}
