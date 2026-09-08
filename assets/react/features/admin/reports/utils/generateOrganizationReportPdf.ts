import { jsPDF } from 'jspdf';
import autoTable from 'jspdf-autotable';
import {
    DistributionItem,
    OrganizationReport,
    StatisticValue,
    TrendSeries,
} from '../types';
import { ReportSectionId } from '../config/reportSections';
import { PDF_BRAND, hexToRgb } from '../config/pdfBrand';
import { loadPdfAssets } from './pdfAssets';
import { formatChange, formatLabel, formatStatValue } from '../utils/formatters';
import {
    ADMIN_GLOSSARY,
    buildExecutiveSummary,
    demographicsInsight,
    healthStatusInsight,
    lifestyleInsight,
    medicalActivityInsight,
    SectionInsight,
    treatmentInsight,
} from './organizationReportInsights';
import { InterpretationLevel } from '@/react/features/clinician/patients/utils/followUpClinicalInterpretation';

const COLORS = {
    primary: hexToRgb(PDF_BRAND.primary),
    secondary: hexToRgb(PDF_BRAND.secondary),
    background: hexToRgb(PDF_BRAND.background),
    surface: hexToRgb(PDF_BRAND.surface),
    text: hexToRgb(PDF_BRAND.text),
    muted: hexToRgb(PDF_BRAND.muted),
    border: hexToRgb(PDF_BRAND.border),
    accent: hexToRgb(PDF_BRAND.accent),
    white: [255, 255, 255] as [number, number, number],
    good: hexToRgb('#2F8F5B'),
    warning: hexToRgb('#D69E2E'),
    critical: hexToRgb('#C85246'),
    info: hexToRgb('#3E6F9E'),
};

const MARGIN = 16;
const PAGE_WIDTH = 210;
const PAGE_HEIGHT = 297;
const CONTENT_WIDTH = PAGE_WIDTH - MARGIN * 2;
const FOOTER_Y = 272;
const QR_SIZE = 18;
const QR_X = PAGE_WIDTH - MARGIN - QR_SIZE;
const QR_Y = PAGE_HEIGHT - MARGIN - QR_SIZE;
const CONTENT_BOTTOM = FOOTER_Y - 4;
const TABLE_MARGIN = { left: MARGIN, right: MARGIN, bottom: PAGE_HEIGHT - CONTENT_BOTTOM };

interface PdfAssets {
    logoDataUrl: string;
    qrDataUrl: string;
    reference: string;
    verificationUrl: string;
}

function formatFrenchDate(value: string): string {
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) {
        return value;
    }

    return new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    }).format(date);
}

function formatFrenchDateTime(value: string): string {
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) {
        return value;
    }

    return new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(date);
}

function statRow(label: string, stat: StatisticValue): [string, string, string, string] {
    return [
        label,
        formatStatValue(stat),
        stat.previousValue !== null && stat.previousValue !== undefined
            ? formatStatValue({ value: stat.previousValue, unit: stat.unit })
            : '—',
        formatChange(stat.changePercent),
    ];
}

function distributionRows(items: DistributionItem[]): string[][] {
    if (!items.length) {
        return [['Aucune donnée disponible', '—', '—']];
    }

    return items.map((item) => [
        formatLabel(item.label),
        String(item.count),
        `${item.percentage}%`,
    ]);
}

function trendRows(series: TrendSeries): string[][] {
    if (!series.points.length) {
        return [['Aucune donnée disponible', '—']];
    }

    return series.points.map((point) => [
        formatFrenchDate(point.date),
        series.unit ? `${point.value} ${series.unit}` : String(point.value),
    ]);
}

class ReportPdfBuilder {
    private doc: jsPDF;
    private y = MARGIN + 18;

    constructor(
        private readonly report: OrganizationReport,
        private readonly assets: PdfAssets,
    ) {
        this.doc = new jsPDF({ unit: 'mm', format: 'a4' });
    }

    async build(sections: ReportSectionId[]): Promise<jsPDF> {
        this.drawCover(sections);
        this.drawSectionPages(sections);
        this.drawAppendices();
        this.addHeadersAndFootersToAllPages();
        return this.doc;
    }

    private ensureSpace(requiredHeight: number): void {
        if (this.y + requiredHeight <= CONTENT_BOTTOM) {
            return;
        }

        this.doc.addPage();
        this.drawBrandHeader(22, false);
        this.y = MARGIN + 22;
    }

    private drawBrandHeader(height = 34, largeLogo = false): void {
        this.doc.setFillColor(...COLORS.primary);
        this.doc.rect(0, 0, PAGE_WIDTH, height, 'F');

        this.doc.setFillColor(...COLORS.secondary);
        this.doc.rect(0, height - 2, PAGE_WIDTH, 2, 'F');

        const logoSize = largeLogo ? 22 : 14;
        const logoY = largeLogo ? 9 : (height - logoSize) / 2;

        this.doc.setFillColor(...COLORS.white);
        this.doc.roundedRect(MARGIN, logoY, logoSize, logoSize, 2, 2, 'F');

        this.doc.addImage(
            this.assets.logoDataUrl,
            'PNG',
            MARGIN + 1,
            logoY + 1,
            logoSize - 2,
            logoSize - 2,
            undefined,
            'FAST'
        );

        const textX = MARGIN + logoSize + 6;
        this.doc.setTextColor(...COLORS.white);
        this.doc.setFont('helvetica', 'bold');
        this.doc.setFontSize(largeLogo ? 14 : 10);
        this.doc.text(PDF_BRAND.name, textX, largeLogo ? 18 : height / 2 + 1);

        if (largeLogo) {
            this.doc.setFont('helvetica', 'normal');
            this.doc.setFontSize(9);
            this.doc.text(PDF_BRAND.tagline, textX, 24);
        }

        this.doc.setFont('helvetica', 'bold');
        this.doc.setFontSize(largeLogo ? 10 : 8);
        this.doc.text('DOCUMENT ADMINISTRATIF CONFIDENTIEL', PAGE_WIDTH - MARGIN, largeLogo ? 16 : 8, {
            align: 'right',
        });

        this.doc.setFont('helvetica', 'normal');
        this.doc.setFontSize(largeLogo ? 9 : 7.5);
        this.doc.text(this.assets.reference, PAGE_WIDTH - MARGIN, largeLogo ? 22 : 13, {
            align: 'right',
        });
    }

    private drawCover(sections: ReportSectionId[]): void {
        const { report } = this;

        this.drawBrandHeader(44, true);

        this.y = 56;
        this.doc.setTextColor(...COLORS.text);
        this.doc.setFont('helvetica', 'bold');
        this.doc.setFontSize(20);
        this.doc.text('Rapport analytique organisationnel', MARGIN, this.y);

        this.y += 8;
        this.doc.setFontSize(13);
        this.doc.setTextColor(...COLORS.primary);
        this.doc.text(report.organizationName, MARGIN, this.y);

        this.y += 5;
        this.doc.setFont('helvetica', 'normal');
        this.doc.setFontSize(10);
        this.doc.setTextColor(...COLORS.muted);
        this.doc.text(PDF_BRAND.tagline, MARGIN, this.y);

        this.y += 12;
        this.doc.setDrawColor(...COLORS.border);
        this.doc.setFillColor(...COLORS.surface);
        this.doc.roundedRect(MARGIN, this.y, CONTENT_WIDTH, 34, 2, 2, 'FD');

        this.doc.setTextColor(...COLORS.text);
        this.doc.setFont('helvetica', 'bold');
        this.doc.setFontSize(10);
        this.doc.text('Synthèse administrative', MARGIN + 5, this.y + 8);
        this.doc.setFont('helvetica', 'normal');
        this.doc.setFontSize(9);
        this.doc.text(
            `Période : ${formatFrenchDate(report.period.from)} au ${formatFrenchDate(report.period.to)}`,
            MARGIN + 5,
            this.y + 15
        );
        this.doc.text(
            `Comparaison : ${formatFrenchDate(report.period.previousFrom)} au ${formatFrenchDate(report.period.previousTo)}`,
            MARGIN + 5,
            this.y + 21
        );
        this.doc.text(`Généré le ${formatFrenchDateTime(report.generatedAt)}`, MARGIN + 5, this.y + 27);

        this.y += 42;
        autoTable(this.doc, {
            startY: this.y,
            margin: TABLE_MARGIN,
            theme: 'grid',
            styles: {
                fontSize: 9,
                cellPadding: 3,
                lineColor: COLORS.border,
                textColor: COLORS.text,
            },
            body: [
                ['Référence document', this.assets.reference],
                ['Organisation', report.organizationName],
                ['Identifiant organisation', report.organizationId],
                ['Sections exportées', String(sections.length)],
            ],
            columnStyles: {
                0: { fontStyle: 'bold', cellWidth: 52, fillColor: COLORS.background, textColor: COLORS.primary },
                1: { fillColor: COLORS.white },
            },
        });

        this.y = (this.doc as jsPDF & { lastAutoTable: { finalY: number } }).lastAutoTable.finalY + 10;

        this.drawExecutiveSummary();

        this.ensureSpace(14);
        this.doc.setFont('helvetica', 'bold');
        this.doc.setFontSize(12);
        this.doc.setTextColor(...COLORS.primary);
        this.doc.text('Sommaire des sections', MARGIN, this.y);
        this.y += 5;

        const sectionTitles: Record<ReportSectionId, string> = {
            demographics: '1. Patients & démographie',
            healthStatus: '2. État de santé',
            medicalActivity: '3. Activité médicale',
            treatments: '4. Traitements & observance',
            lifestyle: '5. Nutrition & activité physique',
            trends: '6. Tendances & indicateurs',
        };

        autoTable(this.doc, {
            startY: this.y,
            margin: TABLE_MARGIN,
            head: [['Section', 'Description']],
            body: sections.map((id) => {
                const meta = {
                    demographics: 'Effectifs et répartition démographique',
                    healthStatus: 'Indicateurs cliniques agrégés',
                    medicalActivity: 'Consultations et dossiers médicaux',
                    treatments: 'Prescriptions et observance thérapeutique',
                    lifestyle: 'Nutrition et activité physique',
                    trends: 'Évolutions et taux de suivi',
                };

                return [sectionTitles[id], meta[id]];
            }),
            styles: { fontSize: 9, cellPadding: 3, lineColor: COLORS.border },
            headStyles: {
                fillColor: COLORS.primary,
                textColor: COLORS.white,
                fontStyle: 'bold',
            },
            alternateRowStyles: { fillColor: COLORS.background },
        });

        this.y = (this.doc as jsPDF & { lastAutoTable: { finalY: number } }).lastAutoTable.finalY + 8;
        this.doc.setFont('helvetica', 'italic');
        this.doc.setFontSize(8.5);
        this.doc.setTextColor(...COLORS.muted);
        const notice = 'Ce document est généré automatiquement à partir des données agrégées de l\'organisation. '
            + 'Usage administratif interne uniquement. Ne constitue pas un avis médical. '
            + 'Le QR code présent sur chaque page permet de vérifier l\'authenticité du rapport.';
        const noticeLines = this.doc.splitTextToSize(notice, CONTENT_WIDTH - QR_SIZE - 4) as string[];
        this.ensureSpace(noticeLines.length * 4 + 4);
        noticeLines.forEach((line, index) => {
            this.doc.text(line, MARGIN, this.y + index * 4);
        });
    }

    private drawSectionPages(sections: ReportSectionId[]): void {
        sections.forEach((sectionId) => {
            this.doc.addPage();
            this.y = MARGIN + 22;

            switch (sectionId) {
                case 'demographics':
                    this.drawDemographics();
                    break;
                case 'healthStatus':
                    this.drawHealthStatus();
                    break;
                case 'medicalActivity':
                    this.drawMedicalActivity();
                    break;
                case 'treatments':
                    this.drawTreatments();
                    break;
                case 'lifestyle':
                    this.drawLifestyle();
                    break;
                case 'trends':
                    this.drawTrends();
                    break;
            }
        });
    }

    private drawSectionHeader(title: string, subtitle: string): void {
        this.doc.setFillColor(...COLORS.background);
        this.doc.roundedRect(MARGIN, this.y - 5, CONTENT_WIDTH, 16, 2, 2, 'F');
        this.doc.setDrawColor(...COLORS.primary);
        this.doc.setLineWidth(0.4);
        this.doc.line(MARGIN, this.y - 5, MARGIN, this.y + 11);

        this.doc.setTextColor(...COLORS.primary);
        this.doc.setFont('helvetica', 'bold');
        this.doc.setFontSize(13);
        this.doc.text(title, MARGIN + 4, this.y + 2);

        this.y += 10;
        this.doc.setTextColor(...COLORS.muted);
        this.doc.setFont('helvetica', 'normal');
        this.doc.setFontSize(9);
        this.doc.text(subtitle, MARGIN + 4, this.y);
        this.y += 8;
    }

    private drawKpiTable(title: string, rows: [string, string, string, string][]): void {
        this.ensureSpace(8);
        this.doc.setTextColor(...COLORS.text);
        this.doc.setFont('helvetica', 'bold');
        this.doc.setFontSize(10);
        this.doc.text(title, MARGIN, this.y);
        this.y += 4;

        autoTable(this.doc, {
            startY: this.y,
            margin: TABLE_MARGIN,
            head: [['Indicateur', 'Valeur', 'Période précédente', 'Évolution']],
            body: rows,
            styles: { fontSize: 9, cellPadding: 3, lineColor: COLORS.border, textColor: COLORS.text },
            headStyles: {
                fillColor: COLORS.primary,
                textColor: COLORS.white,
                fontStyle: 'bold',
            },
            alternateRowStyles: { fillColor: COLORS.background },
        });

        this.y = (this.doc as jsPDF & { lastAutoTable: { finalY: number } }).lastAutoTable.finalY + 8;
    }

    private drawDistributionTable(title: string, items: DistributionItem[]): void {
        this.ensureSpace(8);
        this.doc.setFont('helvetica', 'bold');
        this.doc.setFontSize(10);
        this.doc.setTextColor(...COLORS.text);
        this.doc.text(title, MARGIN, this.y);
        this.y += 4;

        autoTable(this.doc, {
            startY: this.y,
            margin: TABLE_MARGIN,
            head: [['Catégorie', 'Effectif', 'Part']],
            body: distributionRows(items),
            styles: { fontSize: 9, cellPadding: 3, lineColor: COLORS.border },
            headStyles: {
                fillColor: COLORS.secondary,
                textColor: COLORS.white,
                fontStyle: 'bold',
            },
            alternateRowStyles: { fillColor: COLORS.surface },
        });

        this.y = (this.doc as jsPDF & { lastAutoTable: { finalY: number } }).lastAutoTable.finalY + 8;
    }

    private drawDemographics(): void {
        const { demographics } = this.report;
        this.drawSectionHeader(
            'Patients & démographie',
            'Vue consolidée des patients rattachés à l\'organisation'
        );

        this.drawKpiTable('Indicateurs principaux', [
            statRow('Patients total', demographics.totalPatients),
            statRow('Patients actifs', demographics.activePatients),
            statRow('Nouveaux patients', demographics.newPatients),
        ]);

        this.drawDistributionTable('Répartition par genre', demographics.genderDistribution);
        this.drawDistributionTable('Répartition par tranche d\'âge', demographics.ageGroups);

        this.drawInsightBox(demographicsInsight(this.report));
    }

    private drawHealthStatus(): void {
        const { healthStatus } = this.report;
        this.drawSectionHeader(
            'État de santé',
            'Mesures cliniques agrégées sur la période sélectionnée'
        );

        this.drawKpiTable('Indicateurs cliniques', [
            statRow('Glycémie moyenne', healthStatus.averageGlucose),
            statRow('Nombre de mesures glycémiques', healthStatus.glucoseMeasurements),
            statRow('HbA1c moyenne', healthStatus.averageHbA1c),
            statRow('Mesures HbA1c', healthStatus.hba1cMeasurements),
            statRow('Tension systolique moyenne', healthStatus.averageSystolic),
            statRow('Tension diastolique moyenne', healthStatus.averageDiastolic),
            statRow('IMC moyen', healthStatus.averageBmi),
            statRow('Poids moyen', healthStatus.averageWeightKg),
        ]);

        this.drawDistributionTable('Répartition glycémique', healthStatus.glucoseRanges);

        healthStatusInsight(this.report).forEach((insight) => {
            this.drawInsightBox(insight);
        });
    }

    private drawMedicalActivity(): void {
        const { medicalActivity } = this.report;
        this.drawSectionHeader(
            'Activité médicale',
            'Suivi des consultations, diagnostics et dossiers médicaux'
        );

        this.drawKpiTable('Activité', [
            statRow('Rendez-vous total', medicalActivity.totalAppointments),
            statRow('Rendez-vous terminés', medicalActivity.completedAppointments),
            statRow('Rendez-vous annulés', medicalActivity.cancelledAppointments),
            statRow('Diagnostics posés', medicalActivity.diagnosesCount),
            statRow('Dossiers ouverts', medicalActivity.openMedicalRecords),
            statRow('Dossiers fermés', medicalActivity.closedMedicalRecords),
        ]);

        this.drawDistributionTable('Rendez-vous par statut', medicalActivity.appointmentsByStatus);

        this.drawInsightBox(medicalActivityInsight(this.report));
    }

    private drawTreatments(): void {
        const { treatments } = this.report;
        this.drawSectionHeader(
            'Traitements & observance',
            'Analyse des prescriptions et de la prise des médicaments'
        );

        this.drawKpiTable('Traitements', [
            statRow('Prescriptions actives', treatments.activePrescriptions),
            statRow('Nouvelles prescriptions', treatments.newPrescriptions),
            statRow('Taux d\'observance', treatments.adherenceRate),
            statRow('Prises enregistrées', treatments.totalIntakes),
        ]);

        this.drawDistributionTable('Répartition des prises', treatments.intakesByStatus);

        this.drawInsightBox(treatmentInsight(this.report));
    }

    private drawLifestyle(): void {
        const { lifestyle } = this.report;
        this.drawSectionHeader(
            'Nutrition & activité physique',
            'Données de suivi nutritionnel et d\'activité des patients'
        );

        this.drawKpiTable('Mode de vie', [
            statRow('Repas enregistrés', lifestyle.totalMeals),
            statRow('Séances d\'activité physique', lifestyle.physicalActivitySessions),
            statRow('Minutes d\'activité totales', lifestyle.totalActivityMinutes),
            statRow('Durée moyenne par séance', lifestyle.averageActivityMinutes),
        ]);

        this.drawDistributionTable('Repas par type', lifestyle.mealsByType);

        this.drawInsightBox(lifestyleInsight(this.report));
    }

    private drawTrends(): void {
        const { trends } = this.report;
        this.drawSectionHeader(
            'Tendances & indicateurs',
            'Évolution des indicateurs et taux de suivi des patients'
        );

        this.drawKpiTable('Indicateurs de suivi', [
            statRow('Patients avec mesures', trends.patientsWithMeasurements),
            statRow('Taux de suivi', trends.measurementComplianceRate),
        ]);

        trends.series.forEach((series) => {
            this.ensureSpace(8);
            this.doc.setFont('helvetica', 'bold');
            this.doc.setFontSize(10);
            this.doc.setTextColor(...COLORS.text);
            this.doc.text(series.label, MARGIN, this.y);
            this.y += 4;

            autoTable(this.doc, {
                startY: this.y,
                margin: TABLE_MARGIN,
                head: [['Date', 'Valeur']],
                body: trendRows(series),
                styles: { fontSize: 9, cellPadding: 3, lineColor: COLORS.border },
                headStyles: {
                    fillColor: COLORS.secondary,
                    textColor: COLORS.white,
                },
            });

            this.y = (this.doc as jsPDF & { lastAutoTable: { finalY: number } }).lastAutoTable.finalY + 8;
        });
    }

    private levelRgb(level: InterpretationLevel): [number, number, number] {
        switch (level) {
            case 'good':
                return COLORS.good;
            case 'warning':
                return COLORS.warning;
            case 'critical':
                return COLORS.critical;
            default:
                return COLORS.info;
        }
    }

    private drawExecutiveSummary(): void {
        const items = buildExecutiveSummary(this.report);
        if (!items.length) {
            return;
        }

        this.ensureSpace(22);
        this.doc.setFont('helvetica', 'bold');
        this.doc.setFontSize(12);
        this.doc.setTextColor(...COLORS.primary);
        this.doc.text('Points clés de la période', MARGIN, this.y);
        this.y += 5;

        items.forEach((item) => {
            const lines = this.doc.splitTextToSize(item.text, CONTENT_WIDTH - 14) as string[];
            this.ensureSpace(lines.length * 4 + 4);

            this.doc.setFillColor(...this.levelRgb(item.level));
            this.doc.circle(MARGIN + 2, this.y - 1.2, 1.4, 'F');

            this.doc.setFont('helvetica', 'normal');
            this.doc.setFontSize(9);
            this.doc.setTextColor(...COLORS.text);
            lines.forEach((line, index) => {
                this.doc.text(line, MARGIN + 7, this.y + index * 4);
            });

            this.y += lines.length * 4 + 3;
        });
    }

    private drawInsightBox(insight: SectionInsight | null): void {
        if (!insight) {
            return;
        }

        const color = this.levelRgb(insight.level);
        const noteLines = this.doc.splitTextToSize(insight.note, CONTENT_WIDTH - 22) as string[];
        const boxHeight = 12 + noteLines.length * 4;

        this.ensureSpace(boxHeight + 4);

        this.doc.setFillColor(...COLORS.surface);
        this.doc.roundedRect(MARGIN, this.y, CONTENT_WIDTH, boxHeight, 2, 2, 'F');
        this.doc.setFillColor(...color);
        this.doc.roundedRect(MARGIN, this.y, 2.5, boxHeight, 1, 1, 'F');

        const statusWidth = this.doc.getTextWidth(insight.status) + 8;
        this.doc.setFillColor(...color);
        this.doc.roundedRect(MARGIN + 6, this.y + 3, statusWidth, 6.5, 3.2, 3.2, 'F');
        this.doc.setTextColor(...COLORS.white);
        this.doc.setFont('helvetica', 'bold');
        this.doc.setFontSize(7.5);
        this.doc.text(insight.status, MARGIN + 10, this.y + 7.5);

        this.doc.setTextColor(...COLORS.text);
        this.doc.setFont('helvetica', 'normal');
        this.doc.setFontSize(8.5);
        noteLines.forEach((line, index) => {
            this.doc.text(line, MARGIN + 9, this.y + 13 + index * 4);
        });

        this.y += boxHeight + 8;
    }

    private drawAppendices(): void {
        this.doc.addPage();
        this.y = MARGIN + 22;
        this.drawSectionHeader(
            'Annexe — Méthodologie & définitions',
            'Périmètre, sources et références utilisées pour le calcul des indicateurs',
        );

        const { report } = this;
        const periodText = 'Ce rapport agrège les données médicales et administratives des patients rattachés à '
            + `l'organisation sur la période du ${formatFrenchDate(report.period.from)} au ${formatFrenchDate(report.period.to)}. `
            + `Les évolutions sont calculées par comparaison avec la période précédente (${formatFrenchDate(report.period.previousFrom)} `
            + `au ${formatFrenchDate(report.period.previousTo)}).`;

        this.doc.setTextColor(...COLORS.text);
        this.doc.setFont('helvetica', 'bold');
        this.doc.setFontSize(10);
        this.doc.text('Méthodologie', MARGIN, this.y);
        this.y += 4;

        const periodLines = this.doc.splitTextToSize(periodText, CONTENT_WIDTH) as string[];
        this.doc.setFont('helvetica', 'normal');
        this.doc.setFontSize(8.5);
        this.doc.setTextColor(...COLORS.muted);
        periodLines.forEach((line) => {
            this.doc.text(line, MARGIN, this.y);
            this.y += 4;
        });
        this.y += 4;

        const methodPoints = [
            'Les moyennes sont calculées à partir des mesures non supprimées enregistrées sur la période.',
            'Les taux d\'observance et de suivi sont rapportés aux flux de prescriptions et aux effectifs de patients actifs.',
            'Les parts (%) des distributions sont arrondies à l\'unité ; la somme peut ne pas être exactement égale à 100 %.',
            'Les mesures cliniques sont agrégées en valeurs moyennes ; elles ne décrivent ni profil individuel ni prévalence.',
        ];

        methodPoints.forEach((point) => {
            const lines = this.doc.splitTextToSize(point, CONTENT_WIDTH - 6) as string[];
            this.ensureSpace(lines.length * 4 + 2);
            this.doc.setFillColor(...COLORS.primary);
            this.doc.circle(MARGIN + 1.5, this.y - 1.2, 1, 'F');
            lines.forEach((line, index) => {
                this.doc.text(line, MARGIN + 5, this.y + index * 4);
            });
            this.y += lines.length * 4 + 2;
        });

        this.y += 4;
        this.ensureSpace(16);
        this.doc.setTextColor(...COLORS.text);
        this.doc.setFont('helvetica', 'bold');
        this.doc.setFontSize(10);
        this.doc.text('Glossaire', MARGIN, this.y);
        this.y += 4;

        autoTable(this.doc, {
            startY: this.y,
            margin: TABLE_MARGIN,
            head: [['Terme', 'Définition']],
            body: ADMIN_GLOSSARY.map((entry) => [entry.term, entry.definition]),
            styles: { fontSize: 8.5, cellPadding: 2.5, lineColor: COLORS.border },
            headStyles: { fillColor: COLORS.primary, textColor: COLORS.white, fontStyle: 'bold' },
            alternateRowStyles: { fillColor: COLORS.background },
        });
        this.y = (this.doc as jsPDF & { lastAutoTable: { finalY: number } }).lastAutoTable.finalY + 6;

        this.ensureSpace(24);
        const disclaimer = 'Ce document est établi à partir de données agrégées anonymisées, à usage de pilotage interne. '
            + 'Il ne constitue ni un avis médical individuel ni un document à visée de publication. '
            + 'Le QR code figurant sur chaque page permet de vérifier l\'authenticité du rapport.';
        const disclaimerLines = this.doc.splitTextToSize(disclaimer, CONTENT_WIDTH - QR_SIZE - 4) as string[];
        this.doc.setFont('helvetica', 'italic');
        this.doc.setFontSize(8.5);
        this.doc.setTextColor(...COLORS.muted);
        disclaimerLines.forEach((line) => {
            this.doc.text(line, MARGIN, this.y);
            this.y += 4;
        });
    }

    private addHeadersAndFootersToAllPages(): void {
        const totalPages = this.doc.getNumberOfPages();

        for (let page = 1; page <= totalPages; page += 1) {
            this.doc.setPage(page);

            if (page > 1) {
                this.drawBrandHeader(22, false);
            }

            this.doc.setDrawColor(...COLORS.border);
            this.doc.setLineWidth(0.3);
            this.doc.line(MARGIN, FOOTER_Y, PAGE_WIDTH - MARGIN - QR_SIZE - 4, FOOTER_Y);

            this.doc.setFont('helvetica', 'normal');
            this.doc.setFontSize(7.5);
            this.doc.setTextColor(...COLORS.muted);
            this.doc.text(
                `${PDF_BRAND.name} — ${this.report.organizationName}`,
                MARGIN,
                FOOTER_Y + 5
            );
            this.doc.text(
                'Document confidentiel — usage administratif interne',
                MARGIN,
                FOOTER_Y + 9
            );
            this.doc.text(
                `Page ${page} / ${totalPages}`,
                MARGIN,
                FOOTER_Y + 13
            );

            this.doc.setDrawColor(...COLORS.primary);
            this.doc.setLineWidth(0.5);
            this.doc.rect(QR_X - 1, QR_Y - 1, QR_SIZE + 2, QR_SIZE + 2);

            this.doc.addImage(
                this.assets.qrDataUrl,
                'PNG',
                QR_X,
                QR_Y,
                QR_SIZE,
                QR_SIZE,
                undefined,
                'FAST'
            );

            this.doc.setFont('helvetica', 'bold');
            this.doc.setFontSize(6.5);
            this.doc.setTextColor(...COLORS.primary);
            this.doc.text('Vérification', QR_X + QR_SIZE / 2, QR_Y - 2, { align: 'center' });
            this.doc.setFont('helvetica', 'normal');
            this.doc.setFontSize(6);
            this.doc.setTextColor(...COLORS.muted);
            this.doc.text(this.assets.reference, QR_X + QR_SIZE / 2, QR_Y + QR_SIZE + 4, { align: 'center' });
        }
    }
}

export async function generateOrganizationReportPdf(
    report: OrganizationReport,
    sections: ReportSectionId[]
): Promise<jsPDF> {
    if (!sections.length) {
        throw new Error('Sélectionnez au moins une section à exporter.');
    }

    const assets = await loadPdfAssets(report);
    const builder = new ReportPdfBuilder(report, assets);
    return builder.build(sections);
}

export async function downloadOrganizationReportPdf(
    report: OrganizationReport,
    sections: ReportSectionId[],
    filename: string
): Promise<void> {
    const doc = await generateOrganizationReportPdf(report, sections);
    doc.save(filename);
}
