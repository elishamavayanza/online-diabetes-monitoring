import { useState } from 'react';
import { useFoods } from '../hooks/useFoods';
import { FoodsTable } from '../components/FoodsTable';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Button } from '@/react/components/UI/Button';
import { Modal } from '@/react/components/UI/Modal';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import '@/styles/pages/nutritionist/foods/_foods.scss';

export function FoodsPage() {
    const { foods, isLoading, error } = useFoods();
    const [isHelpOpen, setIsHelpOpen] = useState(false);
    const { pushAction } = useActionHistory();

    const openHelp = () => {
        setIsHelpOpen(true);
        pushAction(() => setIsHelpOpen(false));
    };

    if (isLoading) return <Spinner />;
    if (error) return <Alert variant="error">{error}</Alert>;

    return (
        <div className="foods-page">
            <div className="foods-page__header">
                <h1>Aliments</h1>
                <p>Base de données alimentaire</p>
                <Button variant="secondary" onClick={openHelp}>Aide</Button>
            </div>
            <FoodsTable foods={foods} />

            {isHelpOpen && (
                <Modal isOpen={isHelpOpen} onClose={() => setIsHelpOpen(false)}>
                    <p>Bibliothèque des aliments disponibles pour vos plans.</p>
                </Modal>
            )}
        </div>
    );
}
