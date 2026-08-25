import { Card } from '@/react/components/UI/Card';
import { DataTable } from '@/react/components/Data/DataTable';
import { Food } from '../types';

interface FoodsTableProps {
    foods: Food[];
}

export function FoodsTable({ foods }: FoodsTableProps) {
    const columns = [
        { key: 'nom', title: 'Nom' },
        { key: 'categorie', title: 'Catégorie' },
        { key: 'glucides', title: 'Glucides (g)' },
        { key: 'calories', title: 'Calories (kcal)' },
    ];

    return (
        <Card className="foods-card">
            <DataTable columns={columns} data={foods} />
        </Card>
    );
}
