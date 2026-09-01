import { Card } from '@/react/components/UI/Card';
import { Badge } from '@/react/components/UI/Badge';
import { Button } from '@/react/components/UI/Button';
import { usePatientDossierContext } from '../../contexts/PatientDossierContext';
import { formatDisplayDateTime, isInPeriod } from '../../utils/dossierUtils';
import { getMealTypeLabel } from '../../utils/labelUtils';

export function NutritionTab() {
    const { data, period, selectedDate, isReadOnly, openMealModal } = usePatientDossierContext();
    const { meals, mealItems } = data;

    const filtered = meals
        .filter((meal) => {
            const date = meal.measuredAt ?? meal.createdAt;
            return date ? isInPeriod(date, period, selectedDate) : !selectedDate;
        })
        .sort((a, b) => {
            const dateA = new Date(a.measuredAt ?? a.createdAt ?? 0).getTime();
            const dateB = new Date(b.measuredAt ?? b.createdAt ?? 0).getTime();
            return dateB - dateA;
        });

    const getMealItems = (mealId: string) => mealItems.filter((item) => item.mealId === mealId);

    return (
        <div className="patient-dossier-tab patient-dossier-tab--nutrition">
            <div className="patient-dossier-tab__toolbar">
                <p className="patient-dossier-tab__hint">Suivi nutritionnel et repas enregistrés.</p>
                {/*{!isReadOnly && (*/}
                {/*    // <Button variant="primary" onClick={openMealModal}>*/}
                {/*    //     + Enregistrer un repas*/}
                {/*    // </Button>*/}
                {/*)}*/}
            </div>

            {filtered.length === 0 ? (
                <Card><p>Aucun repas sur la période sélectionnée.</p></Card>
            ) : (
                <div className="patient-dossier-tab__grid">
                    {filtered.map((meal) => {
                        const items = getMealItems(meal.id);
                        const date = meal.measuredAt ?? meal.createdAt;
                        return (
                            <Card key={meal.id}>
                                <div className="patient-dossier-tab__card-header">
                                    <h3>{meal.name}</h3>
                                    {meal.mealType && (
                                        <Badge variant="info">{getMealTypeLabel(meal.mealType)}</Badge>
                                    )}
                                </div>
                                {date && (
                                    <p><strong>Date :</strong> {formatDisplayDateTime(date)}</p>
                                )}
                                {meal.description && <p><strong>Description :</strong> {meal.description}</p>}
                                {items.length > 0 && (
                                    <div className="patient-dossier-tab__meal-items">
                                        <strong>Aliments ({items.length}) :</strong>
                                        <ul className="patient-dossier-tab__list">
                                            {items.map((item) => (
                                                <li key={item.id}>
                                                    {item.portionGrams} g
                                                    {item.breadUnits && ` — ${item.breadUnits} UB`}
                                                </li>
                                            ))}
                                        </ul>
                                    </div>
                                )}
                            </Card>
                        );
                    })}
                </div>
            )}
        </div>
    );
}
