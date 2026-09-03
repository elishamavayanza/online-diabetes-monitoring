import { useState, useMemo } from 'react';
import { useMeasurements } from '../hooks/useMeasurements';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Button } from '@/react/components/UI/Button';
import { Card } from '@/react/components/UI/Card';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import { RightSidebar } from '@/react/components/Navigation/RightSidebar';
import { Calendar } from '@/react/components/Calendars/Calendar';
import { MeasurementsTable } from '../components/MeasurementsTable';
import { CandlestickChart } from '@/react/components/Data/CandlestickChart/CandlestickChart';
import { Tabs } from '@/react/components/Navigation/Tabs'; // ✅ import du composant Tabs
import { MeasurementFormModal } from '@/react/features/clinician/patients/components/modals/record/MeasurementFormModal';
import { MeasurementType } from '../types';
import { MeasurementTypeId } from '@/react/features/clinician/patients/types';
import { getCurrentUserIdFromToken } from '@/react/utils/authUtils';
import type { CandlestickDataPoint } from '@/react/hook-components/Data/CandlestickChart/useCandlestickChart';
import {
    BloodGlucoseIcon,
    BloodPressureIcon,
    Hba1cIcon,
    WeightIcon,
    PhysicalActivityIcon,
} from '@/react/features/clinician/patients/config/measurementIcons';
import '@/styles/pages/patient/health/_measurements.scss';

const TYPE_TO_ID: Record<MeasurementType, MeasurementTypeId> = {
    'Glycémie': 'bloodGlucose',
    'Tension': 'bloodPressure',
    'Poids': 'weight',
    'HbA1c': 'hba1c',
    'Activité': 'physicalActivity',
};

const MEASUREMENT_TYPES: {
    id: MeasurementType;
    label: string;
    description: string;
    unit: string;
    icon: React.ReactNode;
}[] = [
    {
        id: 'Glycémie',
        label: 'Glycémie',
        description: 'Taux de sucre dans le sang',
        unit: 'mg/dL',
        icon: <BloodGlucoseIcon />,
    },
    {
        id: 'Tension',
        label: 'Tension',
        description: 'Pression artérielle',
        unit: 'mmHg',
        icon: <BloodPressureIcon />,
    },
    {
        id: 'Poids',
        label: 'Poids',
        description: 'Masse corporelle',
        unit: 'kg',
        icon: <WeightIcon />,
    },
    {
        id: 'HbA1c',
        label: 'HbA1c',
        description: 'Hémoglobine glyquée',
        unit: '%',
        icon: <Hba1cIcon />,
    },
    {
        id: 'Activité',
        label: 'Activité',
        description: 'Durée d’activité physique',
        unit: 'min',
        icon: <PhysicalActivityIcon />,
    },
];

function extractNumericValue(value: string): number | null {
    const normalized = value.replace(',', '.');
    const match = normalized.match(/-?\d+(\.\d+)?/);
    return match ? parseFloat(match[0]) : null;
}

// Définition des onglets pour la vue détail
const detailTabs = [
    { id: 'table', label: 'Historique' },
    { id: 'chart', label: 'Graphique' },
];

export function MeasurementsPage() {
    const { type, setType, records, isLoading, error, refetch } = useMeasurements();
    const [viewMode, setViewMode] = useState<'grid' | 'detail'>('grid');
    const [selectedDate, setSelectedDate] = useState<Date | null>(null);
    const [detailTab, setDetailTab] = useState<'table' | 'chart'>('table');
    const [isPrelevementModalOpen, setIsPrelevementModalOpen] = useState(false);
    const { pushAction } = useActionHistory();

    const markedDates = useMemo(() => {
        const dates = new Set<string>();
        records.forEach((record) => {
            const d = new Date(record.date);
            const normalized = new Date(d.getFullYear(), d.getMonth(), d.getDate());
            dates.add(normalized.toDateString());
        });
        return Array.from(dates).map((dateStr) => ({ date: new Date(dateStr) }));
    }, [records]);

    const handleSelectType = (selected: MeasurementType) => {
        const previousType = type;
        const previousMode = viewMode;
        setType(selected);
        setViewMode('detail');
        setDetailTab('table');
        pushAction(() => {
            setType(previousType);
            setViewMode(previousMode);
        });
    };

    const handleBackToGrid = () => {
        const previousMode = viewMode;
        setViewMode('grid');
        pushAction(() => setViewMode(previousMode));
    };

    const handleDateSelect = (date: Date) => {
        const previousDate = selectedDate;
        setSelectedDate(date);
        pushAction(() => setSelectedDate(previousDate));
    };

    const openPrelevementModal = () => setIsPrelevementModalOpen(true);
    const closePrelevementModal = () => setIsPrelevementModalOpen(false);

    const handlePrelevementSuccess = () => {
        closePrelevementModal();
        refetch?.();
    };

    if (isLoading) return <Spinner />;
    if (error) return <Alert variant="error">{error}</Alert>;

    const filteredRecords = selectedDate
        ? records.filter(
            (record) =>
                new Date(record.date).toDateString() === selectedDate.toDateString()
        )
        : records;

    // Préparation des données pour le graphique
    const chartData: CandlestickDataPoint[] = [];
    for (const record of filteredRecords) {
        const value = extractNumericValue(record.value);
        if (value !== null) {
            chartData.push({
                date: new Date(record.date).getTime(),
                open: value,
                high: value,
                low: value,
                close: value,
            });
        }
    }

    const currentPatientId = getCurrentUserIdFromToken() ?? '';

    const mainContent =
        viewMode === 'grid' ? (
            <div className="measurement-type-grid">
                {MEASUREMENT_TYPES.map((mt) => (
                    <Card
                        key={mt.id}
                        className="measurement-type-card"
                        interactive
                        onClick={() => handleSelectType(mt.id)}
                    >
                        <span className="measurement-type-card__icon">{mt.icon}</span>
                        <h3>{mt.label}</h3>
                        <p>{mt.description}</p>
                        <span className="measurement-type-card__count">Voir</span>
                    </Card>
                ))}
            </div>
        ) : (
            <>
                <div className="measurement-detail__actions">
                    <Button variant="secondary" size="small" onClick={handleBackToGrid}>
                        ← Retour aux mesures
                    </Button>

                    {type !== 'HbA1c' && (
                        <Button variant="primary" size="small" onClick={openPrelevementModal}>
                            + Prélèvement {MEASUREMENT_TYPES.find((t) => t.id === type)?.label}
                        </Button>
                    )}
                </div>

                {/* Utilisation du composant Tabs */}
                <Tabs
                    key={detailTab} // force la réinitialisation à la sélection courante
                    tabs={detailTabs}
                    defaultActiveTabId={detailTab}
                    onChange={(id) => setDetailTab(id as 'table' | 'chart')}
                    renderContent={(activeId) =>
                        activeId === 'table' ? (
                            <>
                                <h3 className="measurements-page__table-title">
                                    Historique — {MEASUREMENT_TYPES.find((t) => t.id === type)?.label}
                                </h3>
                                <MeasurementsTable records={filteredRecords} />
                            </>
                        ) : (
                            <CandlestickChart
                                data={chartData}
                                height={300}
                                formatDate={(timestamp) =>
                                    new Date(Number(timestamp)).toLocaleDateString('fr-FR')
                                }
                                formatPrice={(price) => `${price}`}
                            />
                        )
                    }
                />
            </>
        );

    return (
        <div className="measurements-page">
            <div className="measurements-page__header">
                <h1>Mes mesures</h1>
                <p>{viewMode === 'grid' ? 'Sélectionnez un type de mesure' : 'Historique de vos mesures'}</p>
            </div>

            <div className="measurements-page__body">
                <div className="measurements-page__content">{mainContent}</div>

                <RightSidebar
                    collapsible
                    size="medium"
                    minWidth={250}
                    maxWidth={400}
                    closeThreshold={80}
                    collapsedWidth={35}
                    title="Calendrier"
                    header={<div>Naviguez par date</div>}
                >
                    <div className="measurements-page__right-content">
                        <Calendar
                            selectedDate={selectedDate}
                            onDateSelect={handleDateSelect}
                            markedDates={markedDates}
                        />
                        {selectedDate && (
                            <Button
                                variant="secondary"
                                size="small"
                                onClick={() => setSelectedDate(null)}
                            >
                                Effacer la sélection
                            </Button>
                        )}
                    </div>
                </RightSidebar>
            </div>

            {isPrelevementModalOpen && type && (
                <MeasurementFormModal
                    isOpen={isPrelevementModalOpen}
                    onClose={closePrelevementModal}
                    patientId={currentPatientId}
                    initialType={TYPE_TO_ID[type]}
                    onSuccess={handlePrelevementSuccess}
                />
            )}
        </div>
    );
}
