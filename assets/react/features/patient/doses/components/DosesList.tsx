import { Card } from '@/react/components/UI/Card';
import { Badge } from '@/react/components/UI/Badge';
import { MedicationIntake } from '../types';

interface DosesListProps {
    intakes: MedicationIntake[];
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

export function DosesList({ intakes }: DosesListProps) {
    return (
        <Card className="doses-card">
            <h2>Aujourd'hui</h2>
            <ul className="doses-list">
                {intakes.map((intake) => (
                    <li key={intake.id} className="dose-item">
                        <span className="dose-item__time">{intake.time}</span>
                        <span className="dose-item__medication">{intake.medication}</span>
                        <Badge variant={statusVariant[intake.statut]}>{statusLabel[intake.statut]}</Badge>
                    </li>
                ))}
            </ul>
        </Card>
    );
}
