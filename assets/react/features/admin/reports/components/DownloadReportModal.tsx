import { useEffect, useMemo, useState } from 'react';
import { Modal } from '@/react/components/UI/Modal';
import { Button } from '@/react/components/UI/Button';
import { Checkbox } from '@/react/components/Forms/Checkbox';
import { OrganizationReport, PeriodPreset, ReportFilters } from '../types';
import {
    ALL_SECTION_IDS,
    buildReportFilename,
    REPORT_SECTIONS,
    ReportSectionId,
} from '../config/reportSections';
import { fetchOrganizationReport } from '../services/organizationReportService';
import { downloadOrganizationReportPdf } from '../utils/generateOrganizationReportPdf';
import { PeriodSelector } from './PeriodSelector';
import {
    getInitialReportFilters,
    getPeriodSummary,
    validateReportFilters,
} from '../utils/reportFilters';

interface DownloadReportModalProps {
    isOpen: boolean;
    onClose: () => void;
    report: OrganizationReport;
}

export function DownloadReportModal({ isOpen, onClose, report }: DownloadReportModalProps) {
    const [selectedSections, setSelectedSections] = useState<ReportSectionId[]>(ALL_SECTION_IDS);
    const [downloadFilters, setDownloadFilters] = useState<ReportFilters>(() => getInitialReportFilters(report));
    const [isGenerating, setIsGenerating] = useState(false);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        if (!isOpen) {
            return;
        }

        setSelectedSections(ALL_SECTION_IDS);
        setDownloadFilters(getInitialReportFilters(report));
        setError(null);
    }, [isOpen, report]);

    const allSelected = selectedSections.length === ALL_SECTION_IDS.length;

    const selectedCountLabel = useMemo(
        () => `${selectedSections.length} section${selectedSections.length > 1 ? 's' : ''} sélectionnée${selectedSections.length > 1 ? 's' : ''}`,
        [selectedSections.length]
    );

    const periodSummary = useMemo(() => getPeriodSummary(downloadFilters), [downloadFilters]);

    const toggleSection = (sectionId: ReportSectionId, checked: boolean) => {
        setSelectedSections((current) => {
            if (checked) {
                return current.includes(sectionId) ? current : [...current, sectionId];
            }

            return current.filter((id) => id !== sectionId);
        });
        setError(null);
    };

    const toggleAll = () => {
        setSelectedSections(allSelected ? [] : [...ALL_SECTION_IDS]);
        setError(null);
    };

    const handlePeriodChange = (period: PeriodPreset) => {
        setDownloadFilters({ period });
        setError(null);
    };

    const handleCustomRangeChange = (from: string, to: string) => {
        setDownloadFilters({ from, to });
        setError(null);
    };

    const handleDownload = async () => {
        if (!selectedSections.length) {
            setError('Veuillez sélectionner au moins une section.');
            return;
        }

        const periodError = validateReportFilters(downloadFilters);
        if (periodError) {
            setError(periodError);
            return;
        }

        setIsGenerating(true);
        setError(null);

        try {
            const reportForDownload = await fetchOrganizationReport(downloadFilters);
            const filename = buildReportFilename(reportForDownload, selectedSections);
            await downloadOrganizationReportPdf(reportForDownload, selectedSections, filename);
            onClose();
        } catch (err) {
            setError(err instanceof Error ? err.message : 'Impossible de générer le PDF.');
        } finally {
            setIsGenerating(false);
        }
    };

    return (
        <Modal
            isOpen={isOpen}
            onClose={onClose}
            size="medium"
            title="Télécharger le rapport en PDF"
            footer={(
                <>
                    <Button variant="secondary" onClick={onClose} disabled={isGenerating}>
                        Annuler
                    </Button>
                    <Button
                        variant="primary"
                        onClick={handleDownload}
                        isLoading={isGenerating}
                        disabled={!selectedSections.length}
                    >
                        Télécharger le PDF
                    </Button>
                </>
            )}
        >
            <div className="download-report-modal">
                <p className="download-report-modal__intro">
                    Configurez la période et les sections à inclure dans le document administratif.
                </p>

                <section className="download-report-modal__block">
                    <h3 className="download-report-modal__block-title">Période du rapport</h3>
                    <p className="download-report-modal__block-description">
                        Sélectionnez un raccourci ou une plage personnalisée. Le PDF sera généré pour : <strong>{periodSummary}</strong>.
                    </p>
                    <PeriodSelector
                        activePeriod={downloadFilters.period}
                        customFrom={downloadFilters.from}
                        customTo={downloadFilters.to}
                        onPeriodChange={handlePeriodChange}
                        onCustomRangeChange={handleCustomRangeChange}
                    />
                </section>

                <section className="download-report-modal__block">
                    <div className="download-report-modal__toolbar">
                        <h3 className="download-report-modal__block-title">Sections à exporter</h3>
                        <button type="button" className="download-report-modal__toggle-all" onClick={toggleAll}>
                            {allSelected ? 'Tout désélectionner' : 'Tout sélectionner'}
                        </button>
                    </div>
                    <p className="download-report-modal__count">{selectedCountLabel}</p>

                    <div className="download-report-modal__sections">
                        {REPORT_SECTIONS.map((section) => (
                            <div key={section.id} className="download-report-modal__section">
                                <Checkbox
                                    checked={selectedSections.includes(section.id)}
                                    onChange={(event) => toggleSection(section.id, event.target.checked)}
                                />
                                <div className="download-report-modal__section-content">
                                    <span className="download-report-modal__section-title">{section.label}</span>
                                    <span className="download-report-modal__section-description">{section.description}</span>
                                </div>
                            </div>
                        ))}
                    </div>
                </section>

                {error && <p className="download-report-modal__error">{error}</p>}
            </div>
        </Modal>
    );
}
