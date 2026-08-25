import { useMealPlans } from '../hooks/useMealPlans';
import { MealPlansTable } from '../components/MealPlansTable';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Button } from '@/react/components/UI/Button';
import '@/styles/pages/nutritionist/plans/_plans.scss';

export function MealPlansPage() {
    const { plans, isLoading, error } = useMealPlans();

    if (isLoading) return <Spinner />;
    if (error) return <Alert variant="error">{error}</Alert>;

    return (
        <div className="meal-plans-page">
            <div className="meal-plans-page__header">
                <h1>Plans alimentaires</h1>
                <Button variant="primary" onClick={() => console.log('Nouveau plan')}>+ Nouveau plan</Button>
            </div>
            <MealPlansTable plans={plans} />
        </div>
    );
}
