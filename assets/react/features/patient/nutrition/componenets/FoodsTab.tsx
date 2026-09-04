// components/FoodsTab.tsx
import { Button } from '@/react/components/UI/Button';
import { FoodOption } from '../types';

interface FoodsTabProps {
    foods: FoodOption[];
    selectedFoods: FoodOption[];
    setSelectedFoods: (foods: FoodOption[]) => void;
    onCreatePlan: () => void;
}

export function FoodsTab({ foods, selectedFoods, setSelectedFoods, onCreatePlan }: FoodsTabProps) {
    const toggleFoodSelection = (food: FoodOption) => {
        setSelectedFoods(
            selectedFoods.some((f) => f.id === food.id)
                ? selectedFoods.filter((f) => f.id !== food.id)
                : [...selectedFoods, food]
        );
    };

    return (
        <>
            <div className="nutrition-page__header-actions">
                <p>Sélectionnez des aliments pour composer un plan</p>
                <Button
                    variant="primary"
                    disabled={selectedFoods.length === 0}
                    onClick={onCreatePlan}
                >
                    Créer un plan ({selectedFoods.length})
                </Button>
            </div>
            <div className="food-selection-grid">
                {foods.map((food) => (
                    <div
                        key={food.id}
                        className={`food-card ${selectedFoods.some((f) => f.id === food.id) ? 'food-card--selected' : ''}`}
                        onClick={() => toggleFoodSelection(food)}
                    >
                        {food.photoUrl ? (
                            <img src={food.photoUrl} alt={food.name} className="food-card__photo" />
                        ) : (
                            <div className="food-card__placeholder">🍽️</div>
                        )}
                        <span className="food-card__name">{food.name}</span>
                        <input
                            type="checkbox"
                            checked={selectedFoods.some((f) => f.id === food.id)}
                            onChange={() => toggleFoodSelection(food)}
                            onClick={(e) => e.stopPropagation()}
                        />
                    </div>
                ))}
            </div>
        </>
    );
}
