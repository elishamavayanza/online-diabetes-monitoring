import { useState } from 'react';
import { usePatients } from '../hooks/usePatients';
import { PatientsTable } from '../components/PatientsTable';
import { PatientDetailsDrawer } from '../components/PatientDetailsDrawer';
import { PatientEditModal } from '../components/PatientEditModal';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Button } from '@/react/components/UI/Button';
import { SearchInput } from '@/react/components/Forms/SearchInput';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import '@/styles/pages/admin/patients/_patients.scss';
import { PatientFormModal } from '../components/PatientFormModal';
import { Patient } from '../types';
import { PatientFormValues } from "@/react/features/root/users/types/userForm.types";
import { AttachPeopleModal } from '../components/AttachPeopleModal';

const FilterIcon = () => (
    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" strokeWidth="2">
        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" />
    </svg>
);

// Convertit un Patient (affichage) en PatientFormValues (formulaire)
function toPatientFormValues(patient: Patient): PatientFormValues {
    return {
        email: patient.email ?? '',
        password: '', // non modifié ici
        fullName: patient.nom,
        phone: patient.telephone ?? '',
        gender: 'UNSPECIFIED',
        locale: 'fr',
        dateOfBirth: patient.dateNaissance,
        placeOfBirth: '',
        bloodType: '',
        heightCm: '',
        avatarUrl: patient.avatarUrl ?? '',
        avatarFile: null,
        address: { street: '', city: '', postalCode: '', country: 'RDC' },
    };
}

export function PatientsPage() {
    const { patients, isLoading, error, refetch } = usePatients(); // récupération de refetch
    const [isAddModalOpen, setIsAddModalOpen] = useState(false);
    const [search, setSearch] = useState('');
    const [diabeteFilter, setDiabeteFilter] = useState<string>('Tous');
    const [showFilter, setShowFilter] = useState(false);
    const [selectedPatient, setSelectedPatient] = useState<Patient | null>(null);
    const [isDrawerOpen, setIsDrawerOpen] = useState(false);

    // États pour l'édition
    const [editingPatientId, setEditingPatientId] = useState<string | null>(null);
    const [editingPatientData, setEditingPatientData] = useState<PatientFormValues | null>(null);
    const [isEditModalOpen, setIsEditModalOpen] = useState(false);
    const [isAttachPeopleOpen, setIsAttachPeopleOpen] = useState(false);
    const [selectedPatientId, setSelectedPatientId] = useState<string | null>(null);
    const [attachMode, setAttachMode] = useState<'create' | 'edit'>('create');

    const { pushAction } = useActionHistory();

    const openAddModal = () => {
        setIsAddModalOpen(true);
        pushAction(() => setIsAddModalOpen(false));
    };

    const openDetails = (patient: Patient) => {
        setSelectedPatient(patient);
        setIsDrawerOpen(true);
    };

    const closeDrawer = () => {
        setSelectedPatient(null);
        setIsDrawerOpen(false);
    };

    const handleModify = (patient: Patient) => {
        setEditingPatientId(patient.id);
        setEditingPatientData(toPatientFormValues(patient));
        setIsEditModalOpen(true);
        closeDrawer();
    };

    const handleAttachToPeople = (patient: Patient) => {
        setSelectedPatientId(patient.id);
        const mode = patient.equipeSoins && patient.equipeSoins.trim() !== '' ? 'edit' : 'create';
        setAttachMode(mode);
        setIsAttachPeopleOpen(true);
        closeDrawer();
    };

    if (isLoading) return <Spinner />;
    if (error) return <Alert variant="error">{error}</Alert>;

    const filteredPatients = patients.filter((patient) => {
        const q = search.toLowerCase();
        const matchSearch =
            patient.nom.toLowerCase().includes(q) ||
            patient.equipeSoins.toLowerCase().includes(q);
        const matchDiabete = diabeteFilter === 'Tous' || patient.typeDiabete === diabeteFilter;
        return matchSearch && matchDiabete;
    });

    const diabeteOptions = ['Tous', 'Type 1', 'Type 2', 'Gestationnel'];

    return (
        <div className="patients-page">
            <div className="patients-page__header">
                <h1>Patients</h1>
                <p>Gérez les patients de votre organisation</p>
            </div>

            <div className="patients-page__actions">
                <div className="patients-page__search">
                    <SearchInput
                        placeholder="Rechercher un patient..."
                        value={search}
                        onSearch={(value: string) => setSearch(value)}
                    />
                </div>

                <div className="patients-page__filter-wrapper">
                    <button
                        className={`patients-page__filter-btn ${diabeteFilter !== 'Tous' ? 'patients-page__filter-btn--active' : ''}`}
                        onClick={() => setShowFilter((prev) => !prev)}
                        aria-label="Filtrer par type de diabète"
                        title="Filtrer par type de diabète"
                    >
                        <FilterIcon />
                    </button>

                    {showFilter && (
                        <div className="patients-page__filter-dropdown">
                            {diabeteOptions.map((option) => (
                                <div
                                    key={option}
                                    className={`patients-page__filter-option ${diabeteFilter === option ? 'patients-page__filter-option--selected' : ''}`}
                                    onClick={() => {
                                        setDiabeteFilter(option);
                                        setShowFilter(false);
                                    }}
                                >
                                    {option}
                                </div>
                            ))}
                        </div>
                    )}
                </div>

                <Button variant="primary" onClick={openAddModal} className="patients-page__add-btn">
                    + Ajouter un patient
                </Button>
            </div>

            <PatientsTable
                patients={filteredPatients}
                onViewDetails={openDetails}
            />

            {/* Modale de création  onSuccess={refetch} */}
            <PatientFormModal
                isOpen={isAddModalOpen}
                onClose={() => setIsAddModalOpen(false)}
                onSuccess={refetch}
            />

            {/* Modale d'édition  onSuccess={refetch} */}
            {editingPatientId && editingPatientData && (
                <PatientEditModal
                    isOpen={isEditModalOpen}
                    onClose={() => setIsEditModalOpen(false)}
                    patientId={editingPatientId}
                    patientData={editingPatientData}
                    onSuccess={refetch}
                />
            )}

            {/* Drawer de détails */}
            <PatientDetailsDrawer
                patient={selectedPatient}
                isOpen={isDrawerOpen}
                onClose={closeDrawer}
                onModify={handleModify}
                onAttachToPeople={handleAttachToPeople}
            />

            {/* Modale d'attachement  onSuccess={refetch} */}
            {selectedPatientId && (
                <AttachPeopleModal
                    isOpen={isAttachPeopleOpen}
                    onClose={() => setIsAttachPeopleOpen(false)}
                    patientId={selectedPatientId!}
                    mode={attachMode}
                    onSuccess={refetch}
                />
            )}
        </div>
    );
}
