import { Modal } from '@/react/components/UI/Modal';
import { Button } from '@/react/components/UI/Button';
import { Input } from '@/react/components/Forms/Input';
import { FormField } from '@/react/components/Forms/FormField';
import { Textarea } from '@/react/components/Forms/Textarea';
import { Spinner } from '@/react/components/UI/Spinner';
import { PatientDossierData, PatientMedicalNote } from '../../types';
import {useMedicalNoteEditForm} from "@/react/features/clinician/patients/hooks/Note/useMedicalNoteEditForm";

interface MedicalNoteEditModalProps {
    isOpen: boolean;
    onClose: () => void;
    data: PatientDossierData;
    note: PatientMedicalNote;
    onSuccess: () => void;
}

export function MedicalNoteEditModal({
                                         isOpen,
                                         onClose,
                                         data,
                                         note,
                                         onSuccess,
                                     }: MedicalNoteEditModalProps) {
    const { form, isLoading, handleChange, handleSubmit } = useMedicalNoteEditForm({
        data,
        note,
        onSuccess,
        onClose,
    });

    return (
        <Modal isOpen={isOpen} onClose={onClose} title="Modifier la note médicale">
            <form onSubmit={handleSubmit} className="dossier-form">
                <FormField label="Date de la note" htmlFor="notedAt" required>
                    <Input
                        id="notedAt"
                        name="notedAt"
                        type="datetime-local"
                        value={form.notedAt}
                        onChange={handleChange}
                        required
                    />
                </FormField>
                <FormField label="Contenu" htmlFor="content" required>
                    <Textarea
                        id="content"
                        name="content"
                        rows={6}
                        value={form.content}
                        fullWidth
                        required
                        onChange={handleChange}
                    />
                </FormField>
                <div className="dossier-form__actions">
                    <Button type="button" variant="secondary" onClick={onClose} disabled={isLoading}>
                        Annuler
                    </Button>
                    <Button type="submit" variant="primary" disabled={isLoading}>
                        {isLoading ? <Spinner size="small" /> : 'Enregistrer'}
                    </Button>
                </div>
            </form>
        </Modal>
    );
}
