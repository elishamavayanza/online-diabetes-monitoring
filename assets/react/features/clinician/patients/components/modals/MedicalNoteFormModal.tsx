import { Modal } from '@/react/components/UI/Modal';
import { Button } from '@/react/components/UI/Button';
import { Input } from '@/react/components/Forms/Input';
import { FormField } from '@/react/components/Forms/FormField';
import { Textarea } from '@/react/components/Forms/Textarea';
import { Spinner } from '@/react/components/UI/Spinner';
import { PatientDossierData } from '../../types';
import {useMedicalNoteForm} from "@/react/features/clinician/patients/hooks/Note/useMedicalNoteForm";

interface MedicalNoteFormModalProps {
    isOpen: boolean;
    onClose: () => void;
    data: PatientDossierData;
    defaultDate?: Date | null;
    onSuccess: () => void;
}

export function MedicalNoteFormModal({
                                         isOpen,
                                         onClose,
                                         data,
                                         defaultDate,
                                         onSuccess,
                                     }: MedicalNoteFormModalProps) {
    const { form, isLoading, handleChange, handleSubmit } = useMedicalNoteForm({
        data,
        defaultDate,
        onSuccess,
        onClose,
    });

    return (
        <Modal isOpen={isOpen} onClose={onClose} title="Nouvelle note médicale">
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
