import { useState } from 'react';
import { usePatients } from '../hooks/usePatients';
import { PatientsTable } from '../components/PatientsTable';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Button } from '@/react/components/UI/Button';
import { SearchInput } from '@/react/components/Forms/SearchInput';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import '@/styles/pages/admin/patients/_patients.scss';
import { Modal } from "@/react/components/UI/Modal";

const FilterIcon = () => (
    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" strokeWidth="2">
        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" />
    </svg>
);

export function PatientsPage() {
    const { patients, isLoading, error } = usePatients();
    const [isAddModalOpen, setIsAddModalOpen] = useState(false);
    const [search, setSearch] = useState('');
    const [diabeteFilter, setDiabeteFilter] = useState<string>('Tous');
    const [showFilter, setShowFilter] = useState(false);
    const { pushAction } = useActionHistory();

    const openAddModal = () => {
        setIsAddModalOpen(true);
        pushAction(() => setIsAddModalOpen(false));
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
                {/* Envelopper le SearchInput dans une div flexible */}
                <div className="patients-page__search">
                    <SearchInput
                        placeholder="Rechercher un patient..."
                        value={search}
                        onSearch={(value: string) => setSearch(value)}
                    />
                </div>

                {/* Bouton filtre icône */}
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

                {/* Bouton ajouter */}
                <Button variant="primary" onClick={openAddModal} className="patients-page__add-btn">
                    + Ajouter un patient
                </Button>
            </div>

            <PatientsTable patients={filteredPatients} />

            {isAddModalOpen && (
                <Modal isOpen={isAddModalOpen} onClose={() => setIsAddModalOpen(false)}>
                    <p>Formulaire d'ajout de patient (à implémenter).</p>
                </Modal>
            )}
        </div>
    );
}
