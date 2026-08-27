import { useState } from 'react';
import { useNavigate } from 'react-router-dom';   // ✅
import { useEstablishments, EstablishmentTreeNodeData } from '../hooks/useEstablishments';
import { EstablishmentsTreeTable } from '../components/EstablishmentsTreeTable';
import { EstablishmentDetailsDrawer } from '../components/EstablishmentDetailsDrawer';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Button } from '@/react/components/UI/Button';
import { SearchInput } from '@/react/components/Forms/SearchInput';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import { useAuth } from '@/react/app/providers/AuthProvider';
import '@/styles/pages/admin/establishments/_establishments.scss';
import { EstablishmentFormModal } from "@/react/features/root/organisations/components/EstablishmentFormModal";
import { DepartmentFormModal } from "@/react/features/root/organisations/components/DepartmentFormModal";
import { EstablishmentEditModal } from "@/react/features/root/organisations/components/EstablishmentEditModal";
import { DepartmentEditModal } from "@/react/features/root/organisations/components/DepartmentEditModal";
import { TreeTableNode } from "@/react/hook-components/Data/TreeTable/types";
// Types affichage (utilisés par le hook et le drawer)
import { Establishment as DisplayEstablishment } from "@/react/features/admin/establishments/types";
import { Department as DisplayDepartment } from "@/react/features/admin/departments/types";
// Types formulaire (utilisés par les modales d'édition)
import { Establishment as FormEstablishment } from "@/react/features/root/organisations/types/establishment";
import { Department as FormDepartment } from "@/react/features/root/organisations/types/department";

function toFormEstablishment(est: DisplayEstablishment): FormEstablishment {
    return {
        id: est.id,
        organizationId: 'current-org',
        name: est.nom,
        phone: est.telephone,
        address: { street: est.adresse || '', city: '', postalCode: '', country: 'RDC' },
    };
}

function toFormDepartment(dep: DisplayDepartment): FormDepartment {
    return {
        id: dep.id,
        facilityId: '',
        name: dep.nom,
        specialty: dep.specialite || '',
    };
}

export function EstablishmentsPage() {
    const navigate = useNavigate();   // ✅
    const { treeNodes, isLoading, error } = useEstablishments();
    const [isAddModalOpen, setIsAddModalOpen] = useState(false);
    const [search, setSearch] = useState('');
    const [isDepartmentModalOpen, setIsDepartmentModalOpen] = useState(false);
    const [selectedFacilityId, setSelectedFacilityId] = useState('');
    const [selectedNode, setSelectedNode] = useState<TreeTableNode<EstablishmentTreeNodeData> | null>(null);
    const [isDrawerOpen, setIsDrawerOpen] = useState(false);
    const [editingEstablishment, setEditingEstablishment] = useState<FormEstablishment | null>(null);
    const [isEstablishmentEditOpen, setIsEstablishmentEditOpen] = useState(false);
    const [editingDepartment, setEditingDepartment] = useState<FormDepartment | null>(null);
    const [isDepartmentEditOpen, setIsDepartmentEditOpen] = useState(false);

    const { pushAction } = useActionHistory();
    const { user } = useAuth();
    const organizationId = user?.organizationId ?? 'current-org';

    const openAddModal = () => {
        setIsAddModalOpen(true);
        pushAction(() => setIsAddModalOpen(false));
    };

    const openDepartmentModal = (node: TreeTableNode<EstablishmentTreeNodeData>) => {
        if (node.data?.type === 'establishment' && node.data.establishment) {
            setSelectedFacilityId(node.data.establishment.id);
            setIsDepartmentModalOpen(true);
            pushAction(() => setIsDepartmentModalOpen(false));
        }
    };

    const openDetailsDrawer = (node: TreeTableNode<EstablishmentTreeNodeData>) => {
        setSelectedNode(node);
        setIsDrawerOpen(true);
    };

    const handleModify = (node: TreeTableNode<EstablishmentTreeNodeData>) => {
        if (node.data?.type === 'establishment' && node.data.establishment) {
            setEditingEstablishment(toFormEstablishment(node.data.establishment as DisplayEstablishment));
            setIsEstablishmentEditOpen(true);
        } else if (node.data?.type === 'department' && node.data.department) {
            setEditingDepartment(toFormDepartment(node.data.department as DisplayDepartment));
            setIsDepartmentEditOpen(true);
        }
        setIsDrawerOpen(false);
    };

    // ✅ Double‑clic : redirige vers la page de détail
    const handleDoubleClick = (node: TreeTableNode<EstablishmentTreeNodeData>) => {
        const type = node.data?.type;
        if (!type) return;
        navigate(`/admin/establishments/${type}/${node.id}`);
    };

    if (isLoading) return <Spinner />;
    if (error) return <Alert variant="error">{error}</Alert>;

    return (
        <div className="establishments-page">
            <div className="establishments-page__header">
                <h1>Établissements / Départements</h1>
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
                onViewDetails={openDetailsDrawer}
                onNodeDoubleClick={handleDoubleClick}
            />

            {/* Modales création */}
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

            {/* Modales édition */}
            {editingEstablishment && (
                <EstablishmentEditModal
                    isOpen={isEstablishmentEditOpen}
                    onClose={() => setIsEstablishmentEditOpen(false)}
                    establishment={editingEstablishment}
                />
            )}
            {editingDepartment && (
                <DepartmentEditModal
                    isOpen={isDepartmentEditOpen}
                    onClose={() => setIsDepartmentEditOpen(false)}
                    department={editingDepartment}
                />
            )}

            <EstablishmentDetailsDrawer
                node={selectedNode}
                isOpen={isDrawerOpen}
                onClose={() => setIsDrawerOpen(false)}
                onModify={handleModify}
            />
        </div>
    );
}
