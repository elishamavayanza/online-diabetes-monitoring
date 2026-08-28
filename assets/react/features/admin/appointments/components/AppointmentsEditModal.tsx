// components/AppointmentsEditModal.tsx
import React, { useState, useEffect } from 'react';
import { Modal } from '@/react/components/UI/Modal';
import { Input } from '@/react/components/Forms/Input';
import { Select } from '@/react/components/Forms/Select';
import { SearchableSelect } from '@/react/components/Forms/SearchableSelect/SearchableSelect';
import { Button } from '@/react/components/UI/Button';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { FormField } from '@/react/components/Forms/FormField';
import { useUpdateAppointment } from '../hooks/useUpdateAppointment';
import '@/styles/pages/admin/appointments/_appointments.scss';
import { Appointment, AppointmentFormData } from '../types';
import {
    patientOptions,
    professionalOptions,
    organizationOptions,
    facilityOptions,
    statusOptions,
} from '../data/options';

interface AppointmentsEditModalProps {
    isOpen: boolean;
    onClose: () => void;
    appointment: Appointment | null; // rendez-vous à modifier
    onUpdated?: (appointment: Appointment) => void;
}

const emptyFormData: AppointmentFormData = {
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

export function AppointmentsEditModal({
                                          isOpen,
                                          onClose,
                                          appointment,
                                          onUpdated,
                                      }: AppointmentsEditModalProps) {
    const { updateAppointment, isUpdating, error } = useUpdateAppointment();
    const [formData, setFormData] = useState<AppointmentFormData>(emptyFormData);

    // Pré-remplir le formulaire quand le modal s'ouvre avec un rendez-vous
    useEffect(() => {
        if (isOpen && appointment) {
            setFormData({
                patientId: appointment.patientId || '',
                professionalId: appointment.professionalId || '',
                organizationId: appointment.organizationId || '',
                facilityId: appointment.facilityId || '',
                scheduledAt: appointment.scheduledAt || '', // adapter le format
                durationMinutes: appointment.durationMinutes || 30,
                status: appointment.status || 'SCHEDULED',
                reason: appointment.reason || '',
                notes: appointment.notes || '',
            });
        }
    }, [isOpen, appointment]);

    const handleChange = (
        e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>
    ) => {
        const { name, value } = e.target;
        setFormData((prev: AppointmentFormData) => ({ ...prev, [name]: value }));
    };

    const handleSelectChange = (e: React.ChangeEvent<HTMLSelectElement>) => {
        const { name, value } = e.target;
        setFormData((prev: AppointmentFormData) => ({ ...prev, [name]: value }));
    };

    const handleSearchableSelectChange = (name: string, value: string) => {
        setFormData((prev: AppointmentFormData) => ({ ...prev, [name]: value }));
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        if (!appointment) return;
        const updated = await updateAppointment(appointment.id, formData);
        if (updated) {
            onUpdated?.(updated);
            onClose();
        }
    };

    return (
        <Modal isOpen={isOpen} onClose={onClose} title="Modifier le rendez-vous">
            {error && <Alert variant="error">{error}</Alert>}
            <form onSubmit={handleSubmit}>
                <div className="appointment-form-grid">
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

                <div className="appointment-form-actions">
                    <Button type="button" variant="secondary" onClick={onClose} disabled={isUpdating}>
                        Annuler
                    </Button>
                    <Button type="submit" variant="primary" disabled={isUpdating}>
                        {isUpdating ? <Spinner size="small" /> : 'Enregistrer'}
                    </Button>
                </div>
            </form>
        </Modal>
    );
}
