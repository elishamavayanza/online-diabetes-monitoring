import React from 'react';
import { Drawer } from '@/react/components/UI/Drawer'; // ou votre chemin exact
import { Avatar } from '@/react/components/UI/Avatar';
import { TreeNode } from '@/react/hook-components/Data/Tree/types';
import { CreateOrganisationPayload } from '../types';
import { Establishment } from '../types/establishment';
import { Department } from '../types/department';
import { useIsMobile } from '@/react/hooks/useIsMobile';
import { useI18n } from '@/react/i18n/I18nContext';

interface NodeDetailsPanelProps {
    isOpen: boolean;
    onClose: () => void;
    node: TreeNode | null;
}

export function NodeDetailsPanel({ isOpen, onClose, node }: NodeDetailsPanelProps) {
    const isMobile = useIsMobile();
    const { t } = useI18n();
    if (!node) return null;

    const data = node.data as Record<string, any> | undefined;
    const dataType = data?.dataType;

    // Rendu des informations selon le type
    let details: React.ReactNode = null;

    if (dataType === 'organisation') {
        const org = data as unknown as CreateOrganisationPayload;
        details = (
            <>
                <p><strong>{t('Nom court :')}</strong> {org.shortName || '—'}</p>
                <p><strong>{t('Type :')}</strong> {org.type}</p>
                <p><strong>{t('Email :')}</strong> {org.email || '—'}</p>
                <p><strong>{t('Téléphone :')}</strong> {org.phone || '—'}</p>
                <p><strong>{t('Site Web :')}</strong> {org.website || '—'}</p>
                <p><strong>{t('Statut :')}</strong> {org.active ? t('Actif') : t('Inactif')}</p>
            </>
        );
    } else if (dataType === 'establishment') {
        const est = data as unknown as Establishment;
        details = (
            <>
                <p><strong>{t('Organisation :')}</strong> {est.organizationId}</p>
                <p><strong>{t('Téléphone :')}</strong> {est.phone || '—'}</p>
                <p><strong>{t('Adresse :')}</strong> {est.address ? `${est.address.street}, ${est.address.city}` : '—'}</p>
            </>
        );
    } else if (dataType === 'department') {
        const dep = data as unknown as Department;
        details = (
            <>
                <p><strong>{t('Établissement :')}</strong> {dep.facilityId}</p>
                <p><strong>{t('Spécialité :')}</strong> {dep.specialty || '—'}</p>
            </>
        );
    } else {
        details = <p>{t('Aucune information supplémentaire.')}</p>;
    }

    return (
        <Drawer isOpen={isOpen} onClose={onClose} position="right"  size={isMobile ? 'full' : 'medium'} className="node-details-drawer" >
            <div className="node-details">
                <div className="node-details__header">
                    <Avatar
                        src={undefined} // ou une URL si disponible
                        name={node.label}
                        size="large"
                        shape="circle"
                        icon={node.icon}
                    />
                    <h2>{node.label}</h2>
                </div>
                <div className="node-details__body">
                    {details}
                </div>
            </div>
        </Drawer>
    );
}
