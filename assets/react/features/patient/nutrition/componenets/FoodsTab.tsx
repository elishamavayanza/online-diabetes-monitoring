// components/FoodsTab.tsx
import { useState } from 'react';
import { Button } from '@/react/components/UI/Button';
import { SearchInput } from '@/react/components/Forms/SearchInput';
import { FoodOption } from '../types';

const FilterIcon = () => (
    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" strokeWidth="2">
        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" />
    </svg>
);

interface FoodsTabProps {
    foods: FoodOption[];
    categories: string[];
    selectedFoods: FoodOption[];
    setSelectedFoods: (foods: FoodOption[]) => void;
    onCreatePlan: () => void;
}

export function FoodsTab({
                             foods,
                             categories,
                             selectedFoods,
                             setSelectedFoods,
                             onCreatePlan,
                         }: FoodsTabProps) {
    const [search, setSearch] = useState('');
    const [category, setCategory] = useState('');
    const [showCategoryFilter, setShowCategoryFilter] = useState(false);

    const categoryOptions = [
        { value: '', label: 'Toutes les catégories' },
        ...categories.map((cat) => ({ value: cat, label: cat })),
    ];

    const filteredFoods = foods.filter((food) => {
        const matchesSearch = food.name.toLowerCase().includes(search.toLowerCase());
        const matchesCategory = category === '' || food.category === category;
        return matchesSearch && matchesCategory;
    });

    const toggleFoodSelection = (food: FoodOption) => {
        setSelectedFoods(
            selectedFoods.some((f) => f.id === food.id)
                ? selectedFoods.filter((f) => f.id !== food.id)
                : [...selectedFoods, food]
        );
    };

    return (
        <>
            {/* Barre d'actions : bouton plan (large), filtre icône, recherche */}
            <div className="nutrition-page__header-actions">
                <div className="nutrition-page__plan-action">
                    <Button
                        variant="primary"
                        disabled={selectedFoods.length === 0}
                        onClick={onCreatePlan}
                        className="nutrition-page__plan-button"
                    >
                        Créer un plan ({selectedFoods.length})
                    </Button>
                </div>

                <div className="nutrition-page__filter-wrapper" onClick={(e) => e.stopPropagation()}>
                    <button
                        className={`nutrition-page__filter-btn ${category !== '' ? 'nutrition-page__filter-btn--active' : ''}`}
                        onClick={() => setShowCategoryFilter((prev) => !prev)}
                        aria-label="Filtrer par catégorie"
                        title="Filtrer par catégorie"
                    >
                        <FilterIcon />
                    </button>

                    {showCategoryFilter && (
                        <div className="nutrition-page__filter-dropdown">
                            {categoryOptions.map((option) => (
                                <div
                                    key={option.value}
                                    className={`nutrition-page__filter-option ${category === option.value ? 'nutrition-page__filter-option--selected' : ''}`}
                                    onClick={() => {
                                        setCategory(option.value);
                                        setShowCategoryFilter(false);
                                    }}
                                >
                                    {option.label}
                                </div>
                            ))}
                        </div>
                    )}
                </div>

                <div className="nutrition-page__search">
                    <SearchInput
                        value={search}
                        onSearch={setSearch}
                        placeholder="Rechercher un aliment..."
                        fullWidth
                    />
                </div>
            </div>

            <div className="food-selection-grid">
                {filteredFoods.length === 0 ? (
                    <p>Aucun aliment trouvé.</p>
                ) : (
                    filteredFoods.map((food) => (
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
                    ))
                )}
            </div>
        </>
    );
}
