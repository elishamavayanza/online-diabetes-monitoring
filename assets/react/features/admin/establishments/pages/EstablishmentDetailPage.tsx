// features/admin/establishments/pages/EstablishmentDetailPage.tsx
import React from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { useEstablishmentDetail } from '../hooks/useEstablishmentDetail';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Badge } from '@/react/components/UI/Badge';
import { Card } from '@/react/components/UI/Card';

export function EstablishmentDetailPage() {
    const { id } = useParams<{ id: string }>();
    const navigate = useNavigate();
    const { node, isLoading, error } = useEstablishmentDetail(id || '');

    if (isLoading) return <Spinner />;
    if (error) return <Alert variant="error">{error}</Alert>;
    if (!node) return <Alert variant="warning">Élément introuvable</Alert>;

    const data = node.data;
    const isEstablishment = data?.type === 'establishment';
    const isDepartment = data?.type === 'department';

    return (
        <div className="establishment-detail-page">
            <div className="establishment-detail-page__header">
                <h1>{node.label}</h1>
                <p>{isEstablishment ? 'Établissement' : 'Département'}</p>
            </div>

            {isEstablishment && data.establishment && (
                <div className="establishment-detail-page__sections">
                    <Card className="detail-card">
                        <h2>Informations</h2>
                        <p><strong>Adresse :</strong> {data.establishment.adresse}</p>
                        <p><strong>Téléphone :</strong> {data.establishment.telephone}</p>
                        <p>
                            <strong>Statut :</strong>{' '}
                            <Badge variant={data.establishment.statut === 'Active' ? 'success' : 'error'}>
                                {data.establishment.statut}
                            </Badge>
                        </p>
                        <p><strong>Départements :</strong> {data.establishment.departementsCount}</p>
                        <p><strong>Personnel total :</strong> {data.establishment.personnelCount ?? '—'}</p>
                        <p><strong>Patients suivis :</strong> {data.establishment.patientCount ?? '—'}</p>
                    </Card>
                    <Card className="detail-card">
                        <h2>Départements</h2>
                        {node.children && node.children.length > 0 ? (
                            <ul>
                                {node.children.map(child => (
                                    <li key={child.id}>{child.label}</li>
                                ))}
                            </ul>
                        ) : (
                            <p>Aucun département</p>
                        )}
                    </Card>
                </div>
            )}

            {isDepartment && data.department && (
                <div className="establishment-detail-page__sections">
                    <Card className="detail-card">
                        <h2>Informations</h2>
                        <p><strong>Établissement :</strong> {data.department.etablissement}</p>
                        <p><strong>Spécialité :</strong> {data.department.specialite || '—'}</p>
                        <p>
                            <strong>Statut :</strong>{' '}
                            <Badge variant={data.department.statut === 'Active' ? 'success' : 'error'}>
                                {data.department.statut}
                            </Badge>
                        </p>
                        <p><strong>Personnel :</strong> {data.department.personnel}</p>
                        <p><strong>Patients :</strong> {data.department.patients ?? '—'}</p>
                    </Card>
                    <Card className="detail-card">
                        <h2>Personnel</h2>
                        <p>Liste du personnel à implémenter</p>
                    </Card>
                </div>
            )}
        </div>
    );
}
