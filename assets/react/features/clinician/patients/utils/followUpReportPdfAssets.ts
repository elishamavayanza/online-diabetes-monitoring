import QRCode from 'qrcode';
import logoImage from '@/images/logo_with.png';
import {
    createSquareLogoDataUrl,
    loadImageAsDataUrl,
} from '@/react/features/admin/reports/utils/pdfAssets';
import { PDF_BRAND } from '@/react/features/admin/reports/config/pdfBrand';
import { PatientFollowUpReport } from '../types/followUpReport';

export interface FollowUpReportPdfAssets {
    logoDataUrl: string;
    patientPhotoDataUrl: string | null;
    qrDataUrl: string;
    reference: string;
    verificationUrl: string;
}

export function buildFollowUpReportReference(report: PatientFollowUpReport): string {
    return `RPT-PAT-${report.header.patientId}-${report.period.to.replace(/-/g, '')}`;
}

export function buildFollowUpReportVerificationUrl(report: PatientFollowUpReport): string {
    const params = new URLSearchParams({
        type: 'patient',
        ref: buildFollowUpReportReference(report),
        patientId: report.header.patientId,
        from: report.period.from,
        to: report.period.to,
    });

    return `${window.location.origin}/verify/report?${params.toString()}`;
}

async function generateFollowUpReportQrCode(report: PatientFollowUpReport): Promise<string> {
    const verificationUrl = buildFollowUpReportVerificationUrl(report);

    return QRCode.toDataURL(verificationUrl, {
        width: 280,
        margin: 1,
        color: {
            dark: PDF_BRAND.primary,
            light: PDF_BRAND.white,
        },
        errorCorrectionLevel: 'H',
    });
}

function getPatientInitials(fullName: string): string {
    return fullName
        .split(/\s+/)
        .filter(Boolean)
        .slice(0, 2)
        .map((part) => part[0]?.toUpperCase() ?? '')
        .join('');
}

export async function createCircularAvatarDataUrl(
    sourceDataUrl: string,
    sizePx: number,
): Promise<string> {
    return new Promise((resolve, reject) => {
        const image = new Image();
        image.crossOrigin = 'anonymous';
        image.onload = () => {
            const canvas = document.createElement('canvas');
            canvas.width = sizePx;
            canvas.height = sizePx;
            const context = canvas.getContext('2d');

            if (!context) {
                reject(new Error('Canvas indisponible pour la photo patient.'));
                return;
            }

            context.beginPath();
            context.arc(sizePx / 2, sizePx / 2, sizePx / 2, 0, Math.PI * 2);
            context.closePath();
            context.clip();

            const scale = Math.max(sizePx / image.width, sizePx / image.height);
            const drawWidth = image.width * scale;
            const drawHeight = image.height * scale;
            const offsetX = (sizePx - drawWidth) / 2;
            const offsetY = (sizePx - drawHeight) / 2;

            context.drawImage(image, offsetX, offsetY, drawWidth, drawHeight);
            resolve(canvas.toDataURL('image/png'));
        };
        image.onerror = () => reject(new Error('Impossible de préparer la photo patient.'));
        image.src = sourceDataUrl;
    });
}

export function createInitialsAvatarDataUrl(fullName: string, sizePx: number): string {
    const canvas = document.createElement('canvas');
    canvas.width = sizePx;
    canvas.height = sizePx;
    const context = canvas.getContext('2d');

    if (!context) {
        throw new Error('Canvas indisponible pour les initiales patient.');
    }

    context.fillStyle = PDF_BRAND.secondary;
    context.beginPath();
    context.arc(sizePx / 2, sizePx / 2, sizePx / 2, 0, Math.PI * 2);
    context.fill();

    context.fillStyle = PDF_BRAND.white;
    context.font = `bold ${Math.floor(sizePx * 0.35)}px Helvetica, Arial, sans-serif`;
    context.textAlign = 'center';
    context.textBaseline = 'middle';
    context.fillText(getPatientInitials(fullName), sizePx / 2, sizePx / 2 + 2);

    return canvas.toDataURL('image/png');
}

async function resolvePatientPhotoDataUrl(report: PatientFollowUpReport): Promise<string> {
    const avatarUrl = report.header.avatarUrl;

    if (avatarUrl) {
        try {
            const rawPhoto = await loadImageAsDataUrl(avatarUrl);
            return createCircularAvatarDataUrl(rawPhoto, 320);
        } catch {
            // Fallback sur les initiales si la photo est inaccessible.
        }
    }

    return createInitialsAvatarDataUrl(report.header.patientFullName, 320);
}

export async function loadFollowUpReportPdfAssets(report: PatientFollowUpReport): Promise<FollowUpReportPdfAssets> {
    const rawLogo = await loadImageAsDataUrl(logoImage);
    const [logoDataUrl, patientPhotoDataUrl, qrDataUrl] = await Promise.all([
        createSquareLogoDataUrl(rawLogo, 256),
        resolvePatientPhotoDataUrl(report),
        generateFollowUpReportQrCode(report),
    ]);

    return {
        logoDataUrl,
        patientPhotoDataUrl,
        qrDataUrl,
        reference: buildFollowUpReportReference(report),
        verificationUrl: buildFollowUpReportVerificationUrl(report),
    };
}
