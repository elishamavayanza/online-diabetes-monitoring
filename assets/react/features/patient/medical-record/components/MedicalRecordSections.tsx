import { Card } from '@/react/components/UI/Card';
import { Badge } from '@/react/components/UI/Badge';
import { MedicalRecordData } from '../types';

interface MedicalRecordSectionsProps {
    data: MedicalRecordData;
}

export function MedicalRecordSections({ data }: MedicalRecordSectionsProps) {
    return (
        <div className="medical-record-sections">
            <Card>
                <h3>Informations personnelles</h3>
                <p><strong>Nom :</strong> {data.personalInfo.nom}</p>
                <p><strong>Date de naissance :</strong> {data.personalInfo.dateNaissance}</p>
                <p><strong>Email :</strong> {data.personalInfo.email}</p>
                <p><strong>Téléphone :</strong> {data.personalInfo.telephone}</p>
            </Card>

            <Card>
                <h3>Diabète</h3>
                <p><strong>Type :</strong> {data.diabetesInfo.type}</p>
                <p><strong>Date du diagnostic :</strong> {data.diabetesInfo.dateDiagnostic}</p>
            </Card>

            <Card>
                <h3>Allergies</h3>
                <ul>
                    {data.allergies.map((allergy, idx) => <li key={idx}>{allergy}</li>)}
                </ul>
            </Card>

            <Card>
                <h3>Contacts d'urgence</h3>
                {data.emergencyContacts.map((contact, idx) => (
                    <div key={idx}>
                        <strong>{contact.nom}</strong> ({contact.relation}) — {contact.telephone}
                    </div>
                ))}
            </Card>

            <Card>
                <h3>Diagnostics</h3>
                {data.diagnostics.map((diag) => (
                    <div key={diag.id}>
                        {diag.nom} — {diag.date}
                    </div>
                ))}
            </Card>

            <Card>
                <h3>Notes médicales</h3>
                <ul>
                    {data.notes.map((note, idx) => <li key={idx}>{note}</li>)}
                </ul>
            </Card>

            <Card>
                <h3>Consentements</h3>
                {data.consentements.map((consent) => (
                    <div key={consent.id}>
                        {consent.type} : <Badge variant={consent.statut === 'Accepté' ? 'success' : 'error'}>{consent.statut}</Badge>
                    </div>
                ))}
            </Card>
        </div>
    );
}
