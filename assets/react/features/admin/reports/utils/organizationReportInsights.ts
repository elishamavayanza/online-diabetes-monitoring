// ============================================================
// organizationReportInsights.ts
// Synthèse analytique & interprétations du rapport organisationnel.
// Produit des points clés (executive summary) et des repères
// métier à partir des KPI agrégés.
// ============================================================

import { OrganizationReport } from '../types';
import {
    interpretAdherence,
    interpretBloodPressure,
    interpretBmi,
    interpretGlucose,
    interpretHbA1c,
    InterpretationLevel,
} from '@/react/features/clinician/patients/utils/followUpClinicalInterpretation';

export interface InsightItem {
    level: InterpretationLevel;
    text: string;
}

function pctString(value: number | null | undefined): string {
    return value === null || value === undefined ? '—' : `${value} %`;
}

function fmtNumber(value: number | null | undefined, decimals = 0): string {
    if (value === null || value === undefined) {
        return '—';
    }

    return Number.isInteger(value) ? String(value) : value.toFixed(decimals);
}

export function buildExecutiveSummary(report: OrganizationReport): InsightItem[] {
    const items: InsightItem[] = [];

    const { demographics, healthStatus, medicalActivity, treatments, trends } = report;

    const total = demographics.totalPatients.value;
    if (total !== null && total !== undefined) {
        const newPatients = demographics.newPatients.value;
        const share = total > 0 && newPatients !== null && newPatients !== undefined
            ? Math.round((newPatients / total) * 100)
            : null;
        items.push({
            level: 'info',
            text: `${fmtNumber(total)} patients suivis sur la période, dont ${fmtNumber(newPatients)} nouveaux patient(s) (${pctString(share)} des effectifs).`,
        });
    }

    const coverage = trends.measurementComplianceRate.value;
    if (coverage !== null && coverage !== undefined) {
        const level: InterpretationLevel = coverage >= 70 ? 'good' : coverage >= 40 ? 'warning' : 'critical';
        items.push({
            level,
            text: `Taux de suivi des mesures : ${pctString(coverage)}. ${coverage >= 70 ? 'Bon niveau de suivi des patients.' : coverage >= 40 ? 'Niveau de suivi à renforcer.' : 'Niveau de suivi faible : des actions sont nécessaires.'}`,
        });
    }

    const adherence = interpretAdherence(treatments.adherenceRate.value);
    items.push({
        level: adherence?.level ?? 'info',
        text: `Observance thérapeutique moyenne : ${pctString(treatments.adherenceRate.value)}. ${adherence?.note ?? ''}`,
    });

    const glucose = interpretGlucose(healthStatus.averageGlucose.value);
    if (glucose) {
        items.push({
            level: glucose.level,
            text: `Glycémie moyenne des patients : ${fmtNumber(healthStatus.averageGlucose.value, 1)} mg/dL — ${glucose.status}.`,
        });
    }

    const hba1c = interpretHbA1c(healthStatus.averageHbA1c.value);
    if (hba1c) {
        items.push({
            level: hba1c.level,
            text: `HbA1c moyenne : ${fmtNumber(healthStatus.averageHbA1c.value, 1)} % — ${hba1c.status}.`,
        });
    }

    const appointments = medicalActivity.totalAppointments.value;
    if (appointments !== null && appointments !== undefined) {
        const completed = medicalActivity.completedAppointments.value ?? 0;
        const completion = appointments > 0 ? Math.round((completed / appointments) * 100) : null;
        const level: InterpretationLevel = completion !== null && completion >= 70 ? 'good' : 'warning';
        items.push({
            level,
            text: `${fmtNumber(appointments)} rendez-vous planifiés, ${fmtNumber(completed)} terminés (${pctString(completion)} de réalisation).`,
        });
    }

    return items;
}

export interface SectionInsight {
    level: InterpretationLevel;
    status: string;
    note: string;
}

export function demographicsInsight(report: OrganizationReport): SectionInsight | null {
    const total = report.demographics.totalPatients.value;
    const active = report.demographics.activePatients.value;
    if (total === null || total === undefined) {
        return null;
    }

    const activeRate = active !== null && active !== undefined && total > 0
        ? Math.round((active / total) * 100)
        : null;

    const primary = [...report.demographics.genderDistribution].sort((a, b) => b.count - a.count)[0];
    const majority = primary && total > 0 ? primary.label : null;

    return {
        level: activeRate !== null && activeRate >= 80 ? 'good' : 'info',
        status: `${fmtNumber(active)} patients actifs`,
        note: `${pctString(activeRate)} des patients présentent une activité pendant la période. ${majority ? `Répartition majoritaire : ${majority}.` : ''}`,
    };
}

export function healthStatusInsight(report: OrganizationReport): SectionInsight[] {
    const insights: SectionInsight[] = [];

    const glucose = interpretGlucose(report.healthStatus.averageGlucose.value);
    if (glucose) {
        insights.push({ level: glucose.level, status: glucose.status, note: glucose.note });
    }

    const hba1c = interpretHbA1c(report.healthStatus.averageHbA1c.value);
    if (hba1c) {
        insights.push({ level: hba1c.level, status: hba1c.status, note: hba1c.note });
    }

    const bp = interpretBloodPressure(report.healthStatus.averageSystolic.value, report.healthStatus.averageDiastolic.value);
    if (bp) {
        insights.push({ level: bp.level, status: bp.status, note: bp.note });
    }

    const bmi = interpretBmi(report.healthStatus.averageBmi.value);
    if (bmi) {
        insights.push({ level: bmi.level, status: bmi.status, note: bmi.note });
    }

    return insights;
}

export function medicalActivityInsight(report: OrganizationReport): SectionInsight | null {
    const total = report.medicalActivity.totalAppointments.value;
    if (total === null || total === undefined) {
        return null;
    }

    const completed = report.medicalActivity.completedAppointments.value ?? 0;
    const cancelled = report.medicalActivity.cancelledAppointments.value ?? 0;
    const completion = total > 0 ? Math.round((completed / total) * 100) : null;

    return {
        level: completion !== null && completion >= 70 ? 'good' : 'warning',
        status: `${completion !== null ? completion : '—'} % de réalisation`,
        note: `${fmtNumber(total)} rendez-vous planifiés, ${fmtNumber(completed)} terminés et ${fmtNumber(cancelled)} annulés sur la période.`,
    };
}

export function treatmentInsight(report: OrganizationReport): SectionInsight | null {
    const adherence = interpretAdherence(report.treatments.adherenceRate.value);
    if (!adherence) {
        return null;
    }

    return {
        level: adherence.level,
        status: adherence.status,
        note: adherence.note,
    };
}

export function lifestyleInsight(report: OrganizationReport): SectionInsight | null {
    const sessions = report.lifestyle.physicalActivitySessions.value;
    const minutes = report.lifestyle.totalActivityMinutes.value;
    if (sessions === null || sessions === undefined || minutes === null || minutes === undefined) {
        return null;
    }

    const avg = sessions > 0 ? Math.round((minutes / sessions)) : null;

    return {
        level: 'info',
        status: `${fmtNumber(sessions)} séances`,
        note: `${fmtNumber(minutes)} minutes d’activité cumulées, soit ${avg !== null ? avg + ' min' : '—'} en moyenne par séance.`,
    };
}

export interface GlossaryEntry {
    term: string;
    definition: string;
}

export const ADMIN_GLOSSARY: GlossaryEntry[] = [
    { term: 'HbA1c', definition: 'Hémoglobine glyquée : reflet du contrôle glycémique moyen des 2 à 3 derniers mois.' },
    { term: 'IMC', definition: 'Indice de masse corporelle : poids (kg) divisé par le carré de la taille (m²).' },
    { term: 'Observance thérapeutique', definition: 'Degré de conformité entre le comportement du patient et la prescription médicale.' },
    { term: 'Taux de suivi', definition: 'Part des patients ayant enregistré au moins une mesure sur la période.' },
    { term: 'Rendez-vous planifié', definition: 'Créneau réservé quel que soit son issue (terminé, annulé, absent…).' },
];

export { pctString as pctStringForInsights, fmtNumber as fmtNumberForInsights };