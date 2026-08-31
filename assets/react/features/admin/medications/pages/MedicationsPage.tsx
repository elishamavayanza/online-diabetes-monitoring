import { useState } from 'react';
import { useMedications } from '../hooks/useMedications';
import { MedicationsTable } from '../components/MedicationsTable';
import { MedicationFormModal } from '../components/MedicationFormModal';
import { MedicationEditModal } from '../components/MedicationEditModal';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Button } from '@/react/components/UI/Button';
import { SearchInput } from '@/react/components/Forms/SearchInput';
import { Medication, MedicationFormValues } from '../types/types';
import '@/styles/pages/admin/medications/_medications.scss';

function toFormValues(med: Medication): MedicationFormValues {
    return {
        name: med.name,
        category: med.category,
        description: med.description ?? '',
        insulinLevel: med.insulinLevel ?? 0,
        manufacturer: med.manufacturer ?? '',
        active: med.active,
    };
}

export function MedicationsPage() {
    const { medications, isLoading, error, filters, setFilters, refetch } = useMedications();
    const [isCreateOpen, setIsCreateOpen] = useState(false);
    const [editingMed, setEditingMed] = useState<{ id: string; data: MedicationFormValues } | null>(null);
    const [isEditOpen, setIsEditOpen] = useState(false);

    const handleEdit = (med: Medication) => {
        setEditingMed({ id: med.id, data: toFormValues(med) });
        setIsEditOpen(true);
    };

    if (isLoading) return <Spinner />;
    if (error) return <Alert variant="error">{error}</Alert>;

    return (
        <div className="medications-page">
            <div className="medications-page__header">
                <h1>Médicaments</h1>
                <p>Gérez le référentiel médicamenteux de votre organisation</p>
            </div>

            <div className="medications-page__actions">
                <div className="medications-page__search">
                    <SearchInput
                        placeholder="Rechercher un médicament..."
                        value={filters.search}
                        onSearch={(value) => setFilters({ ...filters, search: value })}
                    />
                </div>
                <Button variant="primary" onClick={() => setIsCreateOpen(true)}>
                    + Ajouter un médicament
                </Button>
            </div>

            <MedicationsTable
                medications={medications}
                onEdit={handleEdit}
                onDelete={(med) => console.log('Supprimer', med.id)}
            />

            <MedicationFormModal
                isOpen={isCreateOpen}
                onClose={() => setIsCreateOpen(false)}
                onSuccess={refetch}
            />

            {editingMed && (
                <MedicationEditModal
                    isOpen={isEditOpen}
                    onClose={() => setIsEditOpen(false)}
                    medicationId={editingMed.id}
                    medicationData={editingMed.data}
                    onSuccess={refetch}
                />
            )}
        </div>
    );
}
