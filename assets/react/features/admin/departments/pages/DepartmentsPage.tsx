import { useDepartments } from '../hooks/useDepartments';
import { DepartmentsTable } from '../components/DepartmentsTable';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Button } from '@/react/components/UI/Button';
import '@/styles/pages/admin/departments/_departments.scss';

export function DepartmentsPage() {
    const { departments, isLoading, error } = useDepartments();

    if (isLoading) {
        return <Spinner />;
    }

    if (error) {
        return <Alert variant="error">{error}</Alert>;
    }

    return (
        <div className="departments-page">
            <div className="departments-page__header">
                <h1>Départements</h1>
                <Button variant="primary" onClick={() => console.log('Nouveau département')}>
                    + Nouveau département
                </Button>
            </div>
            <DepartmentsTable departments={departments} />
        </div>
    );
}
