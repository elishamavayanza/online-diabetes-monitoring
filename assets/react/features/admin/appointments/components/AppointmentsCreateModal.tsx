import React, { useState, useEffect } from 'react';
import { Modal } from '@/react/components/UI/Modal';
import { Input } from '@/react/components/Forms/Input';
import { Select } from '@/react/components/Forms/Select';
import { Button } from '@/react/components/UI/Button';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { FormField } from '@/react/components/Forms/FormField';
import { useCreateAppointment } from '../hooks/useCreateAppointment';
import '@/styles/pages/admin/appointments/_appointments.scss';
import { Appointment, AppointmentFormData } from '../types';
import {
    patientOptions,
    professionalOptions,
    organizationOptions,
    facilityOptions,
    statusOptions,
} from '../data/options';
import {SearchableSelect} from "@/react/components/Forms/SearchableSelect/SearchableSelect";

interface AppointmentsCreateModalProps {
    isOpen: boolean;
    onClose: () => void;
    onCreated?: (appointment: Appointment) => void;
}

const initialFormData: AppointmentFormData = {
    patientId: '',
    professionalId: '',
    organizationId: '',
    facilityId: '',
    scheduledAt: '',
    durationMinutes: 30,
    status: 'SCHEDULED',
    reason: '',
    notes: '',
};

export function AppointmentsCreateModal({
                                            isOpen,
                                            onClose,
                                            onCreated,
                                        }: AppointmentsCreateModalProps) {
    const { createAppointment, isLoading, error } = useCreateAppointment();
    const [formData, setFormData] = useState<AppointmentFormData>(initialFormData);

    useEffect(() => {
        if (!isOpen) {
            setFormData(initialFormData);
        }
    }, [isOpen]);

    // Gestion des champs Input/Select natifs
    const handleChange = (
        e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>
    ) => {
        const { name, value } = e.target;
        setFormData((prev: AppointmentFormData) => ({ ...prev, [name]: value }));
    };

    // Gestion du Select natif (Organisation, Établissement, Statut)
    const handleSelectChange = (e: React.ChangeEvent<HTMLSelectElement>) => {
        const { name, value } = e.target;
        setFormData((prev: AppointmentFormData) => ({ ...prev, [name]: value }));
    };

    // Gestion du SearchableSelect (Patient, Professionnel)
    const handleSearchableSelectChange = (name: string, value: string) => {
        setFormData((prev: AppointmentFormData) => ({ ...prev, [name]: value }));
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        const newAppointment = await createAppointment(formData);
        if (newAppointment) {
            onCreated?.(newAppointment);
            onClose();
        }
    };

    return (
        <Modal isOpen={isOpen} onClose={onClose} title="Nouveau rendez-vous">
            {error && <Alert variant="error">{error}</Alert>}
            <form onSubmit={handleSubmit}>
                {/* Grille 50/50 via classe SCSS */}
                <div className="appointment-form-grid">
                    {/* Rangée 1 : Patient (SearchableSelect) + Professionnel (SearchableSelect) */}
                    <FormField label="Patient" htmlFor="patientId" required>
                        <SearchableSelect
                            options={patientOptions}
                            value={formData.patientId}
                            onChange={(value) => handleSearchableSelectChange('patientId', value)}
                            placeholder="Sélectionner un patient"
                            required
                        />
                    </FormField>

                    <FormField label="Professionnel" htmlFor="professionalId" required>
                        <SearchableSelect
                            options={professionalOptions}
                            value={formData.professionalId}
                            onChange={(value) => handleSearchableSelectChange('professionalId', value)}
                            placeholder="Sélectionner un professionnel"
                            required
                        />
                    </FormField>

                    {/* Rangée 2 : Organisation (Select natif) + Établissement (Select natif) */}
                    <FormField label="Organisation" htmlFor="organizationId" required>
                        <Select
                            id="organizationId"
                            name="organizationId"
                            value={formData.organizationId}
                            onChange={handleSelectChange}
                            options={organizationOptions}
                            placeholder="Sélectionner une organisation"
                            required
                        />
                    </FormField>

                    <FormField label="Établissement (optionnel)" htmlFor="facilityId">
                        <Select
                            id="facilityId"
                            name="facilityId"
                            value={formData.facilityId || ''}
                            onChange={handleSelectChange}
                            options={facilityOptions}
                        />
                    </FormField>

                    {/* Rangée 3 : Date et heure + Durée */}
                    <FormField label="Date et heure" htmlFor="scheduledAt" required>
                        <Input
                            type="datetime-local"
                            id="scheduledAt"
                            name="scheduledAt"
                            value={formData.scheduledAt}
                            onChange={handleChange}
                            required
                        />
                    </FormField>

                    <FormField label="Durée (minutes)" htmlFor="durationMinutes" required>
                        <Input
                            type="number"
                            id="durationMinutes"
                            name="durationMinutes"
                            value={formData.durationMinutes}
                            onChange={handleChange}
                            min={5}
                            step={5}
                            required
                        />
                    </FormField>

                    {/* Rangée 4 : Statut (Select natif) + Motif */}
                    <FormField label="Statut" htmlFor="status" required>
                        <Select
                            id="status"
                            name="status"
                            value={formData.status}
                            onChange={handleSelectChange}
                            options={statusOptions}
                            required
                        />
                    </FormField>

                    <FormField label="Motif" htmlFor="reason">
                        <Input
                            type="text"
                            id="reason"
                            name="reason"
                            value={formData.reason || ''}
                            onChange={handleChange}
                            placeholder="Motif du rendez-vous"
                        />
                    </FormField>
                </div>

                {/* Champ pleine largeur pour Notes */}
                <FormField label="Notes" htmlFor="notes">
                    <Input
                        type="text"
                        id="notes"
                        name="notes"
                        value={formData.notes || ''}
                        onChange={handleChange}
                        placeholder="Notes supplémentaires"
                    />
                </FormField>

                {/* Actions */}
                <div className="appointment-form-actions">
                    <Button type="button" variant="secondary" onClick={onClose} disabled={isLoading}>
                        Annuler
                    </Button>
                    <Button type="submit" variant="primary" disabled={isLoading}>
                        {isLoading ? <Spinner size="small" /> : 'Créer'}
                    </Button>
                </div>
            </form>
        </Modal>
    );
}
