import React from 'react';
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
        { value: 'PRIMARY_CLINICIAN', label: 'Médecin principal' },
        { value: 'SPECIALIST', label: 'Spécialiste' },
        { value: 'NUTRITIONIST', label: 'Nutritionniste' },
    ];

    return (
        <Modal isOpen={isOpen} onClose={onClose} size="large">
            <div className="attach-people-modal">
                <h2>{mode === 'create' ? 'Attacher des professionnels' : "Modifier l'équipe de soins"}</h2>
                {error && <Alert variant="error">{error}</Alert>}

                <form onSubmit={handleSubmit}>
                    {assignments.length === 0 ? (
                        <p>Aucune affectation ajoutée. Cliquez sur « Ajouter ».</p>
                    ) : (
                        <div className="attach-people-modal__list">
                            {assignments.map((assignment, index) => (
                                <div key={assignment.id} className="attach-people-modal__row">
                                    <FormField label={`Professionnel ${index + 1} *`}>
                                        <SearchableSelect
                                            options={professionalOptions}
                                            value={assignment.professionalId}
                                            onChange={(value) =>
                                                updateAssignment(assignment.id, 'professionalId', value)
                                            }
                                            placeholder="Rechercher un professionnel..."
                                            required
                                        />
                                    </FormField>
                                    <FormField label="Rôle *">
                                        <Select
                                            value={assignment.role}
                                            onChange={(e) =>
                                                updateAssignment(assignment.id, 'role', e.target.value as any)
                                            }
                                            options={roleOptions}
                                        />
                                    </FormField>
                                    <FormField label="Début *">
                                        <Input
                                            type="date"
                                            value={assignment.startDate}
                                            onChange={(e) =>
                                                updateAssignment(assignment.id, 'startDate', e.target.value)
                                            }
                                            required
                                        />
                                    </FormField>
                                    <FormField label="Fin">
                                        <Input
                                            type="date"
                                            value={assignment.endDate}
                                            onChange={(e) =>
                                                updateAssignment(assignment.id, 'endDate', e.target.value)
                                            }
                                        />
                                    </FormField>
                                    <FormField label="Actif">
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
                                        Supprimer
                                    </Button>
                                </div>
                            ))}
                        </div>
                    )}

                    <div className="attach-people-modal__actions">
                        <Button type="button" variant="outline" onClick={addAssignment}>
                            + Ajouter
                        </Button>
                        <Button type="button" variant="outline" onClick={onClose}>
                            Annuler
                        </Button>
                        <Button type="submit" disabled={isSubmitting || assignments.length === 0}>
                            {isSubmitting ? 'Enregistrement...' : 'Attacher'}
                        </Button>
                    </div>
                </form>
            </div>
        </Modal>
    );
}
