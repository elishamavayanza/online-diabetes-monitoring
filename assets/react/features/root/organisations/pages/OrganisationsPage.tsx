import { useState } from 'react';
import { useOrganisations } from '../hooks/useOrganisations';
import { OrganisationsTree } from '../components/OrganisationsTree';
import { OrganisationFormModal } from '../components/OrganisationFormModal';
import { OrganisationEditModal } from '../components/OrganisationEditModal';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Button } from '@/react/components/UI/Button';
import { SearchInput } from '@/react/components/Forms/SearchInput';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import '@/styles/pages/root/organisations/_organisations.scss';
import { CreateOrganisationPayload } from '../types';
import {TreeNode} from "@/react/hook-components/Data/Tree/types";

export function OrganisationsPage() {
    const { treeNodes, isLoading, error } = useOrganisations();
    const [modalCreateOpen, setModalCreateOpen] = useState(false);
    const [modalEditOpen, setModalEditOpen] = useState(false);
    const [editingOrg, setEditingOrg] = useState<CreateOrganisationPayload | null>(null);
    const [search, setSearch] = useState('');
    const { pushAction } = useActionHistory();

    const openAddModal = () => {
        setModalCreateOpen(true);
        pushAction(() => setModalCreateOpen(false));
    };

    const handleAction = (action: string, node: TreeNode) => {
        switch (action) {
            case 'modify':
                if (node.data) {
                    setEditingOrg(node.data as CreateOrganisationPayload);
                    setModalEditOpen(true);
                    pushAction(() => setModalEditOpen(false));
                }
                break;
            case 'add-establishment':
                // Ouvrir une modale pour ajouter un établissement (à implémenter)
                console.log('Ajouter établissement pour', node.label);
                break;
            case 'add-admin':
                console.log('Ajouter admin pour', node.label);
                break;
            case 'suspend':
                console.log('Suspendre', node.label);
                break;
            default:
                break;
        }
    };

    if (isLoading) return <Spinner />;
    if (error) return <Alert variant="error">{error}</Alert>;

    return (
        <div className="organisations-page">
            <div className="organisations-page__header">
                <h1>Organisations</h1>
                <p>Liste de toutes les organisations de la plateforme</p>
            </div>

            <div className="organisations-page__actions">
                <SearchInput
                    placeholder="Rechercher une organisation..."
                    value={search}
                    onSearch={(value) => setSearch(value)}
                    className="organisations-page__search"
                />
                <Button onClick={openAddModal} className="organisations-page__add-btn">Ajouter une organisation</Button>
            </div>

            <OrganisationsTree
                treeNodes={treeNodes}
                filter={search}
                onAction={handleAction}
            />

            {/* Modales */}
            <OrganisationFormModal
                isOpen={modalCreateOpen}
                onClose={() => setModalCreateOpen(false)}
            />
            {editingOrg && (
                <OrganisationEditModal
                    isOpen={modalEditOpen}
                    onClose={() => setModalEditOpen(false)}
                    organisationData={editingOrg}
                />
            )}
        </div>
    );
}
