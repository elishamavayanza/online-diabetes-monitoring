import { useState } from 'react';
import { useAgenda } from '../hooks/useAgenda';
import { AgendaDayCard } from '../components/AgendaDayCard';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Button } from '@/react/components/UI/Button';
import { Modal } from '@/react/components/UI/Modal';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import '@/styles/pages/nutritionist/agenda/_agenda.scss';

export function AgendaPages() {
    const { data, isLoading, error } = useAgenda();
    const [isHelpOpen, setIsHelpOpen] = useState(false);
    const { pushAction } = useActionHistory();

    const openHelp = () => {
        setIsHelpOpen(true);
        pushAction(() => setIsHelpOpen(false));
    };

    if (isLoading) return <Spinner />;
    if (error || !data) return <Alert variant="error">{error ?? 'Aucune donnée'}</Alert>;

    return (
        <div className="agenda-page">
            <div className="agenda-page__header">
                <h1>Agenda</h1>
                <p>Votre planning de la semaine</p>
                <Button variant="secondary" onClick={openHelp}>Aide</Button>
            </div>
            <div className="agenda-page__days">
                {data.days.map((day) => (
                    <AgendaDayCard key={day.date} day={day} />
                ))}
            </div>

            {isHelpOpen && (
                <Modal isOpen={isHelpOpen} onClose={() => setIsHelpOpen(false)}>
                    <p>Votre agenda hebdomadaire. Cliquez sur un jour pour plus de détails.</p>
                </Modal>
            )}
        </div>
    );
}
