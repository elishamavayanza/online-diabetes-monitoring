import React from 'react';
import { Drawer } from '@/react/components/UI/Drawer';
import { Avatar } from '@/react/components/UI/Avatar';
import { Badge } from '@/react/components/UI/Badge';
import { Button } from '@/react/components/UI/Button';
import { EstablishmentTreeNodeData } from '../hooks/useEstablishments';
import { TreeTableNode } from "@/react/hook-components/Data/TreeTable/types";
import { useI18n } from '@/react/i18n/I18nContext';

interface EstablishmentDetailsDrawerProps {
    node: TreeTableNode<EstablishmentTreeNodeData> | null;
    isOpen: boolean;
    onClose: () => void;
    onModify?: (node: TreeTableNode<EstablishmentTreeNodeData>) => void;
}

export function EstablishmentDetailsDrawer({
                                               node,
                                               isOpen,
                                               onClose,
                                               onModify,
                                           }: EstablishmentDetailsDrawerProps) {
    const { t } = useI18n();

    if (!node) return null;

    const data = node.data;
    const isEstablishment = data?.type === 'establishment';
    const isDepartment = data?.type === 'department';

    return (
        <Drawer
            isOpen={isOpen}
            onClose={onClose}
            position="right"
            size="medium"
            className="establishment-details-drawer"
        >
            <div className="details">
                <div className="details__header">
                    <Avatar
                        src={undefined}
                        name={node.label}
                        size="large"
                        shape="circle"
                    />
                    <h2>{node.label}</h2>
                </div>

                {isEstablishment && data.establishment && (
                    <div className="details__body">
                        <p><strong>{t('Type :')}</strong> {t('Établissement')}</p>
                        <p><strong>{t('Adresse :')}</strong> {data.establishment.adresse}</p>
                        <p><strong>{t('Téléphone :')}</strong> {data.establishment.telephone}</p>
                        <p>
                            <strong>{t('Statut :')}</strong>{' '}
                            <Badge variant={data.establishment.statut === 'Active' ? 'success' : 'error'}>
                                {data.establishment.statut}
                            </Badge>
                        </p>
                        <p>
                            <strong>{t('Départements :')}</strong> {data.establishment.departementsCount}
                        </p>
                        <p>
                            <strong>{t('Personnel total :')}</strong> {data.establishment.personnelCount ?? '—'}
                        </p>
                        <p>
                            <strong>{t('Patients suivis :')}</strong> {data.establishment.patientCount ?? '—'}
                        </p>
                    </div>
                )}

                {isDepartment && data.department && (
                    <div className="details__body">
                        <p><strong>{t('Type :')}</strong> {t('Département')}</p>
                        <p><strong>{t('Établissement :')}</strong> {data.department.etablissement}</p>
                        <p><strong>{t('Spécialité :')}</strong> {data.department.specialite || '—'}</p>
                        <p>
                            <strong>{t('Statut :')}</strong>{' '}
                            <Badge variant={data.department.statut === 'Active' ? 'success' : 'error'}>
                                {data.department.statut}
                            </Badge>
                        </p>
                        <p>
                            <strong>{t('Personnel :')}</strong> {data.department.personnel}
                        </p>
                        <p>
                            <strong>{t('Patients :')}</strong> {data.department.patients ?? '—'}
                        </p>
                    </div>
                )}

                <div className="details__actions">
                    <Button variant="primary" onClick={() => onModify?.(node)}>
                        {t('Modifier')}
                    </Button>
                </div>
            </div>
        </Drawer>
    );
}
