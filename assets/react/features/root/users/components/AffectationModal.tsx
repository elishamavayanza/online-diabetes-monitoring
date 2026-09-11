import React, { useEffect, useState } from 'react';
import { Modal } from '@/react/components/UI/Modal';
import { Form } from '@/react/components/Forms/Form';
import { FormField } from '@/react/components/Forms/FormField';
import { Select } from '@/react/components/Forms/Select';
import { Input } from '@/react/components/Forms/Input';
import { Button } from '@/react/components/UI/Button';
import { Alert } from '@/react/components/UI/Alert';
import {
    fetchOrganisationsOptions,
    fetchFacilitiesByOrg,
    fetchDepartmentsByFacility,
    createAffectation,
    updateAffectation,
} from '../services/affectationService';
import { OrganisationOption, FacilityOption, DepartmentOption, AffectationData } from '../types/affectation';
import { useI18n } from '@/react/i18n/I18nContext';

interface AffectationModalProps {
    isOpen: boolean;
    onClose: () => void;
    mode: 'create' | 'edit';
    affectationData?: AffectationData;
    userId: string;
}

export function AffectationModal({ isOpen, onClose, mode, affectationData, userId }: AffectationModalProps) {
    const { t } = useI18n();
    const [organisations, setOrganisations] = useState<OrganisationOption[]>([]);
    const [facilities, setFacilities] = useState<FacilityOption[]>([]);
    const [departments, setDepartments] = useState<DepartmentOption[]>([]);
    const [form, setForm] = useState<AffectationData>(
        affectationData || {
            userId,
            organizationId: '',
            facilityId: '',
            departmentId: '',
            startDate: '',
            endDate: '',
            status: 'ACTIVE',
        }
    );
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const loadOrganisations = async () => {
            const orgs = await fetchOrganisationsOptions();
            setOrganisations(orgs);
        };
        if (isOpen) loadOrganisations();
    }, [isOpen]);

    useEffect(() => {
        const organizationId = form.organizationId;
        if (organizationId) {
            const loadFacilities = async () => {
                const facs = await fetchFacilitiesByOrg(organizationId);
                setFacilities(facs);
                setDepartments([]); // reset
            };
            loadFacilities();
        } else {
            setFacilities([]);
            setDepartments([]);
        }
    }, [form.organizationId]);

    useEffect(() => {
        const facilityId = form.facilityId;   // capture locale
        if (facilityId) {
            const loadDepts = async () => {
                const depts = await fetchDepartmentsByFacility(facilityId); //  type string
                setDepartments(depts);
            };
            loadDepts();
        } else {
            setDepartments([]);
        }
    }, [form.facilityId]);

    const updateField = (field: keyof AffectationData, value: any) => {
        setForm((prev) => ({ ...prev, [field]: value }));
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setIsSubmitting(true);
        setError(null);
        try {
            if (mode === 'create') {
                await createAffectation(form);
            } else if (affectationData?.affectationId) {
                await updateAffectation(affectationData.affectationId, form);
            }
            onClose();
        } catch (err) {
            setError(t('Erreur lors de l\u2019enregistrement.'));
        } finally {
            setIsSubmitting(false);
        }
    };

    return (
        <Modal isOpen={isOpen} onClose={onClose} size="medium">
            <div className="affectation-modal">
                <h2>{mode === 'create' ? t('Affecter l\u2019utilisateur') : t('Modifier l\u2019affectation')}</h2>
                {error && <Alert variant="error">{error}</Alert>}
                <Form onSubmit={handleSubmit}>
                    <FormField label={t('Organisation *')}>
                        <Select
                            value={form.organizationId}
                            onChange={(e) => updateField('organizationId', e.target.value)}
                            options={organisations.map((org) => ({ value: org.id, label: org.nom }))}
                            placeholder={t('Choisir une organisation')}
                            required
                        />
                    </FormField>
                    <FormField label={t('Établissement')}>
                        <Select
                            value={form.facilityId ?? ''}
                            onChange={(e) => updateField('facilityId', e.target.value)}
                            options={facilities.map((fac) => ({ value: fac.id, label: fac.nom }))}
                            placeholder={t('Choisir un établissement')}
                        />
                    </FormField>
                    <FormField label={t('Département')}>
                        <Select
                            value={form.departmentId ?? ''}
                            onChange={(e) => updateField('departmentId', e.target.value)}
                            options={departments.map((dep) => ({ value: dep.id, label: dep.nom }))}
                            placeholder={t('Choisir un département')}
                        />
                    </FormField>
                    <FormField label={t('Date de début *')}>
                        <Input
                            type="date"
                            value={form.startDate}
                            onChange={(e) => updateField('startDate', e.target.value)}
                            required
                        />
                    </FormField>
                    <FormField label={t('Date de fin')}>
                        <Input
                            type="date"
                            value={form.endDate ?? ''}
                            onChange={(e) => updateField('endDate', e.target.value)}
                        />
                    </FormField>
                    <FormField label={t('Statut')}>
                        <Select
                            value={form.status}
                            onChange={(e) => updateField('status', e.target.value)}
                            options={[
                                { value: 'ACTIVE', label: t('Actif') },
                                { value: 'SUSPENDED', label: t('Suspendu') },
                                { value: 'ENDED', label: t('Terminé') },
                            ]}
                        />
                    </FormField>
                    <div className="affectation-modal__actions">
                        <Button type="button" variant="outline" onClick={onClose}>{t('Annuler')}</Button>
                        <Button type="submit" disabled={isSubmitting}>
                            {mode === 'create' ? t('Affecter') : t('Enregistrer')}
                        </Button>
                    </div>
                </Form>
            </div>
        </Modal>
    );
}
