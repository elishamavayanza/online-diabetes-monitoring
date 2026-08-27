import { useState } from 'react';
import { useEstablishments, EstablishmentTreeNodeData } from '../hooks/useEstablishments';
import { EstablishmentsTreeTable } from '../components/EstablishmentsTreeTable';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Button } from '@/react/components/UI/Button';
import { SearchInput } from '@/react/components/Forms/SearchInput';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import { useAuth } from '@/react/app/providers/AuthProvider';
import '@/styles/pages/admin/establishments/_establishments.scss';
import { EstablishmentFormModal } from "@/react/features/root/organisations/components/EstablishmentFormModal";
import { DepartmentFormModal } from "@/react/features/root/organisations/components/DepartmentFormModal";
import {TreeTableNode} from "@/react/hook-components/Data/TreeTable/types";

export function EstablishmentsPage() {
    const { treeNodes, isLoading, error } = useEstablishments();
    const [isAddModalOpen, setIsAddModalOpen] = useState(false);
    const [search, setSearch] = useState('');
    const [isDepartmentModalOpen, setIsDepartmentModalOpen] = useState(false);
    const [selectedFacilityId, setSelectedFacilityId] = useState('');
    const { pushAction } = useActionHistory();
    const { user } = useAuth();

    const organizationId = user?.organizationId ?? 'current-org';

    const openAddModal = () => {
        setIsAddModalOpen(true);
        pushAction(() => setIsAddModalOpen(false));
    };

    // ✅ Paramètre typé explicitement
    const openDepartmentModal = (node: TreeTableNode<EstablishmentTreeNodeData>) => {
        if (node.data?.type === 'establishment' && node.data.establishment) {
            setSelectedFacilityId(node.data.establishment.id);
            setIsDepartmentModalOpen(true);
            pushAction(() => setIsDepartmentModalOpen(false));
        }
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

            <EstablishmentsTreeTable
                nodes={treeNodes}
                filter={search}
                onAddDepartment={openDepartmentModal}
            />

            <EstablishmentFormModal
                isOpen={isAddModalOpen}
                onClose={() => setIsAddModalOpen(false)}
                organizationId={organizationId}
            />

            <DepartmentFormModal
                isOpen={isDepartmentModalOpen}
                onClose={() => setIsDepartmentModalOpen(false)}
                facilityId={selectedFacilityId}
            />
        </div>
    );
}
