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
import { OrgAdminFormModal } from '../components/OrgAdminFormModal';
import { NodeDetailsPanel } from '../components/NodeDetailsPanel';
import { OrganisationsTable } from '../components/OrganisationsTable';
import { SuspensionModal } from '@/react/features/security/components/SuspensionModal';
import { ReactivateModal } from '@/react/features/security/components/ReactivateModal';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';
import { suspendOrganisation, reactivateOrganisation } from '../services/organisationsService';
import { SuspensionPayload } from '@/react/features/security/types';
import { ApiError } from '@/services/api/api.types';

export function OrganisationsPage() {
    const { treeNodes, isLoading, error, refetch } = useOrganisations();
    const { showToast } = useToast();
    const [modalCreateOpen, setModalCreateOpen] = useState(false);
    const [modalEditOpen, setModalEditOpen] = useState(false);
    const [editingOrg, setEditingOrg] = useState<CreateOrganisationPayload | null>(null);
    const [editingOrgId, setEditingOrgId] = useState<string>(''); //  ID de l'organisation à modifier
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
    const [modalAdminOpen, setModalAdminOpen] = useState(false);
    const [selectedNode, setSelectedNode] = useState<TreeNode | null>(null);
    const [isDrawerOpen, setIsDrawerOpen] = useState(false);
    const [selectedAdminOrgId, setSelectedAdminOrgId] = useState<string>('');
    const [suspendingNode, setSuspendingNode] = useState<TreeNode | null>(null);
    const [reactivatingNode, setReactivatingNode] = useState<TreeNode | null>(null);

    const openAddModal = () => {
        setModalCreateOpen(true);
        pushAction(() => setModalCreateOpen(false));
    };

    const handleNodeClick = (node: TreeNode) => {
        setSelectedNode(node);
        setIsDrawerOpen(true);
    };

    const handleAddAdmin = (node: TreeNode) => {
        setSelectedAdminOrgId(node.id);
        setModalAdminOpen(true);
        pushAction(() => setModalAdminOpen(false));
    };

    const handleModify = (node: TreeNode) => {
        if (node.data && typeof node.data === 'object') {
            const data = node.data as Record<string, unknown>;
            if (data.dataType === 'organisation') {
                setEditingOrg(data as unknown as CreateOrganisationPayload);
                setEditingOrgId(node.id);
                setModalEditOpen(true);
            }
        }
    };

    const handleSuspend = (node: TreeNode) => {
        setSuspendingNode(node);
    };

    const handleConfirmSuspend = async (payload: SuspensionPayload) => {
        if (!suspendingNode) return;
        try {
            await suspendOrganisation(suspendingNode.id, payload);
            showToast({
                type: 'success',
                message: `L'organisation « ${suspendingNode.label} » a été suspendue.`,
            });
            await refetch();
        } catch (err) {
            const message = err instanceof ApiError ? (err.data?.message ?? err.message) : (err instanceof Error ? err.message : 'Une erreur est survenue.');
            showToast({ type: 'error', message });
            throw err;
        }
    };

    const handleReactivate = (node: TreeNode) => {
        setReactivatingNode(node);
    };

    const handleConfirmReactivate = async () => {
        if (!reactivatingNode) return;
        try {
            await reactivateOrganisation(reactivatingNode.id);
            showToast({
                type: 'success',
                message: `L'organisation « ${reactivatingNode.label} » a été réactivée.`,
            });
            await refetch();
        } catch (err) {
            const message = err instanceof ApiError ? (err.data?.message ?? err.message) : (err instanceof Error ? err.message : 'Une erreur est survenue.');
            showToast({ type: 'error', message });
            throw err;
        }
    };

    const handleAction = (action: string, node: TreeNode) => {
        switch (action) {
            case 'modify':
                if (typeof node.data === 'object' && node.data !== null) {
                    const data = node.data as Record<string, unknown>;
                    if (data.dataType === 'organisation') {
                        setEditingOrg(data as unknown as CreateOrganisationPayload);
                        setEditingOrgId(node.id);
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
                setSelectedAdminOrgId(node.id);
                setModalAdminOpen(true);
                pushAction(() => setModalAdminOpen(false));
                break;
            case 'suspend':
                setSuspendingNode(node);
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

            <OrganisationsTable
                treeNodes={treeNodes}
                onDetail={handleNodeClick}
                onModify={handleModify}
                onSuspend={handleSuspend}
                onReactivate={handleReactivate}
                onAddAdmin={handleAddAdmin}
            />

            {/* Modales organisation */}
            <OrganisationFormModal
                isOpen={modalCreateOpen}
                onClose={() => setModalCreateOpen(false)}
                onSuccess={refetch}
            />
            {editingOrg && (
                <OrganisationEditModal
                    isOpen={modalEditOpen}
                    onClose={() => setModalEditOpen(false)}
                    organisationId={editingOrgId}
                    organisationData={editingOrg}
                    onSuccess={refetch}
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

            {/* Modal admin */}
            <OrgAdminFormModal
                isOpen={modalAdminOpen}
                onClose={() => setModalAdminOpen(false)}
                organizationId={selectedAdminOrgId}
                onSuccess={refetch}   //  recharge après ajout admin
            />

            <NodeDetailsPanel
                isOpen={isDrawerOpen}
                onClose={() => setIsDrawerOpen(false)}
                node={selectedNode}
            />

            <SuspensionModal
                isOpen={!!suspendingNode}
                onClose={() => setSuspendingNode(null)}
                title="Suspendre une organisation"
                entityLabel={suspendingNode ? `Organisation : ${suspendingNode.label}` : ''}
                onConfirm={handleConfirmSuspend}
            />

            <ReactivateModal
                isOpen={!!reactivatingNode}
                onClose={() => setReactivatingNode(null)}
                title="Réactiver une organisation"
                message={reactivatingNode ? `Confirmer la réactivation de « ${reactivatingNode.label} » ? L'organisation et ses comptes pourront à nouveau accéder à la plateforme.` : ''}
                onConfirm={handleConfirmReactivate}
            />
        </div>
    );
}
