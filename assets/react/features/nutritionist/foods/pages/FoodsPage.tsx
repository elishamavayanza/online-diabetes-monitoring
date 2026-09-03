import { useState } from 'react';
import { useFoods } from '../hooks/useFoods';
import { FoodsTable } from '../components/FoodsTable';
import { FoodFormModal } from '../components/FoodFormModal';
import { FoodEditModal } from '../components/FoodEditModal';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Button } from '@/react/components/UI/Button';
import { SearchInput } from '@/react/components/Forms/SearchInput';
import { Select } from '@/react/components/Forms/Select';
import { ConfirmDialog } from '@/react/components/UI/ConfirmDialog';
import { Food } from '../types';
import { foodToFormValues } from '../services/foodsService';
import '@/styles/pages/nutritionist/foods/_foods.scss';

export function FoodsPage() {
    const { foods, categories, filters, setFilters, isLoading, error, refetch, removeFood } = useFoods();
    const [isCreateOpen, setIsCreateOpen] = useState(false);
    const [editingFood, setEditingFood] = useState<Food | null>(null);
    const [deletingFood, setDeletingFood] = useState<Food | null>(null);
    const [isDeleting, setIsDeleting] = useState(false);

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

    if (isLoading) return <Spinner />;
    if (error) return <Alert variant="error">{error}</Alert>;

    return (
        <div className="foods-page">
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
                <Select
                    value={filters.categoryId}
                    onChange={(e) => setFilters({ ...filters, categoryId: e.target.value })}
                    options={categoryOptions}
                />
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
