import { useI18n } from '@/react/i18n/I18nContext';
import { useAppointments } from '../hooks/useAppointments';
import { AppointmentsTable } from '../components/AppointmentsTable';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Tabs } from '@/react/components/Navigation/Tabs';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import '@/styles/pages/nutritionist/appointments/_appointments.scss';

export function AppointmentsPages() {
    const { t } = useI18n();
    const { appointments, filter, setFilter, isLoading, error, reload } = useAppointments();
    const { pushAction } = useActionHistory();

    const tabs = [
        { id: 'today', label: t("Aujourd'hui") },
        { id: 'upcoming', label: t('À venir') },
        { id: 'completed', label: t('Terminés') },
        { id: 'cancelled', label: t('Annulés') },
    ];

    const handleFilterChange = (newFilter: string) => {
        const previousFilter = filter;
        setFilter(newFilter as typeof filter);
        pushAction(() => setFilter(previousFilter));
    };

    if (isLoading) return <Spinner />;
    if (error) return <Alert variant="error">{error}</Alert>;

    return (
        <div className="appointments-page">
            <div className="appointments-page__header">
                <h1>{t('Rendez-vous')}</h1>
                <p>{t('Gérez vos rendez-vous')}</p>
            </div>
            <Tabs
                tabs={tabs}
                defaultActiveTabId={filter}
                onChange={handleFilterChange}
            />
            <AppointmentsTable appointments={appointments} onActionSuccess={reload} />
        </div>
    );
}
