import React from 'react';
import { useLoginForm } from '../hooks/useLoginForm';
import { Form } from "@/react/components/Forms/Form";
import {FormField} from "@/react/components/Forms/FormField";
import {Input} from "@/react/components/Forms/Input";
import {Password} from "@/react/components/Forms/Password";
import {ErrorMessage} from "@/react/components/Forms/ErrorMessage";
import {Button} from "@/react/components/UI/Button";
import { Checkbox } from "@/react/components/Forms/Checkbox";
import { Alert } from '@/react/components/UI/Alert';

export function LoginForm() {
    const {
        values,
        errors,
        isSubmitting,
        submitError,
        block,
        resetBlock,
        handleChange,
        handleBooleanChange,
        handleSubmit,
    } = useLoginForm();

    if (block) {
        const formattedEnd = block.endsAt ? new Date(block.endsAt).toLocaleString('fr-FR') : undefined;
        return (
            <div className="login-block">
                <div className="login-block__icon" aria-hidden="true" />
                <h2 className="login-block__title">{block.title}</h2>
                <p className="login-block__message">{block.message}</p>
                <Alert variant="warning">
                    <div className="login-block__details">
                        {block.reason && (
                            <p><strong>Motif :</strong> {block.reason}</p>
                        )}
                        {formattedEnd && (
                            <p><strong>Fin de suspension :</strong> {formattedEnd}</p>
                        )}
                        <p>
                            Pour toute question, contactez le support :{' '}
                            <a href="mailto:support@onlinediab.com">support@onlinediab.com</a>.
                        </p>
                    </div>
                </Alert>
                <Button type="button" variant="outline" fullWidth onClick={resetBlock}>
                    Retour à la connexion
                </Button>
            </div>
        );
    }

    return (
        <Form layout="vertical" gap="medium" fullWidth onSubmit={handleSubmit} noValidate>
            <FormField
                label="Email ou nom d'utilisateur"
                htmlFor="emailOrUsername"
                required
                error={errors.emailOrUsername}
            >
                <Input
                    id="emailOrUsername"
                    name="emailOrUsername"
                    type="text"
                    placeholder="exemple@domaine.com"
                    fullWidth
                    value={values.emailOrUsername}
                    onChange={handleChange('emailOrUsername')}
                    variant={errors.emailOrUsername ? 'error' : 'default'}
                />
            </FormField>

            <FormField
                label="Mot de passe"
                htmlFor="password"
                required
                error={errors.password}
            >
                <Password
                    id="password"
                    name="password"
                    placeholder="••••••••"
                    fullWidth
                    value={values.password}
                    onChange={handleChange('password')}
                    variant={errors.password ? 'error' : 'default'}
                />
            </FormField>

            <div className="login-form__options-row">
                <Checkbox
                    label="Se souvenir de moi"
                    checked={values.rememberMe}
                    onChange={handleBooleanChange('rememberMe')}
                    name="rememberMe"
                    id="rememberMe"
                />
                <a href="/forgot-password" className="login-form__forgot-link">
                    Mot de passe oublié ?
                </a>
            </div>

            {submitError && <ErrorMessage variant="error">{submitError}</ErrorMessage>}

            <Button
                type="submit"
                variant="primary"
                fullWidth
                isLoading={isSubmitting}
            >
                {isSubmitting ? 'Connexion en cours...' : 'Se connecter'}
            </Button>
        </Form>
    );
}
