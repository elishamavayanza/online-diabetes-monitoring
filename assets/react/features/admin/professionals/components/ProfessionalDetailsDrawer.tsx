import React, { useState } from 'react';
import { Drawer } from '@/react/components/UI/Drawer';
import { Avatar } from '@/react/components/UI/Avatar';
import { Badge } from '@/react/components/UI/Badge';
import { Button } from '@/react/components/UI/Button';
import { Switch } from '@/react/components/Forms/Switch';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { SearchInput } from '@/react/components/Forms/SearchInput'; // ✅ import
import { Professional } from '../types/types';
import { useAttachedPatients } from '../hooks/useAttachedPatients';

interface ProfessionalDetailsDrawerProps {
    professional: Professional | null;
    isOpen: boolean;
    onClose: () => void;
    onModify: (professional: Professional) => void;
    onAttachPatient: (professional: Professional) => void;
    onSuspend: (professional: Professional) => void;
    onReactivate: (professional: Professional) => void;
}

export function ProfessionalDetailsDrawer({
                                              professional,
                                              isOpen,
                                              onClose,
                                              onModify,
                                              onAttachPatient,
                                              onSuspend,
                                              onReactivate,
                                          }: ProfessionalDetailsDrawerProps) {
    const { patients, isLoading, error, toggleActive } = useAttachedPatients(
        professional?.id ?? ''
    );
    const [searchTerm, setSearchTerm] = useState('');

    if (!professional) return null;

    // Filtrer les patients selon la recherche
    const filteredPatients = patients.filter((patient) =>
        patient.nom.toLowerCase().includes(searchTerm.toLowerCase())
    );

    return (
        <Drawer
            isOpen={isOpen}
            onClose={onClose}
            position="right"
            size="medium"
            className="professional-details-drawer"
        >
            <div className="professional-details">
                <div className="professional-details__header">
                    <Avatar
                        src={professional.avatarUrl}
                        name={professional.nom}
                        size="xlarge"
                        shape="circle"
                    />
                    <h2>{professional.nom}</h2>
                    <p>{professional.email}</p>
                </div>

                <div className="professional-details__body">
                    <p><strong>Type :</strong> {professional.type}</p>
                    <p><strong>Spécialité :</strong> {professional.specialite || '—'}</p>
                    <p><strong>Établissement :</strong> {professional.etablissement || '—'}</p>
                    <p><strong>Département :</strong> {professional.departement || '—'}</p>
                    <p>
                        <strong>Statut :</strong>{' '}
                        <Badge variant={professional.statut === 'Active' ? 'success' : professional.statut === 'Suspended' ? 'warning' : 'error'}>
                            {professional.statut === 'Suspended' ? 'Suspendu' : professional.statut}
                        </Badge>
                    </p>
                </div>

                {/* Section patients attachés */}
                <div className="professional-details__patients">
                    <h3>Patients attachés</h3>

                    {!isLoading && !error && patients.length > 0 && (
                        <SearchInput
                            value={searchTerm}
                            inputProps={{
                                onChange: (e: React.ChangeEvent<HTMLInputElement>) =>
                                    setSearchTerm(e.target.value),
                            }}
                            placeholder="Rechercher un patient..."
                            fullWidth
                        />
                    )}

                    {isLoading ? (
                        <Spinner />
                    ) : error ? (
                        <Alert variant="error">{error}</Alert>
                    ) : filteredPatients.length === 0 ? (
                        <p>Aucun patient trouvé.</p>
                    ) : (
                        <ul className="attached-patients-list">
                            {filteredPatients.map((patient) => (
                                <li key={patient.assignmentId} className="attached-patient-item">
                                    <span>{patient.nom}</span>
                                    <Switch
                                        checked={patient.active}
                                        onChange={() => toggleActive(patient.assignmentId, patient.active)}
                                    />
                                </li>
                            ))}
                        </ul>
                    )}
                </div>

                <div className="professional-details__actions">
                    <Button variant="primary" onClick={() => onModify(professional)}>
                        Modifier
                    </Button>
                    <Button variant="secondary" onClick={() => onAttachPatient(professional)}>
                        Attacher un patient
                    </Button>
                    {professional.statut === 'Suspended' ? (
                        <Button variant="success" onClick={() => onReactivate(professional)}>
                            Réactiver
                        </Button>
                    ) : (
                        <Button variant="danger" onClick={() => onSuspend(professional)}>
                            Suspendre
                        </Button>
                    )}
                </div>
            </div>
        </Drawer>
    );
}
