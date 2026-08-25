import { useAgenda } from '../hooks/useAgenda';
import { AgendaDayCard } from '../components/AgendaDayCard';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import '@/styles/pages/nutritionist/agenda/_agenda.scss';

export function AgendaPages() {
    const { data, isLoading, error } = useAgenda();

    if (isLoading) return <Spinner />;
    if (error || !data) return <Alert variant="error">{error ?? 'Aucune donnée'}</Alert>;

    return (
        <div className="agenda-page">
            <div className="agenda-page__header">
                <h1>Agenda</h1>
                <p>Votre planning de la semaine</p>
            </div>
            <div className="agenda-page__days">
                {data.days.map((day) => (
                    <AgendaDayCard key={day.date} day={day} />
                ))}
            </div>
        </div>
    );
}
