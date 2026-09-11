import React from 'react';
import { useI18n } from '@/react/i18n/I18nContext';
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
    onSuspend: (patient: Patient) => void;
    onReactivate: (patient: Patient) => void;
}

export function PatientDetailsDrawer({
                                         patient,
                                         isOpen,
                                         onClose,
                                         onModify,
                                         onAttachToPeople,
                                         onSuspend,
                                         onReactivate,
                                     }: PatientDetailsDrawerProps) {
    const { t } = useI18n();

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
                        size="xlarge"
                        shape="circle"
                    />
                    <h2>{patient.nom}</h2>
                    {patient.email && <p>{patient.email}</p>}
                </div>

                <div className="patient-details__body">
                    <p><strong>{t('Date de naissance :')}</strong> {patient.dateNaissance}</p>
                    <p><strong>{t('Type de diabète :')}</strong> {patient.typeDiabete}</p>
                    <p><strong>{t('Équipe de soins :')}</strong> {patient.equipeSoins}</p>
                    {patient.telephone && <p><strong>{t('Téléphone :')}</strong> {patient.telephone}</p>}
                    <p>
                        <strong>{t('Statut :')}</strong>{' '}
                        <Badge variant={patient.statut === 'Active' ? 'success' : patient.statut === 'Suspended' ? 'warning' : 'error'}>
                            {patient.statut === 'Suspended' ? t('Suspendu') : patient.statut}
                        </Badge>
                    </p>
                </div>

                <div className="patient-details__actions">
                    <Button variant="primary" onClick={() => onModify(patient)}>
                        {t('Modifier')}
                    </Button>
                    <Button variant="secondary" onClick={() => onAttachToPeople(patient)}>
                        {t('Attacher à des personnes')}
                    </Button>
                    {patient.statut === 'Suspended' ? (
                        <Button variant="success" onClick={() => onReactivate(patient)}>
                            {t('Réactiver')}
                        </Button>
                    ) : (
                        <Button variant="danger" onClick={() => onSuspend(patient)}>
                            {t('Suspendre')}
                        </Button>
                    )}
                </div>
            </div>
        </Drawer>
    );
}
