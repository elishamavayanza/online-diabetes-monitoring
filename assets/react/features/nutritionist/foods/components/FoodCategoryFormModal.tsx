import { useI18n } from '@/react/i18n/I18nContext';
import { Modal } from '@/react/components/UI/Modal';
import { Form } from '@/react/components/Forms/Form';
import { FormField } from '@/react/components/Forms/FormField';
import { Input } from '@/react/components/Forms/Input';
import { Textarea } from '@/react/components/Forms/Textarea';
import { Button } from '@/react/components/UI/Button';
import { Alert } from '@/react/components/UI/Alert';
import { useCreateFoodCategory } from '../hooks/useCreateFoodCategory';
import { FoodCategory } from '../types';

interface FoodCategoryFormModalProps {
    isOpen: boolean;
    onClose: () => void;
    onSuccess: (category: FoodCategory) => void;
}

export function FoodCategoryFormModal({ isOpen, onClose, onSuccess }: FoodCategoryFormModalProps) {
    const { t } = useI18n();
    const { label, description, setLabel, setDescription, isSubmitting, error, submit } = useCreateFoodCategory();

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        const category = await submit();
        if (category) {
            onSuccess(category);
            onClose();
        }
    };

    return (
        <Modal isOpen={isOpen} onClose={onClose} title={t('Nouvelle catégorie')}>
            <div className="food-form-modal">
                {error && <Alert variant="error">{error}</Alert>}
                <Form onSubmit={handleSubmit}>
                    <FormField label={t('Libellé *')}>
                        <Input
                            value={label}
                            onChange={(e) => setLabel(e.target.value)}
                            placeholder={t('Ex: Fruits secs')}
                            required
                        />
                    </FormField>
                    <FormField label={t('Description')}>
                        <Textarea
                            value={description}
                            onChange={(e) => setDescription(e.target.value)}
                            placeholder={t('Description optionnelle...')}
                        />
                    </FormField>
                    <div className="food-form-modal__actions">
                        <Button type="button" variant="outline" onClick={onClose}>{t('Annuler')}</Button>
                        <Button type="submit" disabled={isSubmitting}>
                            {isSubmitting ? t('Création...') : t('Créer')}
                        </Button>
                    </div>
                </Form>
            </div>
        </Modal>
    );
}
