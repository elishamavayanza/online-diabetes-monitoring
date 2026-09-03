import { useState } from 'react';
import { useFoods } from '../hooks/useFoods';
import { FoodsTable } from '../components/FoodsTable';
import { FoodFormModal } from '../components/FoodFormModal';
import { FoodEditModal } from '../components/FoodEditModal';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Button } from '@/react/components/UI/Button';
import { SearchInput } from '@/react/components/Forms/SearchInput';
import { ConfirmDialog } from '@/react/components/UI/ConfirmDialog';
import {Food, FoodCategory} from '../types';
import { foodToFormValues } from '../services/foodsService';
import '@/styles/pages/nutritionist/foods/_foods.scss';

const FilterIcon = () => (
    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" strokeWidth="2">
        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" />
    </svg>
);

export function FoodsPage() {
    const { foods, categories, filters, setFilters, isLoading, error, refetch, removeFood } = useFoods();
    const [isCreateOpen, setIsCreateOpen] = useState(false);
    const [editingFood, setEditingFood] = useState<Food | null>(null);
    const [deletingFood, setDeletingFood] = useState<Food | null>(null);
    const [isDeleting, setIsDeleting] = useState(false);
    const [showCategoryFilter, setShowCategoryFilter] = useState(false);

    const categoryOptions = [
        { value: '', label: 'Toutes les catégories' },
        ...categories.map((c) => ({ value: c.id, label: c.label })),
    ];

    const handleDelete = async () => {
        if (!deletingFood) return;
        setIsDeleting(true);
        const success = await removeFood(deletingFood.id);
        setIsDeleting(false);
        if (success) setDeletingFood(null);
    };

    const handleCategoryCreated = (newCategory: FoodCategory) => {
        // Recharge les catégories et aliments depuis le backend
        refetch();
    };

    // Fermer le dropdown si on clique ailleurs (optionnel)
    const closeFilter = () => setShowCategoryFilter(false);

    if (isLoading) return <Spinner />;
    if (error) return <Alert variant="error">{error}</Alert>;

    return (
        <div className="foods-page" onClick={closeFilter}>
            <div className="foods-page__header">
                <h1>Aliments</h1>
                <p>Base de données alimentaire pour vos plans nutritionnels</p>
            </div>

            <div className="foods-page__actions">
                <div className="foods-page__search">
                    <SearchInput
                        placeholder="Rechercher un aliment..."
                        value={filters.search}
                        onSearch={(value) => setFilters({ ...filters, search: value })}
                    />
                </div>

                <div className="foods-page__filter-wrapper" onClick={(e) => e.stopPropagation()}>
                    <button
                        className={`foods-page__filter-btn ${filters.categoryId !== '' ? 'foods-page__filter-btn--active' : ''}`}
                        onClick={() => setShowCategoryFilter((prev) => !prev)}
                        aria-label="Filtrer par catégorie"
                        title="Filtrer par catégorie"
                    >
                        <FilterIcon />
                    </button>

                    {showCategoryFilter && (
                        <div className="foods-page__filter-dropdown">
                            {categoryOptions.map((option) => (
                                <div
                                    key={option.value}
                                    className={`foods-page__filter-option ${filters.categoryId === option.value ? 'foods-page__filter-option--selected' : ''}`}
                                    onClick={() => {
                                        setFilters({ ...filters, categoryId: option.value });
                                        setShowCategoryFilter(false);
                                    }}
                                >
                                    {option.label}
                                </div>
                            ))}
                        </div>
                    )}
                </div>

                <Button variant="primary" onClick={() => setIsCreateOpen(true)}>
                    + Ajouter un aliment
                </Button>
            </div>

            <FoodsTable
                foods={foods}
                categories={categories}
                onEdit={setEditingFood}
                onDelete={setDeletingFood}
            />

            <FoodFormModal
                isOpen={isCreateOpen}
                onClose={() => setIsCreateOpen(false)}
                onSuccess={refetch}
                categories={categories}
                onCategoryCreated={handleCategoryCreated}
            />

            {editingFood && (
                <FoodEditModal
                    isOpen={!!editingFood}
                    onClose={() => setEditingFood(null)}
                    foodId={editingFood.id}
                    foodData={foodToFormValues(editingFood)}
                    categories={categories}
                    onSuccess={refetch}
                />
            )}

            <ConfirmDialog
                isOpen={!!deletingFood}
                onClose={() => setDeletingFood(null)}
                onConfirm={handleDelete}
                title="Supprimer l'aliment"
                message={`Voulez-vous supprimer « ${deletingFood?.name} » ? Cette action est irréversible.`}
                confirmLabel={isDeleting ? 'Suppression...' : 'Supprimer'}
                cancelLabel="Annuler"
            />
        </div>
    );
}
