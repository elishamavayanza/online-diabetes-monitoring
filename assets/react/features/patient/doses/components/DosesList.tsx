import { Card } from '@/react/components/UI/Card';
import { Badge } from '@/react/components/UI/Badge';
import { Button } from '@/react/components/UI/Button';
import { MedicationIntake, IntakeStatus } from '../types';

interface DosesListProps {
    intakes: MedicationIntake[];
    onAction: (intake: MedicationIntake, newStatus: IntakeStatus) => void;
}

const statusVariant: Record<string, 'success' | 'warning' | 'error' | 'primary'> = {
    TAKEN: 'success',
    SKIPPED: 'error',
    DELAYED: 'warning',
    PENDING: 'primary',
};

const statusLabel: Record<string, string> = {
    TAKEN: '✓ Prise',
    SKIPPED: 'Ignorée',
    DELAYED: 'Retardée',
    PENDING: 'En attente',
};

function getPeriodFromTime(time: string): string {
    const hour = parseInt(time.split(':')[0], 10);
    if (hour < 12) return 'Matin';
    if (hour < 15) return 'Midi';
    return 'Soir';
}

export function DosesList({ intakes, onAction }: DosesListProps) {
    const summary = {
        total: intakes.length,
        taken: intakes.filter((i) => i.statut === 'TAKEN').length,
        pending: intakes.filter((i) => i.statut === 'PENDING').length,
        skipped: intakes.filter((i) => i.statut === 'SKIPPED').length,
        delayed: intakes.filter((i) => i.statut === 'DELAYED').length,
    };

    const periods = ['Matin', 'Midi', 'Soir'];
    const groupedByPeriod = periods.map((period) => ({
        period,
        intakes: intakes.filter((intake) => getPeriodFromTime(intake.time) === period),
    }));

    return (
        <Card className="doses-card">
            <h2>Aujourd'hui</h2>

            <div className="doses-summary">
                <span className="doses-summary__total">{summary.total} prises</span>
                <span className="doses-summary__taken">{summary.taken} prises effectuées</span>
                <span className="doses-summary__pending">{summary.pending} en attente</span>
                {summary.skipped > 0 && <span className="doses-summary__skipped">{summary.skipped} ignorées</span>}
                {summary.delayed > 0 && <span className="doses-summary__delayed">{summary.delayed} retardées</span>}
            </div>

            {groupedByPeriod.map(({ period, intakes }) =>
                intakes.length > 0 ? (
                    <section key={period} className="dose-period">
                        <h3 className="dose-period__title">{period}</h3>
                        <div className="dose-period__list">
                            {intakes.map((intake) => (
                                <div key={intake.id} className="dose-item">
                                    <div className="dose-item__actions">
                                        {intake.statut === 'PENDING' ? (
                                            <>
                                                <Button size="small" variant="success" onClick={() => onAction(intake, 'TAKEN')}>
                                                    Prise
                                                </Button>
                                                <Button size="small" variant="danger" onClick={() => onAction(intake, 'SKIPPED')}>
                                                    Ignorée
                                                </Button>
                                                <Button size="small" variant="secondary" onClick={() => onAction(intake, 'DELAYED')}>
                                                    Retardée
                                                </Button>
                                            </>
                                        ) : (
                                            <span className="dose-item__locked">🔒</span>
                                        )}
                                    </div>

                                    <div className="dose-item__info">
                                        <span className="dose-item__medication">{intake.medication}</span>
                                        <span className="dose-item__time">{intake.time}</span>
                                        <Badge variant={statusVariant[intake.statut]}>
                                            {statusLabel[intake.statut]}
                                        </Badge>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </section>
                ) : null
            )}
        </Card>
    );
}
