export type ProfessionalType = 'Clinician' | 'Nutritionist';

export interface Professional {
    id: string;
    nom: string;
    type: ProfessionalType;
    specialite: string;
    etablissement: string;
    departement: string;
    statut: 'Active' | 'Inactive';
}
