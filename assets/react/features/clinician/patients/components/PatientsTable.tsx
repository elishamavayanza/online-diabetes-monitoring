import { useNavigate } from 'react-router-dom';
import { Card } from '@/react/components/UI/Card';
import { Badge } from '@/react/components/UI/Badge';
import { Avatar } from '@/react/components/UI/Avatar';
import { Button } from '@/react/components/UI/Button';
import { ClinicianPatient } from '../types';

interface PatientsTableProps {
    patients: ClinicianPatient[];
}

export function PatientsTable({ patients }: PatientsTableProps) {
    const navigate = useNavigate();

    const handleOpenRecord = (patient: ClinicianPatient) => {
        navigate(`/clinician/patients/${patient.id}/record`);
    };

    const handleInitRecord = (patient: ClinicianPatient) => {
        navigate(`/clinician/patients/${patient.id}/record/init`);
    };

    const handleClosedRecord = (patient: ClinicianPatient) => {
        navigate(`/clinician/patients/${patient.id}/record/closed`);
    };

    return (
        <div className="clinician-patients-cards">
            {patients.map((patient) => {
                const isOpen = patient.medicalRecordStatus === 'open';
                const isClosed = patient.medicalRecordStatus === 'closed';
                const hasNoRecord = patient.medicalRecordStatus === 'none';

                // La carte est cliquable si le dossier est ouvert ou fermé (mais pas si aucun dossier)
                const isClickable = isOpen || isClosed;

                return (
                    <Card
                        key={patient.id}
                        className="clinician-patient-card"
                        interactive={isClickable}
                        onClick={
                            isClickable
                                ? () => {
                                    if (isOpen) handleOpenRecord(patient);
                                    else if (isClosed) handleClosedRecord(patient);
                                }
                                : undefined
                        }
                    >
                        <div className="clinician-patient-card__photo">
                            <Avatar
                                src={patient.avatarUrl}
                                name={patient.nom}
                                size="xlarge"
                                shape="circle"
                            />
                        </div>

                        <div className="clinician-patient-card__info">
                            <h3 className="clinician-patient-card__name">{patient.nom}</h3>
                            <p className="clinician-patient-card__detail">
                                <span className="clinician-patient-card__label">Date de naissance :</span>{' '}
                                {patient.dateNaissance ?? 'Non renseignée'}
                            </p>
                            <p className="clinician-patient-card__detail">
                                <span className="clinician-patient-card__label">Téléphone :</span>{' '}
                                {patient.telephone ?? 'Non renseigné'}
                            </p>
                        </div>

                        <div className="clinician-patient-card__status">
                            <Badge variant={patient.statut === 'Active' ? 'success' : 'error'}>
                                {patient.statut}
                            </Badge>
                        </div>

                        {hasNoRecord && (
                            <div className="clinician-patient-card__action">
                                <Button
                                    variant="primary"
                                    size="small"
                                    onClick={(e) => {
                                        e.stopPropagation(); // évite le clic sur la carte
                                        handleInitRecord(patient);
                                    }}
                                >
                                    Ouvrir un dossier
                                </Button>
                            </div>
                        )}

                        {isClosed && (
                            <div className="clinician-patient-card__closed-badge">
                                <Badge variant="warning">Dossier fermé</Badge>
                            </div>
                        )}
                    </Card>
                );
            })}
        </div>
    );
}
