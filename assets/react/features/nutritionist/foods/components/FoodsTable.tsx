import { Card } from '@/react/components/UI/Card';
import { DataTable } from '@/react/components/Data/DataTable';
import { Button } from '@/react/components/UI/Button';
import { Food, FoodCategory } from '../types';

interface FoodsTableProps {
    foods: Food[];
    categories: FoodCategory[];
    onEdit: (food: Food) => void;
    onDelete: (food: Food) => void;
}

function getCategoryLabel(categories: FoodCategory[], categoryId: string): string {
    return categories.find((c) => c.id === categoryId)?.label ?? '—';
}

export function FoodsTable({ foods, categories, onEdit, onDelete }: FoodsTableProps) {
    const columns = [
        {
            key: 'photo',
            title: 'Photo',
            render: (row: Food) => (
                row.photoUrl ? (
                    <img
                        src={row.photoUrl}
                        alt={row.name}
                        className="foods-table__photo"
                    />
                ) : (
                    <span className="foods-table__no-photo">—</span>
                )
            ),
        },
        { key: 'name', title: 'Nom' },
        {
            key: 'categoryId',
            title: 'Catégorie',
            render: (row: Food) => getCategoryLabel(categories, row.categoryId),
        },
        {
            key: 'caloriesPer100g',
            title: 'Calories',
            render: (row: Food) => `${row.caloriesPer100g} kcal`,
        },
        {
            key: 'carbsPer100g',
            title: 'Glucides',
            render: (row: Food) => `${row.carbsPer100g} g`,
        },
        {
            key: 'proteinPer100g',
            title: 'Protéines',
            render: (row: Food) => `${row.proteinPer100g} g`,
        },
        {
            key: 'fatPer100g',
            title: 'Lipides',
            render: (row: Food) => `${row.fatPer100g} g`,
        },
        {
            key: 'actions',
            title: 'Actions',
            render: (row: Food) => (
                <div className="foods-table__actions">
                    <Button variant="secondary" size="small" onClick={() => onEdit(row)}>Modifier</Button>
                    <Button variant="danger" size="small" onClick={() => onDelete(row)}>Supprimer</Button>
                </div>
            ),
        },
    ];

    return (
        <Card className="foods-card">
            <DataTable columns={columns} data={foods} pageSize={10} />
        </Card>
    );
}
