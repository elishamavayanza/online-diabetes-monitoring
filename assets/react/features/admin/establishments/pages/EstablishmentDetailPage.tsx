import React, { useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import { useEstablishmentDetail } from '../hooks/useEstablishmentDetail';
import {fetchMembersByEntity, Member, MemberRole} from '../services/membersService';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Card } from '@/react/components/UI/Card';
import { Avatar } from '@/react/components/UI/Avatar';
import { SearchInput } from '@/react/components/Forms/SearchInput';
import { Button } from '@/react/components/UI/Button';
import { Badge } from '@/react/components/UI/Badge';
import { Modal } from '@/react/components/UI/Modal';
import { useI18n } from '@/react/i18n/I18nContext';
import '@/styles/pages/admin/establishments/_establishment-detail-page.scss';


function getRoleBadge(role: MemberRole) {
    switch (role) {
        case 'CLINICIAN':
            return { label: 'Clinicien', variant: 'primary' as const };
        case 'NUTRITIONIST':
            return { label: 'Nutritionniste', variant: 'success' as const };
        case 'PATIENT':
            return { label: 'Patient', variant: 'warning' as const };
        default:
            return { label: role, variant: 'secondary' as const };
    }
}
export function EstablishmentDetailPage() {
    const { id } = useParams<{ id: string }>();
    const { node, isLoading, error } = useEstablishmentDetail(id || '');
    const { t } = useI18n();

    const [members, setMembers] = useState<Member[]>([]);
    const [isLoadingMembers, setIsLoadingMembers] = useState(false);
    const [membersError, setMembersError] = useState<string | null>(null);
    const [search, setSearch] = useState('');
    const [isAffectModalOpen, setIsAffectModalOpen] = useState(false);

    useEffect(() => {
        if (!node) return;

        const loadMembers = async () => {
            setIsLoadingMembers(true);
            setMembersError(null);
            try {
                const data = await fetchMembersByEntity(
                    node.data?.type || 'establishment',
                    node.id
                );
                setMembers(data);
            } catch (err) {
                setMembersError(t('Impossible de charger les membres.'));
            } finally {
                setIsLoadingMembers(false);
            }
        };

        loadMembers();
    }, [node]);

    if (isLoading) return <Spinner />;
    if (error) return <Alert variant="error">{error}</Alert>;
    if (!node) return <Alert variant="warning">{t('Élément introuvable')}</Alert>;

    const isEstablishment = node.data?.type === 'establishment';

    // Filtrer les membres selon la recherche
    const filteredMembers = members.filter((member) => {
        const q = search.toLowerCase();
        return (
            member.nom.toLowerCase().includes(q) ||
            member.email.toLowerCase().includes(q)
        );
    });

    return (
        <div className="establishment-detail-page">
            <div className="establishment-detail-page__header">
                <h1>{t('Membres de {{ name }}', { name: node.label })}</h1>
                <p>{isEstablishment ? t('Établissement') : t('Département')}</p>
            </div>

            {/* Barre de recherche + bouton affecter */}
            <div className="establishment-detail-page__actions">
                <div className="establishment-detail-page__search">
                    <SearchInput
                        placeholder={t('Rechercher un membre...')}
                        value={search}
                        onSearch={(value: string) => setSearch(value)}
                    />
                </div>
                <Button
                    variant="primary"
                    onClick={() => setIsAffectModalOpen(true)}
                    className="establishment-detail-page__affect-btn"
                >
                    {t('Affecter un nouveau')}
                </Button>
            </div>

            {isLoadingMembers ? (
                <Spinner />
            ) : membersError ? (
                <Alert variant="error">{membersError}</Alert>
            ) : (
                <div className="members-grid">
                    {filteredMembers.map((member) => {
                        const roleBadge = getRoleBadge(member.role);
                        return (
                            <Card key={member.id} className="member-card">
                                <div className="member-card__layout">
                                    <Avatar
                                        src={member.avatarUrl}
                                        name={member.nom}
                                        size="large"
                                        shape="circle"
                                    />
                                    <div className="member-card__info">
                                        <div className="member-card__header">
                                            <h3>{member.nom}</h3>
                                            <Badge variant={roleBadge.variant}>
                                                {t(roleBadge.label)}
                                            </Badge>
                                        </div>
                                        <p className="member-card__email">{member.email}</p>
                                        <p className="member-card__date">
                                            {t('Date de naissance :')} {member.dateNaissance}
                                        </p>
                                    </div>
                                </div>
                            </Card>
                        );
                    })}
                </div>
            )}

            {/* Modale factice pour l'affectation */}
            <Modal
                isOpen={isAffectModalOpen}
                onClose={() => setIsAffectModalOpen(false)}
                size="medium"
            >
                <p>{t("Formulaire d'affectation à implémenter.")}</p>
            </Modal>
        </div>
    );
}
