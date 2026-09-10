import { useEffect, useMemo, useState } from 'react';
import { Modal } from '@/react/components/UI/Modal';
import { Button } from '@/react/components/UI/Button';
import { Checkbox } from '@/react/components/Forms/Checkbox';
import { useI18n } from '@/react/i18n/I18nContext';
import { PeriodSelector } from '@/react/features/admin/reports/components/PeriodSelector';
import { PeriodPreset } from '@/react/features/admin/reports/types';
import {
    ALL_FOLLOW_UP_ELEMENT_IDS,
    buildFollowUpReportFilename,
    FOLLOW_UP_REPORT_ELEMENTS,
    getDefaultFollowUpPeriod,
} from '../config/followUpReportElements';
import { fetchPatientFollowUpReport } from '../services/patientFollowUpReportService';
import { downloadPatientFollowUpReportPdf } from '../utils/generatePatientFollowUpReportPdf';
import {
    getFollowUpPeriodSummary,
    validateFollowUpReportFilters,
} from '../utils/followUpReportFilters';
import { FollowUpReportElementId, FollowUpReportFilters } from '../types/followUpReport';

interface FollowUpReportModalProps {
    isOpen: boolean;
    onClose: () => void;
    patientId: string;
    patientName: string;
}

export function FollowUpReportModal({
    isOpen,
    onClose,
    patientId,
    patientName,
}: FollowUpReportModalProps) {
    const { t } = useI18n();
    const defaultPeriod = useMemo(() => getDefaultFollowUpPeriod(), []);
    const [selectedElements, setSelectedElements] = useState<FollowUpReportElementId[]>(ALL_FOLLOW_UP_ELEMENT_IDS);
    const [filters, setFilters] = useState<FollowUpReportFilters>(defaultPeriod);
    const [activePeriod, setActivePeriod] = useState<PeriodPreset | undefined>(undefined);
    const [isGenerating, setIsGenerating] = useState(false);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        if (!isOpen) {
            return;
        }

        setSelectedElements(ALL_FOLLOW_UP_ELEMENT_IDS);
        setFilters(defaultPeriod);
        setActivePeriod(undefined);
        setError(null);
    }, [isOpen, defaultPeriod]);

    const allSelected = selectedElements.length === ALL_FOLLOW_UP_ELEMENT_IDS.length;
    const periodSummary = useMemo(() => getFollowUpPeriodSummary(filters), [filters]);

    const toggleElement = (elementId: FollowUpReportElementId, checked: boolean) => {
        setSelectedElements((current) => {
            if (checked) {
                return current.includes(elementId) ? current : [...current, elementId];
            }

            return current.filter((id) => id !== elementId);
        });
        setError(null);
    };

    const toggleAll = () => {
        setSelectedElements(allSelected ? [] : [...ALL_FOLLOW_UP_ELEMENT_IDS]);
        setError(null);
    };

    const handlePeriodChange = (period: PeriodPreset) => {
        setActivePeriod(period);
        const today = new Date();

        if (period === 'month') {
            const from = new Date(today.getFullYear(), today.getMonth(), 1);
            const to = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            setFilters({ from: from.toISOString().slice(0, 10), to: to.toISOString().slice(0, 10) });
        } else if (period === 'quarter') {
            const quarterStart = Math.floor(today.getMonth() / 3) * 3;
            const from = new Date(today.getFullYear(), quarterStart, 1);
            const to = new Date(today.getFullYear(), quarterStart + 3, 0);
            setFilters({ from: from.toISOString().slice(0, 10), to: to.toISOString().slice(0, 10) });
        } else {
            const from = new Date(today.getFullYear(), 0, 1);
            const to = new Date(today.getFullYear(), 11, 31);
            setFilters({ from: from.toISOString().slice(0, 10), to: to.toISOString().slice(0, 10) });
        }

        setError(null);
    };

    const handleCustomRangeChange = (from: string, to: string) => {
        setActivePeriod(undefined);
        setFilters({ from, to });
        setError(null);
    };

    const handleGenerate = async () => {
        if (!selectedElements.length) {
            setError(t('Veuillez sélectionner au moins un élément.'));
            return;
        }

        const periodError = validateFollowUpReportFilters(filters);
        if (periodError) {
            setError(periodError);
            return;
        }

        setIsGenerating(true);
        setError(null);

        try {
            const report = await fetchPatientFollowUpReport(patientId, filters, selectedElements);
            const filename = buildFollowUpReportFilename(patientName, filters.from, filters.to);
            await downloadPatientFollowUpReportPdf(report, selectedElements, filename);
            onClose();
        } catch (err) {
            setError(err instanceof Error ? err.message : t('Impossible de générer le PDF.'));
        } finally {
            setIsGenerating(false);
        }
    };

    return (
        <Modal
            isOpen={isOpen}
            onClose={onClose}
            size="medium"
            title={t('Générer un rapport de suivi')}
            footer={(
                <>
                    <Button variant="secondary" onClick={onClose} disabled={isGenerating}>
                        {t('Annuler')}
                    </Button>
                    <Button
                        variant="primary"
                        onClick={handleGenerate}
                        isLoading={isGenerating}
                        disabled={!selectedElements.length}
                    >
                        {t('Générer le rapport PDF')}
                    </Button>
                </>
            )}
        >
            <div className="follow-up-report-modal">
                <p className="follow-up-report-modal__intro">
                    {t('Configurez la période et les éléments médicaux à inclure dans le rapport de {{ name }}.', { name: patientName })}
                </p>

                <section className="follow-up-report-modal__block">
                    <h3 className="follow-up-report-modal__block-title">{t("Période d'analyse")}</h3>
                    <p className="follow-up-report-modal__block-description">
                        {t('Le rapport sera généré pour : {{ period }}.', { period: periodSummary })}
                    </p>
                    <PeriodSelector
                        activePeriod={activePeriod}
                        customFrom={filters.from}
                        customTo={filters.to}
                        onPeriodChange={handlePeriodChange}
                        onCustomRangeChange={handleCustomRangeChange}
                    />
                </section>

                <section className="follow-up-report-modal__block">
                    <div className="follow-up-report-modal__toolbar">
                        <h3 className="follow-up-report-modal__block-title">{t('Éléments à inclure')}</h3>
                        <button type="button" className="follow-up-report-modal__toggle-all" onClick={toggleAll}>
                            {allSelected ? t('Tout désélectionner') : t('Tout sélectionner')}
                        </button>
                    </div>
                    <p className="follow-up-report-modal__count">
                        {selectedElements.length > 1
                            ? t('{{ count }} éléments sélectionnés', { count: selectedElements.length })
                            : t('{{ count }} élément sélectionné', { count: selectedElements.length })}
                    </p>

                    <div className="follow-up-report-modal__sections">
                        {FOLLOW_UP_REPORT_ELEMENTS.map((element) => (
                            <div key={element.id} className="follow-up-report-modal__section">
                                <Checkbox
                                    checked={selectedElements.includes(element.id)}
                                    onChange={(event) => toggleElement(element.id, event.target.checked)}
                                />
                                <div className="follow-up-report-modal__section-content">
                                    <span className="follow-up-report-modal__section-title">{t(element.label)}</span>
                                    <span className="follow-up-report-modal__section-description">{t(element.description)}</span>
                                </div>
                            </div>
                        ))}
                    </div>
                </section>

                {error && <p className="follow-up-report-modal__error">{error}</p>}
            </div>
        </Modal>
    );
}
