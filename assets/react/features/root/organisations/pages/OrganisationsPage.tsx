import { useOrganisations } from '../hooks/useOrganisations';
import { OrganisationsTable } from '../components/OrganisationsTable';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import '@/styles/pages/root/organisations/_organisations.scss';

export function OrganisationsPage() {
    const { organisations, isLoading, error } = useOrganisations();

    if (isLoading) {
        return <Spinner />;
    }

    if (error) {
        return <Alert variant="error">{error}</Alert>;
    }

    return (
        <div className="organisations-page">
            <div className="organisations-page__header">
                <h1>Organisations</h1>
                <p>Liste de toutes les organisations de la plateforme</p>
            </div>
            <OrganisationsTable organisations={organisations} />
        </div>
    );
}
