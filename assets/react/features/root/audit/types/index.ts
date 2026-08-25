export interface AuditLog {
    id: string;
    utilisateur: string;
    action: string;
    ressource: string;
    date: string;
    adresseIp: string;
    resultat: 'SUCCESS' | 'FAILURE';
}

export interface DataAccessLog {
    id: string;
    utilisateur: string;
    patient: string;
    ressourceConsultee: string;
    motif: string;
    date: string;
}
