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

interface AffectationModalProps {
    isOpen: boolean;
    onClose: () => void;
    mode: 'create' | 'edit';
    affectationData?: AffectationData;
    userId: string;
}

export function AffectationModal({ isOpen, onClose, mode, affectationData, userId }: AffectationModalProps) {
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
            setError('Erreur lors de l’enregistrement.');
        } finally {
            setIsSubmitting(false);
        }
    };

    return (
        <Modal isOpen={isOpen} onClose={onClose} size="medium">
            <div className="affectation-modal">
                <h2>{mode === 'create' ? 'Affecter l’utilisateur' : 'Modifier l’affectation'}</h2>
                {error && <Alert variant="error">{error}</Alert>}
                <Form onSubmit={handleSubmit}>
                    <FormField label="Organisation *">
                        <Select
                            value={form.organizationId}
                            onChange={(e) => updateField('organizationId', e.target.value)}
                            options={organisations.map((org) => ({ value: org.id, label: org.nom }))}
                            placeholder="Choisir une organisation"
                            required
                        />
                    </FormField>
                    <FormField label="Établissement">
                        <Select
                            value={form.facilityId ?? ''}
                            onChange={(e) => updateField('facilityId', e.target.value)}
                            options={facilities.map((fac) => ({ value: fac.id, label: fac.nom }))}
                            placeholder="Choisir un établissement"
                        />
                    </FormField>
                    <FormField label="Département">
                        <Select
                            value={form.departmentId ?? ''}
                            onChange={(e) => updateField('departmentId', e.target.value)}
                            options={departments.map((dep) => ({ value: dep.id, label: dep.nom }))}
                            placeholder="Choisir un département"
                        />
                    </FormField>
                    <FormField label="Date de début *">
                        <Input
                            type="date"
                            value={form.startDate}
                            onChange={(e) => updateField('startDate', e.target.value)}
                            required
                        />
                    </FormField>
                    <FormField label="Date de fin">
                        <Input
                            type="date"
                            value={form.endDate ?? ''}
                            onChange={(e) => updateField('endDate', e.target.value)}
                        />
                    </FormField>
                    <FormField label="Statut">
                        <Select
                            value={form.status}
                            onChange={(e) => updateField('status', e.target.value)}
                            options={[
                                { value: 'ACTIVE', label: 'Actif' },
                                { value: 'SUSPENDED', label: 'Suspendu' },
                                { value: 'ENDED', label: 'Terminé' },
                            ]}
                        />
                    </FormField>
                    <div className="affectation-modal__actions">
                        <Button type="button" variant="outline" onClick={onClose}>Annuler</Button>
                        <Button type="submit" disabled={isSubmitting}>
                            {mode === 'create' ? 'Affecter' : 'Enregistrer'}
                        </Button>
                    </div>
                </Form>
            </div>
        </Modal>
    );
}
