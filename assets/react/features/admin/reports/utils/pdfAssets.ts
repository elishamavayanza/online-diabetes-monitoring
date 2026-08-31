import QRCode from 'qrcode';
import logoImage from '@/images/logo_with.png';
import { OrganizationReport } from '../types';
import { PDF_BRAND } from '../config/pdfBrand';

export async function loadImageAsDataUrl(src: string): Promise<string> {
    const response = await fetch(src);

    if (!response.ok) {
        throw new Error('Impossible de charger le logo OnlineDIAB.');
    }

    const blob = await response.blob();

    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = () => resolve(reader.result as string);
        reader.onerror = () => reject(new Error('Lecture du logo impossible.'));
        reader.readAsDataURL(blob);
    });
}

export async function createSquareLogoDataUrl(sourceDataUrl: string, sizePx: number): Promise<string> {
    return new Promise((resolve, reject) => {
        const image = new Image();
        image.onload = () => {
            const canvas = document.createElement('canvas');
            canvas.width = sizePx;
            canvas.height = sizePx;
            const context = canvas.getContext('2d');

            if (!context) {
                reject(new Error('Canvas indisponible pour le logo.'));
                return;
            }

            context.fillStyle = PDF_BRAND.white;
            context.fillRect(0, 0, sizePx, sizePx);

            const scale = Math.min(sizePx / image.width, sizePx / image.height);
            const drawWidth = image.width * scale;
            const drawHeight = image.height * scale;
            const offsetX = (sizePx - drawWidth) / 2;
            const offsetY = (sizePx - drawHeight) / 2;

            context.drawImage(image, offsetX, offsetY, drawWidth, drawHeight);
            resolve(canvas.toDataURL('image/png'));
        };
        image.onerror = () => reject(new Error('Impossible de préparer le logo carré.'));
        image.src = sourceDataUrl;
    });
}

export function buildReportReference(report: OrganizationReport): string {
    return `RPT-ORG-${report.organizationId}-${report.period.to.replace(/-/g, '')}`;
}

export function buildReportVerificationUrl(report: OrganizationReport): string {
    const params = new URLSearchParams({
        ref: buildReportReference(report),
        organizationId: report.organizationId,
        from: report.period.from,
        to: report.period.to,
    });

    return `${window.location.origin}/verify/report?${params.toString()}`;
}

export async function generateReportQrCode(report: OrganizationReport): Promise<string> {
    const verificationUrl = buildReportVerificationUrl(report);

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

export async function loadPdfAssets(report: OrganizationReport): Promise<{
    logoDataUrl: string;
    qrDataUrl: string;
    reference: string;
    verificationUrl: string;
}> {
    const rawLogo = await loadImageAsDataUrl(logoImage);
    const [logoDataUrl, qrDataUrl] = await Promise.all([
        createSquareLogoDataUrl(rawLogo, 256),
        generateReportQrCode(report),
    ]);

    return {
        logoDataUrl,
        qrDataUrl,
        reference: buildReportReference(report),
        verificationUrl: buildReportVerificationUrl(report),
    };
}
