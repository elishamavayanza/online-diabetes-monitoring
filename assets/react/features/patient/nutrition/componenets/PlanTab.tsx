// components/PlanTab.tsx
import { RightSidebar } from '@/react/components/Navigation/RightSidebar';
import { Calendar } from '@/react/components/Calendars/Calendar';
import { Card } from '@/react/components/UI/Card';
import { Badge } from '@/react/components/UI/Badge';
import { Button } from '@/react/components/UI/Button';
import { PatientMeal, PatientMealItem, FoodOption } from '../types';

// ✅ Traduction des types de repas
const MEAL_TYPE_LABELS: Record<string, string> = {
    BREAKFAST: 'Petit-déjeuner',
    LUNCH: 'Déjeuner',
    DINNER: 'Dîner',
    SNACK: 'Collation',
    // Les types personnalisés (OTHER) seront affichés tels quels
};

interface PlanTabProps {
    meals: PatientMeal[];
    mealItems: Record<string, PatientMealItem[]>;
    foods: FoodOption[];
    selectedDate: Date;
    setSelectedDate: (date: Date) => void;
    markedDates: { date: Date }[];
    onRemoveMeal: (id: string) => void;
    onRemoveItem: (id: string) => void;
    onAddItem: (meal: PatientMeal) => void;
}

export function PlanTab({
                            meals,
                            mealItems,
                            foods,
                            selectedDate,
                            setSelectedDate,
                            markedDates,
                            onRemoveMeal,
                            onRemoveItem,
                            onAddItem,
                        }: PlanTabProps) {
    const foodMap = new Map(foods.map((food) => [food.id, food]));

    return (
        <div className="nutrition-page__plan-layout">
            <div className="nutrition-page__plan-content">
                <h2>Repas du {selectedDate.toLocaleDateString('fr-FR')}</h2>
                {meals.length === 0 ? (
                    <Card>
                        <p>Aucun repas planifié pour cette date.</p>
                    </Card>
                ) : (
                    <div className="nutrition-page__meals">
                        {meals.map((meal) => (
                            <Card key={meal.id} className="meal-card">
                                <div className="meal-card__header">
                                    <h3>{meal.name}</h3>
                                    {/* ✅ Affichage traduit */}
                                    <Badge variant="info">
                                        {MEAL_TYPE_LABELS[meal.mealType] || meal.mealType}
                                    </Badge>
                                    <Button
                                        variant="danger"
                                        size="small"
                                        onClick={() => onRemoveMeal(meal.id)}
                                    >
                                        Supprimer
                                    </Button>
                                </div>
                                {meal.description && (
                                    <p className="meal-card__desc">{meal.description}</p>
                                )}
                                <div className="meal-card__items">
                                    {mealItems[meal.id]?.length ? (
                                        mealItems[meal.id].map((item) => {
                                            const food = foodMap.get(item.foodId);
                                            return (
                                                <div key={item.id} className="meal-item">
                                                    {food?.photoUrl ? (
                                                        <img
                                                            src={food.photoUrl}
                                                            alt={food.name}
                                                            className="meal-item__photo"
                                                        />
                                                    ) : (
                                                        <div className="meal-item__placeholder">🍽️</div>
                                                    )}
                                                    <span className="meal-item__name">
                                                        {food?.name || item.foodName || 'Aliment'}
                                                    </span>
                                                    <span className="meal-item__portion">
                                                        {item.portionGrams} g
                                                    </span>
                                                    {item.breadUnits !== undefined && item.breadUnits !== null && (
                                                        <span className="meal-item__bread-units">
                                                            {item.breadUnits} UP
                                                        </span>
                                                    )}
                                                    <Button
                                                        variant="secondary"
                                                        size="small"
                                                        onClick={() => onRemoveItem(item.id)}
                                                    >
                                                        Retirer
                                                    </Button>
                                                </div>
                                            );
                                        })
                                    ) : (
                                        <p>Aucun aliment dans ce repas.</p>
                                    )}
                                </div>
                                <Button
                                    variant="secondary"
                                    size="small"
                                    onClick={() => onAddItem(meal)}
                                >
                                    + Aliment
                                </Button>
                            </Card>
                        ))}
                    </div>
                )}
            </div>

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
                <div className="nutrition-page__right-content">
                    <Calendar
                        selectedDate={selectedDate}
                        onDateSelect={setSelectedDate}
                        markedDates={markedDates}
                    />
                </div>
            </RightSidebar>
        </div>
    );
}
