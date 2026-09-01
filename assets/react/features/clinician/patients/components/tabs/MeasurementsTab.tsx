import { useState } from 'react';
import { Card } from '@/react/components/UI/Card';
import { Button } from '@/react/components/UI/Button';
import { TrendChart } from '@/react/features/admin/reports/components/TrendChart';
import { usePatientDossierContext } from '../../contexts/PatientDossierContext';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import { MEASUREMENT_TYPES } from '../../config/measurementTypes';
import { MeasurementTypeId } from '../../types';
import {
    buildTrendSeries,
    buildCandlestickData,
    formatDisplayDateTime,
    isInPeriod,
} from '../../utils/dossierUtils';
import type { CandlestickDataPoint } from '@/react/hook-components/Data/CandlestickChart/useCandlestickChart';
import {CandlestickChart} from "@/react/components/Data/CandlestickChart/CandlestickChart";

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
    };
    return lists[type].filter((m) => isInPeriod(m.createdAt, period, selectedDate)).length;
}

export function MeasurementsTab() {
    const { data, period, selectedDate, isReadOnly, openMeasurementModal } = usePatientDossierContext();
    const { pushAction } = useActionHistory();
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

        let series;
        let items: { id: string; label: string }[] = [];
        let candlestickData: CandlestickDataPoint[] | null = null;

        switch (selectedType) {
            case 'bloodGlucose': {
                const filtered = measurements.bloodGlucose.filter((m) => isInPeriod(m.createdAt, period, selectedDate));
                candlestickData = buildCandlestickData(filtered.map((m) => ({ createdAt: m.createdAt, value: m.value })));
                series = buildTrendSeries('Glycémie', filtered.map((m) => ({ createdAt: m.createdAt, value: m.value })), period, selectedDate, measurements.bloodGlucose[0]?.unit ?? 'mg/dL');
                items = filtered.slice(-15).reverse().map((m) => ({
                    id: m.id,
                    label: `${formatDisplayDateTime(m.createdAt)} — ${m.value} ${m.unit ?? 'mg/dL'}${m.context ? ` (${m.context})` : ''}`,
                }));
                break;
            }
            case 'bloodPressure': {
                const filtered = measurements.bloodPressure.filter((m) => isInPeriod(m.createdAt, period, selectedDate));
                candlestickData = buildCandlestickData(filtered.map((m) => ({ createdAt: m.createdAt, value: m.systolic })));
                series = buildTrendSeries('Systolique', filtered.map((m) => ({ createdAt: m.createdAt, value: m.systolic })), period, selectedDate, 'mmHg');
                items = filtered.slice(-15).reverse().map((m) => ({
                    id: m.id,
                    label: `${formatDisplayDateTime(m.createdAt)} — ${m.systolic}/${m.diastolic} mmHg${m.pulse != null ? `, pouls ${m.pulse}` : ''}`,
                }));
                break;
            }
            case 'hba1c': {
                const filtered = measurements.hba1c.filter((m) => isInPeriod(m.createdAt, period, selectedDate));
                candlestickData = buildCandlestickData(filtered.map((m) => ({ createdAt: m.createdAt, value: m.valuePercent })));
                series = buildTrendSeries('HbA1c', filtered.map((m) => ({ createdAt: m.createdAt, value: m.valuePercent })), period, selectedDate, '%');
                items = filtered.slice(-15).reverse().map((m) => ({
                    id: m.id,
                    label: `${formatDisplayDateTime(m.createdAt)} — ${m.valuePercent}%`,
                }));
                break;
            }
            case 'weight': {
                const filtered = measurements.weight.filter((m) => isInPeriod(m.createdAt, period, selectedDate));
                candlestickData = buildCandlestickData(filtered.map((m) => ({ createdAt: m.createdAt, value: m.valueKg })));
                series = buildTrendSeries('Poids', filtered.map((m) => ({ createdAt: m.createdAt, value: m.valueKg })), period, selectedDate, 'kg');
                items = filtered.slice(-15).reverse().map((m) => ({
                    id: m.id,
                    label: `${formatDisplayDateTime(m.createdAt)} — ${m.valueKg} kg${m.bmi != null ? ` (IMC ${m.bmi})` : ''}`,
                }));
                break;
            }
            case 'physicalActivity': {
                const filtered = measurements.physicalActivity.filter((m) => isInPeriod(m.createdAt, period, selectedDate));
                candlestickData = buildCandlestickData(filtered.map((m) => ({ createdAt: m.createdAt, value: m.durationMinutes })));
                series = buildTrendSeries('Activité', filtered.map((m) => ({ createdAt: m.createdAt, value: m.durationMinutes })), period, selectedDate, 'min');
                items = filtered.slice(-15).reverse().map((m) => ({
                    id: m.id,
                    label: `${formatDisplayDateTime(m.createdAt)} — ${m.activityType ?? 'Activité'} (${m.durationMinutes} min)`,
                }));
                break;
            }
            case 'laboratory': {
                series = { label: 'Laboratoire', unit: '', points: [] };
                items = measurements.laboratoryResults.filter((m) => isInPeriod(m.createdAt, period, selectedDate)).map((m) => ({
                    id: m.id,
                    label: `${formatDisplayDateTime(m.createdAt)} — ${m.testName}${m.labName ? ` (${m.labName})` : ''}`,
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
                        ← Retour aux mesures
                    </Button>
                    {!isReadOnly && (
                        <Button variant="primary" size="small" onClick={() => openMeasurementModal(selectedType)}>
                            + Prélèvement {config.label}
                        </Button>
                    )}
                </div>

                {/*  Graphique en chandeliers si données disponibles, sinon fallback TrendChart */}
                {selectedType !== 'laboratory' && candlestickData && candlestickData.length > 0 ? (
                    <CandlestickChart
                        data={candlestickData}
                        formatDate={(d) => String(d)}
                        formatPrice={(p) => `${p} ${config.unit}`}
                    />
                ) : selectedType !== 'laboratory' ? (
                    <TrendChart series={series!} />
                ) : null}

                <Card>
                    <h3>Historique — {config.label}</h3>
                    {items.length === 0 ? (
                        <p>Aucune mesure sur la période sélectionnée.</p>
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
                <p className="patient-dossier-tab__hint">Sélectionnez un type de mesure pour voir le graphique et l'historique.</p>
                {!isReadOnly && (
                    <Button variant="primary" onClick={() => openMeasurementModal()}>
                        + Prélèvement
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
                            <h3>{type.label}</h3>
                            <p>{type.description}</p>
                            <span className="measurement-type-card__count">
                                {count} mesure{count !== 1 ? 's' : ''}
                            </span>
                        </Card>
                    );
                })}
            </div>
        </div>
    );
}
