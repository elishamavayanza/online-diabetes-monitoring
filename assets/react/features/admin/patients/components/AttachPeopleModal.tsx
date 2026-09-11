import React from 'react';
import { useI18n } from '@/react/i18n/I18nContext';
import { Modal } from '@/react/components/UI/Modal';
import { Button } from '@/react/components/UI/Button';
import { Alert } from '@/react/components/UI/Alert';
import { FormField } from '@/react/components/Forms/FormField';
import { Select } from '@/react/components/Forms/Select';
import { Input } from '@/react/components/Forms/Input';
import { Switch } from '@/react/components/Forms/Switch';
import { useAttachPeople } from '../hooks/useAttachPeople';
import { SearchableSelect } from "@/react/components/Forms/SearchableSelect/SearchableSelect";

interface AttachPeopleModalProps {
    isOpen: boolean;
    onClose: () => void;
    patientId: string;
    mode?: 'create' | 'edit';
    onSuccess?: () => void;
}

export function AttachPeopleModal({
                                      isOpen,
                                      onClose,
                                      patientId,
                                      mode = 'create',
                                      onSuccess,
                                  }: AttachPeopleModalProps) {
    const { t } = useI18n();
    const {
        professionals,
        assignments,
        addAssignment,
        removeAssignment,
        updateAssignment,
        submit,
        isSubmitting,
        error,
    } = useAttachPeople(patientId, mode);

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        const success = await submit();
        if (success) {
            onSuccess?.();
            onClose();
        }
    };

    const professionalOptions = professionals.map((p) => ({
        value: p.id,
        label: `${p.nom} (${p.specialty})`,
    }));

    const roleOptions = [
        { value: 'PRIMARY_CLINICIAN', label: t('Médecin principal') },
        { value: 'SPECIALIST', label: t('Spécialiste') },
        { value: 'NUTRITIONIST', label: t('Nutritionniste') },
    ];

    return (
        <Modal isOpen={isOpen} onClose={onClose} size="large">
            <div className="attach-people-modal">
                <h2>{mode === 'create' ? t("Attacher des professionnels") : t("Modifier l'équipe de soins")}</h2>
                {error && <Alert variant="error">{error}</Alert>}

                <form onSubmit={handleSubmit}>
                    {assignments.length === 0 ? (
                        <p>{t("Aucune affectation ajoutée. Cliquez sur « Ajouter ».")}</p>
                    ) : (
                        <div className="attach-people-modal__list">
                            {assignments.map((assignment, index) => (
                                <div key={assignment.id} className="attach-people-modal__row">
                                    <FormField label={`${t('Professionnel {{ index }}', { index: index + 1 })} *`}>
                                        <SearchableSelect
                                            options={professionalOptions}
                                            value={assignment.professionalId}
                                            onChange={(value) =>
                                                updateAssignment(assignment.id, 'professionalId', value)
                                            }
                                            placeholder={t('Rechercher un professionnel...')}
                                            required
                                        />
                                    </FormField>
                                    <FormField label={t('Rôle *')}>
                                        <Select
                                            value={assignment.role}
                                            onChange={(e) =>
                                                updateAssignment(assignment.id, 'role', e.target.value as any)
                                            }
                                            options={roleOptions}
                                        />
                                    </FormField>
                                    <FormField label={t('Début *')}>
                                        <Input
                                            type="date"
                                            value={assignment.startDate}
                                            onChange={(e) =>
                                                updateAssignment(assignment.id, 'startDate', e.target.value)
                                            }
                                            required
                                        />
                                    </FormField>
                                    <FormField label={t('Fin')}>
                                        <Input
                                            type="date"
                                            value={assignment.endDate}
                                            onChange={(e) =>
                                                updateAssignment(assignment.id, 'endDate', e.target.value)
                                            }
                                        />
                                    </FormField>
                                    <FormField label={t('Actif')}>
                                        <Switch
                                            checked={assignment.active}
                                            onChange={(e) =>
                                                updateAssignment(assignment.id, 'active', e.target.checked)
                                            }
                                        />
                                    </FormField>
                                    <Button
                                        type="button"
                                        variant="danger"
                                        size="small"
                                        onClick={() => removeAssignment(assignment.id)}
                                    >
                                        {t('Supprimer')}
                                    </Button>
                                </div>
                            ))}
                        </div>
                    )}

                    <div className="attach-people-modal__actions">
                        <Button type="button" variant="outline" onClick={addAssignment}>
                            {t('+ Ajouter')}
                        </Button>
                        <Button type="button" variant="outline" onClick={onClose}>
                            {t('Annuler')}
                        </Button>
                        <Button type="submit" disabled={isSubmitting || assignments.length === 0}>
                            {isSubmitting ? t('Enregistrement...') : t('Attacher')}
                        </Button>
                    </div>
                </form>
            </div>
        </Modal>
    );
}
