import React from 'react';
import { Modal } from '@/react/components/UI/Modal';
import { Form } from '@/react/components/Forms/Form';
import { FormField } from '@/react/components/Forms/FormField';
import { Input } from '@/react/components/Forms/Input';
import { Button } from '@/react/components/UI/Button';
import { Alert } from '@/react/components/UI/Alert';
import { useUpdateDepartment } from '../hooks/useUpdateDepartment';
import { Department } from '../types/department';
import { useI18n } from '@/react/i18n/I18nContext';

interface DepartmentEditModalProps {
    isOpen: boolean;
    onClose: () => void;
    department: Department;
}

export function DepartmentEditModal({ isOpen, onClose, department }: DepartmentEditModalProps) {
    const { t } = useI18n();
    const { form, updateField, submit, isSubmitting, error } = useUpdateDepartment(department);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        submit();
    };

    return (
        <Modal isOpen={isOpen} onClose={onClose}>
            <div className="organisation-form-modal">
                <h2>{t('Modifier le département')}</h2>
                {error && <Alert variant="error">{error}</Alert>}
                <Form onSubmit={handleSubmit}>
                    <FormField label={t('Nom du département *')}>
                        <Input
                            value={form.name}
                            onChange={(e) => updateField('name', e.target.value)}
                            required
                        />
                    </FormField>
                    <FormField label={t('Spécialité')}>
                        <Input
                            value={form.specialty}
                            onChange={(e) => updateField('specialty', e.target.value)}
                        />
                    </FormField>

                    <div className="organisation-form-modal__actions">
                        <Button type="button" variant="outline" onClick={onClose}>{t('Annuler')}</Button>
                        <Button type="submit" disabled={isSubmitting}>
                            {isSubmitting ? t('Mise à jour...') : t('Enregistrer')}
                        </Button>
                    </div>
                </Form>
            </div>
        </Modal>
    );
}
