import React from 'react';
import { Form } from '@/react/components/Forms/Form';
import { FormField } from '@/react/components/Forms/FormField';
import { Input } from '@/react/components/Forms/Input';
import { ErrorMessage } from '@/react/components/Forms/ErrorMessage';
import { Button } from '@/react/components/UI/Button';
import { useForgotPasswordForm } from '../hooks/useForgotPasswordForm';

export function ForgotPasswordForm() {
    const {
        values,
        errors,
        isSubmitting,
        submitError,
        successMessage,
        handleChange,
        handleSubmit,
    } = useForgotPasswordForm();

    return (
        <Form layout="vertical" gap="medium" fullWidth onSubmit={handleSubmit} noValidate>
            <FormField
                label="Adresse email"
                htmlFor="email"
                required
                error={errors.email}
            >
                <Input
                    id="email"
                    name="email"
                    type="email"
                    placeholder="exemple@domaine.com"
                    fullWidth
                    value={values.email}
                    onChange={handleChange('email')}
                    variant={errors.email ? 'error' : 'default'}
                />
            </FormField>

            {successMessage && (
                <div className="forgot-password__success">
                    {successMessage}
                </div>
            )}

            {submitError && <ErrorMessage variant="error">{submitError}</ErrorMessage>}

            <Button
                type="submit"
                variant="primary"
                fullWidth
                isLoading={isSubmitting}
            >
                {isSubmitting ? 'Envoi en cours...' : 'Envoyer le lien'}
            </Button>
        </Form>
    );
}
