import { useEffect, useState } from 'react';
import { Card } from '@/react/components/UI/Card';
import { Badge } from '@/react/components/UI/Badge';
import { usePatientDossierContext } from '../../contexts/PatientDossierContext';
import { formatDisplayDateTime, isInPeriod } from '../../utils/dossierUtils';
import { getMealTypeLabel } from '../../utils/labelUtils';
import { FoodOption } from '@/react/features/patient/nutrition/types';
import { fetchFoods } from '@/react/features/nutritionist/foods/services/foodsService';

export function NutritionTab() {
    const { data, period, selectedDate } = usePatientDossierContext();
    const { meals, mealItems } = data;

    const [foods, setFoods] = useState<FoodOption[]>([]);

    useEffect(() => {
        const loadFoods = async () => {
            try {
                const foodsData = await fetchFoods();
                const mappedFoods: FoodOption[] = foodsData.map((f) => ({
                    id: String(f.id),
                    name: f.name,
                    photoUrl: f.photoUrl ?? undefined,
                }));
                setFoods(mappedFoods);
            } catch (error) {
                console.error('Erreur de chargement des aliments', error);
            }
        };
        loadFoods();
    }, []);

    const foodMap = new Map(foods.map((food) => [food.id, food]));

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
                                        <ul className="patient-dossier-tab__list meal-items-list">
                                            {items.map((item) => {
                                                const food = foodMap.get(item.foodId);
                                                return (
                                                    <li key={item.id} className="meal-item-row">
                                                        <div className="meal-item-row__photo">
                                                            {food?.photoUrl ? (
                                                                <img
                                                                    src={food.photoUrl}
                                                                    alt={food.name}
                                                                    className="meal-item-row__img"
                                                                />
                                                            ) : (
                                                                <span className="meal-item-row__placeholder">🍽️</span>
                                                            )}
                                                        </div>

                                                        <span className="meal-item-row__name">
                                                            {food?.name || 'Aliment'}
                                                        </span>

                                                        <span className="meal-item-row__portion">
                                                            {item.portionGrams} g
                                                        </span>

                                                        {item.breadUnits !== undefined && item.breadUnits !== null && (
                                                            <span className="meal-item-row__bread-units">
                                                                {item.breadUnits} UB
                                                            </span>
                                                        )}
                                                    </li>
                                                );
                                            })}
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
