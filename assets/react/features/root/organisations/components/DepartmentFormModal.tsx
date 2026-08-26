import React from 'react';
import { Modal } from '@/react/components/UI/Modal';
import { Form } from '@/react/components/Forms/Form';
import { FormField } from '@/react/components/Forms/FormField';
import { Input } from '@/react/components/Forms/Input';
import { Button } from '@/react/components/UI/Button';
import { Alert } from '@/react/components/UI/Alert';
import { useCreateDepartment } from '../hooks/useCreateDepartment';

interface DepartmentFormModalProps {
    isOpen: boolean;
    onClose: () => void;
    facilityId: string;  // établissement parent
}

export function DepartmentFormModal({ isOpen, onClose, facilityId }: DepartmentFormModalProps) {
    const { form, updateField, submit, isSubmitting, error } = useCreateDepartment(facilityId);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        submit();
    };

    return (
        <Modal isOpen={isOpen} onClose={onClose}>
            <div className="organisation-form-modal">
                <h2>Ajouter un département</h2>
                {error && <Alert variant="error">{error}</Alert>}
                <Form onSubmit={handleSubmit}>
                    <FormField label="Nom du département *">
                        <Input
                            value={form.name}
                            onChange={(e) => updateField('name', e.target.value)}
                            required
                        />
                    </FormField>
                    <FormField label="Spécialité">
                        <Input
                            value={form.specialty}
                            onChange={(e) => updateField('specialty', e.target.value)}
                        />
                    </FormField>

                    <div className="organisation-form-modal__actions">
                        <Button type="button" variant="outline" onClick={onClose}>Annuler</Button>
                        <Button type="submit" disabled={isSubmitting}>
                            {isSubmitting ? 'Création...' : 'Créer'}
                        </Button>
                    </div>
                </Form>
            </div>
        </Modal>
    );
}
