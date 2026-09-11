import { useState } from 'react';
import { Card } from '@/react/components/UI/Card';
import { Button } from '@/react/components/UI/Button';
import { LineChart } from '@/react/components/Data/LineChart/LineChart';
import { useI18n } from '@/react/i18n/I18nContext';
import { usePatientDossierContext } from '../../contexts/PatientDossierContext';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import { MEASUREMENT_TYPES } from '../../config/measurementTypes';
import { MeasurementTypeId } from '../../types';
import {
    buildTrendSeries,
    formatDisplayDateTime,
    isInPeriod,
} from '../../utils/dossierUtils';
import type { TrendSeries } from '@/react/features/admin/reports/types';

function countForType(
    data: ReturnType<typeof usePatientDossierContext>['data'],
    type: MeasurementTypeId,
    period: ReturnType<typeof usePatientDossierContext>['period'],
    selectedDate: Date | null
): number {
    const { measurements } = data;
    const lists: Record<MeasurementTypeId, { createdAt: string }[]> = {
        bloodGlucose: measurements.bloodGlucose,
        bloodPressure: measurements.bloodPressure,
        hba1c: measurements.hba1c,
        weight: measurements.weight,
        physicalActivity: measurements.physicalActivity,
        laboratory: measurements.laboratoryResults,
        insulinInjection: measurements.insulinInjections,
    };
    return lists[type].filter((m) => isInPeriod(m.createdAt, period, selectedDate)).length;
}

export function MeasurementsTab() {
    const { data, period, selectedDate, isReadOnly, openMeasurementModal } = usePatientDossierContext();
    const { pushAction } = useActionHistory();
    const { t } = useI18n();
    const [selectedType, setSelectedType] = useState<MeasurementTypeId | null>(null);
    const { measurements } = data;

    const handleSelectType = (typeId: MeasurementTypeId) => {
        const previousType = selectedType;
        setSelectedType(typeId);
        pushAction(() => setSelectedType(previousType));
    };

    const handleBackToList = () => {
        const previousType = selectedType;
        setSelectedType(null);
        pushAction(() => setSelectedType(previousType));
    };

    const renderDetail = () => {
        if (!selectedType) return null;
        const config = MEASUREMENT_TYPES.find((t) => t.id === selectedType)!;

        let series: TrendSeries | null = null;
        let items: { id: string; label: string }[] = [];

        const byAuthor = (m: { createdByName?: string }) =>
            m.createdByName ? ` — ${t('Créé par')} ${m.createdByName}` : '';

        switch (selectedType) {
            case 'bloodGlucose': {
                const filtered = measurements.bloodGlucose.filter((m) => isInPeriod(m.createdAt, period, selectedDate));
                series = buildTrendSeries(t('Glycémie'), filtered.map((m) => ({ createdAt: m.createdAt, value: m.value })), period, selectedDate, measurements.bloodGlucose[0]?.unit ?? 'mg/dL');
                items = filtered.slice(-15).reverse().map((m) => ({
                    id: m.id,
                    label: `${formatDisplayDateTime(m.createdAt)} — ${m.value} ${m.unit ?? 'mg/dL'}${m.context ? ` (${t(m.context)})` : ''}${byAuthor(m)}`,
                }));
                break;
            }
            case 'bloodPressure': {
                const filtered = measurements.bloodPressure.filter((m) => isInPeriod(m.createdAt, period, selectedDate));
                series = buildTrendSeries(t('Systolique'), filtered.map((m) => ({ createdAt: m.createdAt, value: m.systolic })), period, selectedDate, 'mmHg');
                items = filtered.slice(-15).reverse().map((m) => ({
                    id: m.id,
                    label: `${formatDisplayDateTime(m.createdAt)} — ${m.systolic}/${m.diastolic} mmHg${m.pulse != null ? `, ${t('pouls {{ pulse }}', { pulse: m.pulse })}` : ''}${byAuthor(m)}`,
                }));
                break;
            }
            case 'hba1c': {
                const filtered = measurements.hba1c.filter((m) => isInPeriod(m.createdAt, period, selectedDate));
                series = buildTrendSeries(t('HbA1c'), filtered.map((m) => ({ createdAt: m.createdAt, value: m.valuePercent })), period, selectedDate, '%');
                items = filtered.slice(-15).reverse().map((m) => ({
                    id: m.id,
                    label: `${formatDisplayDateTime(m.createdAt)} — ${m.valuePercent}%${byAuthor(m)}`,
                }));
                break;
            }
            case 'weight': {
                const filtered = measurements.weight.filter((m) => isInPeriod(m.createdAt, period, selectedDate));
                series = buildTrendSeries(t('Poids'), filtered.map((m) => ({ createdAt: m.createdAt, value: m.valueKg })), period, selectedDate, 'kg');
                items = filtered.slice(-15).reverse().map((m) => ({
                    id: m.id,
                    label: `${formatDisplayDateTime(m.createdAt)} — ${m.valueKg} kg${m.bmi != null ? ` (IMC ${m.bmi})` : ''}${byAuthor(m)}`,
                }));
                break;
            }
            case 'physicalActivity': {
                const filtered = measurements.physicalActivity.filter((m) => isInPeriod(m.createdAt, period, selectedDate));
                series = buildTrendSeries(t('Activité'), filtered.map((m) => ({ createdAt: m.createdAt, value: m.durationMinutes })), period, selectedDate, 'min');
                items = filtered.slice(-15).reverse().map((m) => ({
                    id: m.id,
                    label: `${formatDisplayDateTime(m.createdAt)} — ${t(m.activityType ?? 'Activité')} (${m.durationMinutes} min)${byAuthor(m)}`,
                }));
                break;
            }
            case 'laboratory': {
                series = { label: t('Laboratoire'), unit: '', points: [] };
                items = measurements.laboratoryResults.filter((m) => isInPeriod(m.createdAt, period, selectedDate)).map((m) => ({
                    id: m.id,
                    label: `${formatDisplayDateTime(m.createdAt)} — ${m.testName}${m.labName ? ` (${m.labName})` : ''}${byAuthor(m)}`,
                }));
                break;
            }
            case 'insulinInjection': {
                series = { label: t("Injection d'insuline"), unit: 'u', points: [] };
                items = measurements.insulinInjections
                    .filter((m) => isInPeriod(m.injectedAt ?? m.createdAt, period, selectedDate))
                    .map((m) => ({
                        id: m.id,
                        label: `${formatDisplayDateTime(m.injectedAt ?? m.createdAt)} — ${m.doseUnits} u${m.injectionSite ? ` (${t(m.injectionSite)})` : ''}${m.status ? ` — ${t(m.status)}` : ''}${m.notes ? ` — ${m.notes}` : ''}${m.issuerName ? ` — ${t('Créé par')} ${m.issuerName}` : ''}`,
                    }));
                break;
            }
            default:
                break;
        }

        return (
            <div className="measurement-detail">
                <div className="measurement-detail__actions">
                    <Button variant="secondary" size="small" onClick={handleBackToList}>
                        {t('← Retour aux mesures')}
                    </Button>
                    {!isReadOnly && (
                        <Button variant="primary" size="small" onClick={() => openMeasurementModal(selectedType)}>
                            {t('+ Prélèvement {{ label }}', { label: t(config.label) })}
                        </Button>
                    )}
                </div>

                {/* Line chart moderne si données disponibles */}
                {selectedType !== 'laboratory' && selectedType !== 'insulinInjection' && series && series.points.length > 0 ? (
                    <LineChart
                        data={series.points}
                        formatDate={(d) => String(d)}
                        formatValue={(p) => `${p} ${config.unit}`}
                    />
                ) : null}

                <Card>
                    <h3>{t('Historique — {{ label }}', { label: t(config.label) })}</h3>
                    {items.length === 0 ? (
                        <p>{t('Aucune mesure sur la période sélectionnée.')}</p>
                    ) : (
                        <ul className="patient-dossier-tab__list">
                            {items.map((item) => <li key={item.id}>{item.label}</li>)}
                        </ul>
                    )}
                </Card>
            </div>
        );
    };

    if (selectedType) return <div className="patient-dossier-tab patient-dossier-tab--measurements">{renderDetail()}</div>;

    return (
        <div className="patient-dossier-tab patient-dossier-tab--measurements">
            <div className="patient-dossier-tab__toolbar">
                <p className="patient-dossier-tab__hint">{t("Sélectionnez un type de mesure pour voir le graphique et l'historique.")}</p>
                {!isReadOnly && (
                    <Button variant="primary" onClick={() => openMeasurementModal()}>
                        {t('+ Prélèvement')}
                    </Button>
                )}
            </div>

            <div className="measurement-type-grid">
                {MEASUREMENT_TYPES.map((type) => {
                    const count = countForType(data, type.id, period, selectedDate);
                    return (
                        <Card
                            key={type.id}
                            className="measurement-type-card"
                            interactive
                            onClick={() => handleSelectType(type.id)}
                        >
                            <span className="measurement-type-card__icon">
                                {type.icon}
                            </span>
                            <h3>{t(type.label)}</h3>
                            <p>{t(type.description)}</p>
                            <span className="measurement-type-card__count">
                                {count !== 1
                                    ? t('{{ count }} mesures', { count })
                                    : t('{{ count }} mesure', { count })}
                            </span>
                        </Card>
                    );
                })}
            </div>
        </div>
    );
}
