import { jsPDF } from 'jspdf';
import autoTable from 'jspdf-autotable';
import { PDF_BRAND, hexToRgb } from '@/react/features/admin/reports/config/pdfBrand';
import { FollowUpReportElementId, PatientFollowUpReport, ReportMeasurementStats, TrendSeries } from '../types/followUpReport';
import { FollowUpReportPdfAssets, loadFollowUpReportPdfAssets } from './followUpReportPdfAssets';

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
};

const MARGIN = 16;
const PAGE_WIDTH = 210;
const PAGE_HEIGHT = 297;
const CONTENT_WIDTH = PAGE_WIDTH - MARGIN * 2;
const FOOTER_Y = 272;
const FOOTER_RESERVED_HEIGHT = 48;
const CONTENT_MAX_Y = PAGE_HEIGHT - FOOTER_RESERVED_HEIGHT;
const QR_SIZE = 18;
const QR_X = PAGE_WIDTH - MARGIN - QR_SIZE;
const QR_Y = PAGE_HEIGHT - MARGIN - QR_SIZE;
const PATIENT_PHOTO_SIZE = 28;
const NOTICE_LINE_HEIGHT = 3.6;

function getTablePageMargins() {
    return {
        left: MARGIN,
        right: MARGIN,
        bottom: FOOTER_RESERVED_HEIGHT,
    };
}

const ELEMENT_LABELS: Record<FollowUpReportElementId, string> = {
    glucose: 'Glycémie',
    hba1c: 'HbA1c',
    blood_pressure: 'Tension artérielle',
    weight: 'Poids / IMC',
    treatment: 'Traitement / observance',
    physical_activity: 'Activité physique',
    nutrition: 'Repas / nutrition',
    laboratory: 'Résultats de laboratoire',
};

const ELEMENT_DESCRIPTIONS: Record<FollowUpReportElementId, string> = {
    glucose: 'Statistiques glycémiques et évolution sur la période',
    hba1c: 'Valeurs HbA1c et tendance d\'évolution',
    blood_pressure: 'Tension systolique et diastolique',
    weight: 'Poids, IMC et courbes d\'évolution',
    treatment: 'Prescriptions actives et observance thérapeutique',
    physical_activity: 'Durée et fréquence des activités enregistrées',
    nutrition: 'Repas enregistrés par type',
    laboratory: 'Examens de laboratoire de la période',
};

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

function formatStatValue(stats: ReportMeasurementStats): string {
    if (stats.average === null || stats.average === undefined) {
        return '—';
    }

    return stats.unit ? `${stats.average} ${stats.unit}` : String(stats.average);
}

function statsRows(label: string, stats: ReportMeasurementStats): string[][] {
    if (stats.count === 0) {
        return [[`${label}`, 'Aucune donnée sur la période']];
    }

    return [
        [`${label} — Moyenne`, formatStatValue(stats)],
        [`${label} — Minimum`, stats.minimum !== null ? (stats.unit ? `${stats.minimum} ${stats.unit}` : String(stats.minimum)) : '—'],
        [`${label} — Maximum`, stats.maximum !== null ? (stats.unit ? `${stats.maximum} ${stats.unit}` : String(stats.maximum)) : '—'],
        [`${label} — Nombre de mesures`, String(stats.count)],
    ];
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

function drawSimpleTrendChart(doc: jsPDF, y: number, series: TrendSeries): number {
    const points = series.points;
    if (points.length < 2) {
        return y;
    }

    const chartHeight = 30;
    const chartWidth = CONTENT_WIDTH;
    const values = points.map((point) => point.value);
    const min = Math.min(...values);
    const max = Math.max(...values);
    const range = max - min || 1;

    doc.setDrawColor(...COLORS.border);
    doc.setFillColor(...COLORS.surface);
    doc.roundedRect(MARGIN, y, chartWidth, chartHeight, 2, 2, 'FD');

    doc.setDrawColor(...COLORS.primary);
    doc.setLineWidth(0.4);

    points.forEach((point, index) => {
        const x = MARGIN + (index / (points.length - 1)) * chartWidth;
        const normalized = (point.value - min) / range;
        const pointY = y + chartHeight - 4 - normalized * (chartHeight - 8);

        if (index > 0) {
            const prev = points[index - 1];
            const prevX = MARGIN + ((index - 1) / (points.length - 1)) * chartWidth;
            const prevNormalized = (prev.value - min) / range;
            const prevY = y + chartHeight - 4 - prevNormalized * (chartHeight - 8);
            doc.line(prevX, prevY, x, pointY);
        }
    });

    return y + chartHeight + 6;
}

class PatientFollowUpPdfBuilder {
    private doc: jsPDF;
    private y = MARGIN + 18;

    constructor(
        private readonly report: PatientFollowUpReport,
        private readonly assets: FollowUpReportPdfAssets,
    ) {
        this.doc = new jsPDF({ unit: 'mm', format: 'a4' });
    }

    build(elements: FollowUpReportElementId[]): jsPDF {
        this.drawCover(elements);
        elements.forEach((element, index) => {
            this.doc.addPage();
            this.y = MARGIN + 22;
            this.drawSectionHeader(
                `${index + 1}. ${ELEMENT_LABELS[element]}`,
                ELEMENT_DESCRIPTIONS[element],
            );
            this.drawElement(element);
        });
        this.addHeadersAndFootersToAllPages();
        return this.doc;
    }

    private ensureSpace(requiredHeight: number): void {
        if (this.y + requiredHeight <= CONTENT_MAX_Y) {
            return;
        }

        this.doc.addPage();
        this.drawBrandHeader(22, false);
        this.y = MARGIN + 22;
    }

    private drawBoundedParagraph(text: string, maxWidth: number): void {
        const lines = this.doc.splitTextToSize(text, maxWidth) as string[];
        const blockHeight = lines.length * NOTICE_LINE_HEIGHT;

        if (this.y + blockHeight > CONTENT_MAX_Y) {
            return;
        }

        this.doc.setFont('helvetica', 'italic');
        this.doc.setFontSize(8.5);
        this.doc.setTextColor(...COLORS.muted);

        lines.forEach((line, index) => {
            this.doc.text(line, MARGIN, this.y + index * NOTICE_LINE_HEIGHT);
        });

        this.y += blockHeight + 4;
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
            'FAST',
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
        this.doc.text('DOCUMENT MÉDICAL CONFIDENTIEL', PAGE_WIDTH - MARGIN, largeLogo ? 16 : 8, {
            align: 'right',
        });

        this.doc.setFont('helvetica', 'normal');
        this.doc.setFontSize(largeLogo ? 9 : 7.5);
        this.doc.text(this.assets.reference, PAGE_WIDTH - MARGIN, largeLogo ? 22 : 13, {
            align: 'right',
        });
    }

    private drawPatientPhoto(x: number, y: number): void {
        if (!this.assets.patientPhotoDataUrl) {
            return;
        }

        this.doc.setDrawColor(...COLORS.primary);
        this.doc.setLineWidth(0.6);
        this.doc.circle(x + PATIENT_PHOTO_SIZE / 2, y + PATIENT_PHOTO_SIZE / 2, PATIENT_PHOTO_SIZE / 2 + 0.5);

        this.doc.addImage(
            this.assets.patientPhotoDataUrl,
            'PNG',
            x,
            y,
            PATIENT_PHOTO_SIZE,
            PATIENT_PHOTO_SIZE,
            undefined,
            'FAST',
        );
    }

    private drawCover(elements: FollowUpReportElementId[]): void {
        const { header, period } = this.report;

        this.drawBrandHeader(44, true);

        const photoX = PAGE_WIDTH - MARGIN - PATIENT_PHOTO_SIZE;
        this.drawPatientPhoto(photoX, 52);

        this.y = 56;
        this.doc.setTextColor(...COLORS.text);
        this.doc.setFont('helvetica', 'bold');
        this.doc.setFontSize(18);
        this.doc.text('Rapport périodique d\'évolution', MARGIN, this.y);

        this.y += 8;
        this.doc.setFontSize(13);
        this.doc.setTextColor(...COLORS.primary);
        const nameWidth = CONTENT_WIDTH - PATIENT_PHOTO_SIZE - 6;
        this.doc.text(header.patientFullName, MARGIN, this.y, { maxWidth: nameWidth });

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
        this.doc.text('Synthèse du suivi patient', MARGIN + 5, this.y + 8);
        this.doc.setFont('helvetica', 'normal');
        this.doc.setFontSize(9);
        this.doc.text(
            `Période : ${formatFrenchDate(period.from)} au ${formatFrenchDate(period.to)}`,
            MARGIN + 5,
            this.y + 15,
        );
        this.doc.text(`Clinicien : ${header.clinicianName ?? '—'}`, MARGIN + 5, this.y + 21);
        this.doc.text(`Généré le ${formatFrenchDateTime(this.report.generatedAt)}`, MARGIN + 5, this.y + 27);

        this.y += 42;
        autoTable(this.doc, {
            startY: this.y,
            margin: getTablePageMargins(),
            theme: 'grid',
            styles: {
                fontSize: 9,
                cellPadding: 3,
                lineColor: COLORS.border,
                textColor: COLORS.text,
            },
            body: [
                ['Référence document', this.assets.reference],
                ['Patient', header.patientFullName],
                ['Date de naissance', header.dateOfBirth ? formatFrenchDate(header.dateOfBirth) : '—'],
                ['Type de diabète', header.diabetesType ?? '—'],
                ['Organisation', header.organizationName ?? '—'],
                ['Sections exportées', String(elements.length)],
            ],
            columnStyles: {
                0: { fontStyle: 'bold', cellWidth: 52, fillColor: COLORS.background, textColor: COLORS.primary },
                1: { fillColor: COLORS.white },
            },
        });

        this.y = (this.doc as jsPDF & { lastAutoTable: { finalY: number } }).lastAutoTable.finalY + 8;

        if (!this.report.hasData) {
            this.ensureSpace(18);
            this.doc.setFillColor(...COLORS.background);
            this.doc.roundedRect(MARGIN, this.y, CONTENT_WIDTH, 14, 2, 2, 'F');
            this.doc.setTextColor(...COLORS.muted);
            this.doc.setFont('helvetica', 'italic');
            this.doc.setFontSize(9);
            this.doc.text(
                'Aucune donnée disponible pour les éléments sélectionnés sur cette période.',
                MARGIN + 4,
                this.y + 8,
            );
            this.y += 18;
        }

        const notice = 'Ce document est généré automatiquement à partir des données médicales du patient. '
            + 'Usage clinique et administratif interne uniquement. Ne constitue pas un avis médical autonome. '
            + 'Le QR code présent sur chaque page permet de vérifier l\'authenticité du rapport.';
        this.drawBoundedParagraph(notice, CONTENT_WIDTH - QR_SIZE - 4);

        this.ensureSpace(14);
        this.doc.setFont('helvetica', 'bold');
        this.doc.setFontSize(12);
        this.doc.setTextColor(...COLORS.primary);
        this.doc.text('Sommaire des sections', MARGIN, this.y);
        this.y += 5;

        autoTable(this.doc, {
            startY: this.y,
            margin: getTablePageMargins(),
            head: [['Section', 'Description']],
            body: elements.map((id, index) => [
                `${index + 1}. ${ELEMENT_LABELS[id]}`,
                ELEMENT_DESCRIPTIONS[id],
            ]),
            styles: { fontSize: 9, cellPadding: 2.5, lineColor: COLORS.border },
            headStyles: {
                fillColor: COLORS.primary,
                textColor: COLORS.white,
                fontStyle: 'bold',
            },
            alternateRowStyles: { fillColor: COLORS.background },
        });

        this.y = (this.doc as jsPDF & { lastAutoTable: { finalY: number } }).lastAutoTable.finalY + 8;
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

    private drawKpiTable(title: string, rows: string[][]): void {
        this.ensureSpace(20);
        this.doc.setTextColor(...COLORS.text);
        this.doc.setFont('helvetica', 'bold');
        this.doc.setFontSize(10);
        this.doc.text(title, MARGIN, this.y);
        this.y += 4;

        autoTable(this.doc, {
            startY: this.y,
            margin: getTablePageMargins(),
            body: rows,
            styles: { fontSize: 9, cellPadding: 3, lineColor: COLORS.border, textColor: COLORS.text },
            columnStyles: {
                0: { fontStyle: 'bold', cellWidth: 70, fillColor: COLORS.background, textColor: COLORS.primary },
                1: { fillColor: COLORS.white },
            },
            alternateRowStyles: { fillColor: COLORS.surface },
        });

        this.y = (this.doc as jsPDF & { lastAutoTable: { finalY: number } }).lastAutoTable.finalY + 8;
    }

    private drawElement(element: FollowUpReportElementId): void {
        switch (element) {
            case 'glucose':
                this.drawGlucose();
                break;
            case 'hba1c':
                this.drawHbA1c();
                break;
            case 'blood_pressure':
                this.drawBloodPressure();
                break;
            case 'weight':
                this.drawWeight();
                break;
            case 'treatment':
                this.drawTreatment();
                break;
            case 'physical_activity':
                this.drawPhysicalActivity();
                break;
            case 'nutrition':
                this.drawNutrition();
                break;
            case 'laboratory':
                this.drawLaboratory();
                break;
        }
    }

    private drawGlucose(): void {
        const section = this.report.glucose;
        if (!section || section.stats.count === 0) {
            this.drawNoData();
            return;
        }

        this.drawKpiTable('Indicateurs glycémiques', statsRows('Glycémie', section.stats));

        if (section.trend) {
            this.doc.setFont('helvetica', 'bold');
            this.doc.setFontSize(10);
            this.doc.setTextColor(...COLORS.text);
            this.doc.text('Évolution de la glycémie', MARGIN, this.y);
            this.y += 4;
            this.y = drawSimpleTrendChart(this.doc, this.y, section.trend);

            autoTable(this.doc, {
                startY: this.y,
                margin: getTablePageMargins(),
                head: [['Date', 'Valeur']],
                body: trendRows(section.trend),
                styles: { fontSize: 9, cellPadding: 3, lineColor: COLORS.border },
                headStyles: { fillColor: COLORS.secondary, textColor: COLORS.white, fontStyle: 'bold' },
                alternateRowStyles: { fillColor: COLORS.background },
            });
            this.y = (this.doc as jsPDF & { lastAutoTable: { finalY: number } }).lastAutoTable.finalY + 8;
        }
    }

    private drawHbA1c(): void {
        const section = this.report.hba1c;
        if (!section || section.stats.count === 0) {
            this.drawNoData();
            return;
        }

        this.drawKpiTable('Indicateurs HbA1c', statsRows('HbA1c', section.stats));

        if (section.trend) {
            this.doc.setFont('helvetica', 'bold');
            this.doc.setFontSize(10);
            this.doc.text('Évolution de l\'HbA1c', MARGIN, this.y);
            this.y += 4;
            this.y = drawSimpleTrendChart(this.doc, this.y, section.trend);
        }
    }

    private drawBloodPressure(): void {
        const section = this.report.bloodPressure;
        if (!section || section.systolic.count === 0) {
            this.drawNoData();
            return;
        }

        this.drawKpiTable('Indicateurs tensionnels', [
            ...statsRows('Systolique', section.systolic),
            ...statsRows('Diastolique', section.diastolic),
        ]);

        section.trends.forEach((trend) => {
            this.doc.setFont('helvetica', 'bold');
            this.doc.setFontSize(10);
            this.doc.text(`Évolution — ${trend.label}`, MARGIN, this.y);
            this.y += 4;
            this.y = drawSimpleTrendChart(this.doc, this.y, trend);
        });
    }

    private drawWeight(): void {
        const section = this.report.weight;
        if (!section || section.weight.count === 0) {
            this.drawNoData();
            return;
        }

        this.drawKpiTable('Indicateurs anthropométriques', [
            ...statsRows('Poids', section.weight),
            ...statsRows('IMC', section.bmi),
        ]);

        if (section.weightTrend) {
            this.doc.setFont('helvetica', 'bold');
            this.doc.setFontSize(10);
            this.doc.text('Évolution du poids', MARGIN, this.y);
            this.y += 4;
            this.y = drawSimpleTrendChart(this.doc, this.y, section.weightTrend);
        }
    }

    private drawTreatment(): void {
        const section = this.report.treatment;
        if (!section || (section.totalIntakes === 0 && section.activePrescriptions === 0)) {
            this.drawNoData();
            return;
        }

        this.drawKpiTable('Traitement et observance', [
            ['Prescriptions actives', String(section.activePrescriptions)],
            ['Taux d\'observance', section.adherenceRate !== null ? `${section.adherenceRate} %` : '—'],
            ['Prises enregistrées', String(section.totalIntakes)],
        ]);
    }

    private drawPhysicalActivity(): void {
        const section = this.report.physicalActivity;
        if (!section || section.sessions === 0) {
            this.drawNoData();
            return;
        }

        this.drawKpiTable('Activité physique', [
            ['Séances', String(section.sessions)],
            ['Durée totale', `${section.totalMinutes} min`],
            ['Durée moyenne', section.averageMinutes !== null ? `${section.averageMinutes} min` : '—'],
        ]);

        if (section.trend) {
            this.doc.setFont('helvetica', 'bold');
            this.doc.setFontSize(10);
            this.doc.text('Évolution de l\'activité', MARGIN, this.y);
            this.y += 4;
            this.y = drawSimpleTrendChart(this.doc, this.y, section.trend);
        }
    }

    private drawNutrition(): void {
        const section = this.report.nutrition;
        if (!section || section.totalMeals === 0) {
            this.drawNoData();
            return;
        }

        this.drawKpiTable('Nutrition', [
            ['Repas enregistrés', String(section.totalMeals)],
        ]);
    }

    private drawLaboratory(): void {
        const section = this.report.laboratory;
        if (!section || section.count === 0) {
            this.drawNoData();
            return;
        }

        autoTable(this.doc, {
            startY: this.y,
            margin: getTablePageMargins(),
            head: [['Examen', 'Laboratoire', 'Date', 'Fichier']],
            body: section.results.map((item) => [
                item.testName,
                item.labName ?? '—',
                formatFrenchDate(item.measuredAt),
                item.hasFile ? 'Oui' : 'Non',
            ]),
            styles: { fontSize: 9, cellPadding: 3, lineColor: COLORS.border },
            headStyles: { fillColor: COLORS.primary, textColor: COLORS.white, fontStyle: 'bold' },
            alternateRowStyles: { fillColor: COLORS.background },
        });
        this.y = (this.doc as jsPDF & { lastAutoTable: { finalY: number } }).lastAutoTable.finalY + 8;
    }

    private drawNoData(): void {
        this.doc.setFillColor(...COLORS.background);
        this.doc.roundedRect(MARGIN, this.y, CONTENT_WIDTH, 16, 2, 2, 'F');
        this.doc.setTextColor(...COLORS.muted);
        this.doc.setFont('helvetica', 'italic');
        this.doc.setFontSize(10);
        this.doc.text(
            'Aucune donnée disponible pour cette section sur la période sélectionnée.',
            MARGIN + 4,
            this.y + 10,
        );
        this.y += 22;
    }

    private addHeadersAndFootersToAllPages(): void {
        const totalPages = this.doc.getNumberOfPages();
        const { header } = this.report;

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
                `${PDF_BRAND.name} — ${header.patientFullName}`,
                MARGIN,
                FOOTER_Y + 5,
            );
            this.doc.text(
                'Document confidentiel — usage médical et administratif interne',
                MARGIN,
                FOOTER_Y + 9,
            );
            this.doc.text(
                `Page ${page} / ${totalPages}`,
                MARGIN,
                FOOTER_Y + 13,
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
                'FAST',
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

export async function generatePatientFollowUpReportPdf(
    report: PatientFollowUpReport,
    elements: FollowUpReportElementId[],
): Promise<jsPDF> {
    if (!elements.length) {
        throw new Error('Sélectionnez au moins un élément à exporter.');
    }

    const assets = await loadFollowUpReportPdfAssets(report);
    return new PatientFollowUpPdfBuilder(report, assets).build(elements);
}

export async function downloadPatientFollowUpReportPdf(
    report: PatientFollowUpReport,
    elements: FollowUpReportElementId[],
    filename: string,
): Promise<void> {
    const doc = await generatePatientFollowUpReportPdf(report, elements);
    doc.save(filename);
}
