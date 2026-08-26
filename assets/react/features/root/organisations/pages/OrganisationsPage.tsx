import { useState } from 'react';
import { useOrganisations } from '../hooks/useOrganisations';
import { OrganisationsTree } from '../components/OrganisationsTree';
import { OrganisationFormModal } from '../components/OrganisationFormModal';
import { OrganisationEditModal } from '../components/OrganisationEditModal';
import { EstablishmentFormModal } from '../components/EstablishmentFormModal';
import { EstablishmentEditModal } from '../components/EstablishmentEditModal';
import { DepartmentFormModal } from '../components/DepartmentFormModal';
import { DepartmentEditModal } from '../components/DepartmentEditModal';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Button } from '@/react/components/UI/Button';
import { SearchInput } from '@/react/components/Forms/SearchInput';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import '@/styles/pages/root/organisations/_organisations.scss';
import { CreateOrganisationPayload } from '../types';
import { Establishment } from '../types/establishment';
import { Department } from '../types/department';
import { TreeNode } from "@/react/hook-components/Data/Tree/types";

export function OrganisationsPage() {
    const { treeNodes, isLoading, error } = useOrganisations();
    const [modalCreateOpen, setModalCreateOpen] = useState(false);
    const [modalEditOpen, setModalEditOpen] = useState(false);
    const [editingOrg, setEditingOrg] = useState<CreateOrganisationPayload | null>(null);
    const [modalCreateEstOpen, setModalCreateEstOpen] = useState(false);
    const [modalEditEstOpen, setModalEditEstOpen] = useState(false);
    const [editingEst, setEditingEst] = useState<Establishment | null>(null);
    const [selectedOrgId, setSelectedOrgId] = useState<string>('');
    const [modalCreateDepOpen, setModalCreateDepOpen] = useState(false);
    const [modalEditDepOpen, setModalEditDepOpen] = useState(false);
    const [editingDep, setEditingDep] = useState<Department | null>(null);
    const [selectedFacilityId, setSelectedFacilityId] = useState<string>('');
    const [search, setSearch] = useState('');
    const { pushAction } = useActionHistory();

    const openAddModal = () => {
        setModalCreateOpen(true);
        pushAction(() => setModalCreateOpen(false));
    };

    const handleAction = (action: string, node: TreeNode) => {
        switch (action) {
            case 'modify':
                if (typeof node.data === 'object' && node.data !== null) {
                    const data = node.data as Record<string, unknown>;
                    if (data.dataType === 'organisation') {
                        setEditingOrg(data as unknown as CreateOrganisationPayload);
                        setModalEditOpen(true);
                        pushAction(() => setModalEditOpen(false));
                    } else if (data.dataType === 'establishment') {
                        setEditingEst(data as unknown as Establishment);
                        setModalEditEstOpen(true);
                        pushAction(() => setModalEditEstOpen(false));
                    } else if (data.dataType === 'department') {
                        setEditingDep(data as unknown as Department);
                        setModalEditDepOpen(true);
                        pushAction(() => setModalEditDepOpen(false));
                    }
                }
                break;
            case 'add-establishment':
                setSelectedOrgId(node.id);
                setModalCreateEstOpen(true);
                pushAction(() => setModalCreateEstOpen(false));
                break;
            case 'add-department':
                setSelectedFacilityId(node.id);
                setModalCreateDepOpen(true);
                pushAction(() => setModalCreateDepOpen(false));
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

            {/* Modales organisation */}
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

            {/* Modales établissement */}
            <EstablishmentFormModal
                isOpen={modalCreateEstOpen}
                onClose={() => setModalCreateEstOpen(false)}
                organizationId={selectedOrgId}
            />
            {editingEst && (
                <EstablishmentEditModal
                    isOpen={modalEditEstOpen}
                    onClose={() => setModalEditEstOpen(false)}
                    establishment={editingEst}
                />
            )}

            {/* Modales département */}
            <DepartmentFormModal
                isOpen={modalCreateDepOpen}
                onClose={() => setModalCreateDepOpen(false)}
                facilityId={selectedFacilityId}
            />
            {editingDep && (
                <DepartmentEditModal
                    isOpen={modalEditDepOpen}
                    onClose={() => setModalEditDepOpen(false)}
                    department={editingDep}
                />
            )}
        </div>
    );
}
