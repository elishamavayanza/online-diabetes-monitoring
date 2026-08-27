import { useState } from 'react';
import { useEstablishments } from '../hooks/useEstablishments';
import { EstablishmentsTreeTable } from '../components/EstablishmentsTreeTable';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Button } from '@/react/components/UI/Button';
import { Modal } from '@/react/components/UI/Modal';
import { SearchInput } from '@/react/components/Forms/SearchInput';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import '@/styles/pages/admin/establishments/_establishments.scss';

export function EstablishmentsPage() {
    const { treeNodes, isLoading, error } = useEstablishments();
    const [isAddModalOpen, setIsAddModalOpen] = useState(false);
    const [search, setSearch] = useState('');
    const { pushAction } = useActionHistory();

    const openAddModal = () => {
        setIsAddModalOpen(true);
        pushAction(() => setIsAddModalOpen(false));
    };

    if (isLoading) return <Spinner />;
    if (error) return <Alert variant="error">{error}</Alert>;

    return (
        <div className="establishments-page">
            <div className="establishments-page__header">
                <h1>Établissements</h1>
                <p>Gérez les établissements de votre organisation</p>
            </div>

            <div className="establishments-page__actions">
                <div className="establishments-page__search">
                    <SearchInput
                        placeholder="Rechercher un établissement ou un département..."
                        value={search}
                        onSearch={(value: string) => setSearch(value)}
                    />
                </div>
                <Button variant="primary" onClick={openAddModal}>
                    + Ajouter un établissement
                </Button>
            </div>

            <EstablishmentsTreeTable nodes={treeNodes} filter={search} />

            {isAddModalOpen && (
                <Modal isOpen={isAddModalOpen} onClose={() => setIsAddModalOpen(false)}>
                    <p>Formulaire d'ajout d'établissement (à implémenter).</p>
                </Modal>
            )}
        </div>
    );
}
