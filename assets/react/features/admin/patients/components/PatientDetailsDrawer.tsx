import React from 'react';
import { Drawer } from '@/react/components/UI/Drawer';
import { Avatar } from '@/react/components/UI/Avatar';
import { Badge } from '@/react/components/UI/Badge';
import { Button } from '@/react/components/UI/Button';
import { Patient } from '../types';

interface PatientDetailsDrawerProps {
    patient: Patient | null;
    isOpen: boolean;
    onClose: () => void;
    onModify: (patient: Patient) => void;
    onAttachToPeople: (patient: Patient) => void;
}

export function PatientDetailsDrawer({
                                         patient,
                                         isOpen,
                                         onClose,
                                         onModify,
                                         onAttachToPeople,
                                     }: PatientDetailsDrawerProps) {
    if (!patient) return null;

    return (
        <Drawer
            isOpen={isOpen}
            onClose={onClose}
            position="right"
            size="medium"
            className="patient-details-drawer"
        >
            <div className="patient-details">
                <div className="patient-details__header">
                    <Avatar
                        src={patient.avatarUrl}
                        name={patient.nom}
                        size="large"
                        shape="circle"
                    />
                    <h2>{patient.nom}</h2>
                    {patient.email && <p>{patient.email}</p>}
                </div>

                <div className="patient-details__body">
                    <p><strong>Date de naissance :</strong> {patient.dateNaissance}</p>
                    <p><strong>Type de diabète :</strong> {patient.typeDiabete}</p>
                    <p><strong>Équipe de soins :</strong> {patient.equipeSoins}</p>
                    {patient.telephone && <p><strong>Téléphone :</strong> {patient.telephone}</p>}
                    <p>
                        <strong>Statut :</strong>{' '}
                        <Badge variant={patient.statut === 'Active' ? 'success' : 'error'}>
                            {patient.statut}
                        </Badge>
                    </p>
                </div>

                <div className="patient-details__actions">
                    <Button variant="primary" onClick={() => onModify(patient)}>
                        Modifier
                    </Button>
                    <Button variant="secondary" onClick={() => onAttachToPeople(patient)}>
                        Attacher à des personnes
                    </Button>
                </div>
            </div>
        </Drawer>
    );
}
