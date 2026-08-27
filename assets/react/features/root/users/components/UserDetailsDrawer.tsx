import React from 'react';
import { Drawer } from '@/react/components/UI/Drawer';
import { Avatar } from '@/react/components/UI/Avatar';
import { Badge } from '@/react/components/UI/Badge';
import { Button } from '@/react/components/UI/Button';
import { User } from '../types';

interface UserDetailsDrawerProps {
    user: User | null;
    isOpen: boolean;
    onClose: () => void;
    onAffect: (user: User) => void;
    onModifyAffectation: (user: User) => void;
    onModify: (user: User) => void;
    onSuspend: (user: User) => void;
}

export function UserDetailsDrawer({
                                      user,
                                      isOpen,
                                      onClose,
                                      onAffect,
                                      onModifyAffectation,
                                      onModify,
                                      onSuspend,
                                  }: UserDetailsDrawerProps) {
    if (!user) return null;

    return (
        <Drawer
            isOpen={isOpen}
            onClose={onClose}
            position="right"
            size="medium"
            className="user-details-drawer"
        >
            <div className="user-details">
                <div className="user-details__header">
                    <Avatar
                        src={user.avatarUrl}
                        name={user.nom}
                        size="large"
                        shape="circle"
                    />
                    <h2>{user.nom}</h2>
                    <p>{user.email}</p>
                </div>

                <div className="user-details__body">
                    <p><strong>Type :</strong> {user.type}</p>
                    <p>
                        <strong>Organisation :</strong>{' '}
                        {user.organisation || 'Non affecté'}
                    </p>
                    <p>
                        <strong>Statut :</strong>{' '}
                        <Badge variant={user.statut === 'Active' ? 'success' : 'warning'}>
                            {user.statut}
                        </Badge>
                    </p>
                    <p><strong>Dernière connexion :</strong> {user.derniereConnexion}</p>
                </div>

                <div className="user-details__actions">
                    {user.organisation ? (
                        <Button variant="primary" onClick={() => onModifyAffectation(user)}>
                            Modifier l’affectation
                        </Button>
                    ) : (
                        <Button variant="primary" onClick={() => onAffect(user)}>
                            Affecter
                        </Button>
                    )}
                    <Button variant="outline" onClick={() => onModify(user)}>
                        Modifier
                    </Button>
                    <Button variant="danger" onClick={() => onSuspend(user)}>
                        Suspendre
                    </Button>
                </div>
            </div>
        </Drawer>
    );
}
