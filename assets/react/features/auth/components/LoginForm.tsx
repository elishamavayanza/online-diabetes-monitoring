import React from 'react';
import { useLoginForm } from '../hooks/useLoginForm';
import { Form } from "@/react/components/Forms/Form";
import {FormField} from "@/react/components/Forms/FormField";
import {Input} from "@/react/components/Forms/Input";
import {Password} from "@/react/components/Forms/Password";
import {ErrorMessage} from "@/react/components/Forms/ErrorMessage";
import {Button} from "@/react/components/UI/Button";
import { Checkbox } from "@/react/components/Forms/Checkbox";

export function LoginForm() {
    const {
        values,
        errors,
        isSubmitting,
        submitError,
        handleChange,
        handleBooleanChange,
        handleSubmit,
    } = useLoginForm();

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
