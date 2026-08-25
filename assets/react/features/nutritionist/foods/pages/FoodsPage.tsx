import { useFoods } from '../hooks/useFoods';
import { FoodsTable } from '../components/FoodsTable';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import '@/styles/pages/nutritionist/foods/_foods.scss';

export function FoodsPage() {
    const { foods, isLoading, error } = useFoods();

    if (isLoading) return <Spinner />;
    if (error) return <Alert variant="error">{error}</Alert>;

    return (
        <div className="foods-page">
            <div className="foods-page__header">
                <h1>Aliments</h1>
                <p>Base de données alimentaire</p>
            </div>
            <FoodsTable foods={foods} />
        </div>
    );
}
