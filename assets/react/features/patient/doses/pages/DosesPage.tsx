import { useState } from 'react';
import { useDoses } from '../hooks/useDoses';
import { DosesList } from '../components/DosesList';
import { IntakeActionModal } from '../components/IntakeActionModal';
import { Spinner } from '@/react/components/UI/Spinner';
import { ErrorState } from '@/react/components/UI/ErrorState';
import { Button } from '@/react/components/UI/Button';
import { Modal } from '@/react/components/UI/Modal';
import { RightSidebar } from '@/react/components/Navigation/RightSidebar';
import { Calendar } from '@/react/components/Calendars/Calendar';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import { MedicationIntake, IntakeStatus } from '../types';
import { useI18n } from '@/react/i18n/I18nContext';
import { formatDate } from '@/react/i18n/formatters';
import '@/styles/pages/patient/doses/_doses.scss';

export function DosesPage() {
    const { locale, t } = useI18n();
    const { intakes, selectedDate, setSelectedDate, markedDates, isLoading, error, recordIntake, reload } = useDoses();
    const [selectedIntake, setSelectedIntake] = useState<MedicationIntake | null>(null);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [isHelpOpen, setIsHelpOpen] = useState(false);
    const { pushAction } = useActionHistory();

    const openHelp = () => {
        setIsHelpOpen(true);
        pushAction(() => setIsHelpOpen(false));
    };

    const handleAction = (intake: MedicationIntake, newStatus: IntakeStatus) => {
        setSelectedIntake({ ...intake, statut: newStatus });
    };

    const confirmAction = async (status: IntakeStatus, time: string, quantity: string) => {
        if (!selectedIntake) return;
        const now = new Date();
        const dateTime = new Date(`${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')}T${time}:00`);
        setIsSubmitting(true);
        await recordIntake(selectedIntake.prescriptionItemId, status, dateTime.toISOString(), quantity);
        setIsSubmitting(false);
        setSelectedIntake(null);
    };

    if (isLoading) return <Spinner />;
    if (error) return <ErrorState size="full" {...error} onRetry={reload} />;

    const isToday = selectedDate.toDateString() === new Date().toDateString();
    const title = isToday
        ? t("Aujourd'hui")
        : t('Prises du {{ date }}', { date: formatDate(selectedDate, locale) });

    return (
        <div className="doses-page">
            <div className="doses-page__header">
                <h1>{isToday ? t('Mes prises') : t('Historique des prises')}</h1>
                <p>{isToday ? t("Ce qui vous est prescrit aujourd'hui") : t('Historique du {{ date }}', { date: formatDate(selectedDate, locale) })}</p>
                <Button variant="secondary" onClick={openHelp}>{t('Aide')}</Button>
            </div>

            <div className="doses-page__body">
                <div className="doses-page__content">
                    <DosesList intakes={intakes} onAction={handleAction} title={title} />
                </div>

                <RightSidebar
                    collapsible
                    size="medium"
                    minWidth={250}
                    maxWidth={400}
                    closeThreshold={80}
                    collapsedWidth={35}
                    title={t('Calendrier')}
                    header={<div>{t('Naviguez par date')}</div>}
                >
                    <div className="doses-page__right-content">
                        <Calendar
                            selectedDate={selectedDate}
                            onDateSelect={setSelectedDate}
                            markedDates={markedDates}
                        />
                        {!isToday && (
                            <Button variant="secondary" size="small" onClick={() => setSelectedDate(new Date())}>
                                {t("Retour à aujourd'hui")}
                            </Button>
                        )}
                    </div>
                </RightSidebar>
            </div>

            {isHelpOpen && (
                <Modal isOpen={isHelpOpen} onClose={() => setIsHelpOpen(false)}>
                    <p>{t('Cette page montre les prises de médicaments.')}</p>
                </Modal>
            )}

            <IntakeActionModal
                isOpen={!!selectedIntake}
                onClose={() => setSelectedIntake(null)}
                intake={selectedIntake}
                onConfirm={confirmAction}
                isSubmitting={isSubmitting}
            />
        </div>
    );
}
