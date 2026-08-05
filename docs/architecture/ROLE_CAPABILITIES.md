# DiabCare - Rôles & Capacités

> Version: 1.0
> Module: Identité / RBAC
> Purpose: Décrire les responsabilités et les permissions de chaque rôle dans l'écosystème DiabCare.

---

# Rôles Existants

Le système possède actuellement les rôles suivants :

- SUPER_ADMIN
- ORGANIZATION_ADMIN
- CLINICIAN
- NUTRITIONIST
- PATIENT

---

# 1. SUPER_ADMIN

Le Super Administrateur possède les privilèges les plus élevés.

Il supervise toute la plateforme DiabCare.

## Gestion des Organisations

- Créer une Organisation de Santé
- Mettre à jour une Organisation de Santé
- Désactiver une Organisation de Santé
- Afficher Toutes les Organisations

### Types d'organisations pris en charge

- Hôpital
- Clinique
- Réseau de Santé

---

## Gestion des Utilisateurs

- Créer un Administrateur d'Organisation
- Suspendre un Utilisateur
- Activer un Utilisateur
- Réinitialiser le Mot de Passe
- Vérifier un Compte Utilisateur

---

## Gestion des Rôles & Permissions

- Créer un Rôle
- Mettre à jour un Rôle
- Supprimer un Rôle
- Assigner un Rôle
- Révoquer un Rôle

- Créer une Permission
- Mettre à jour une Permission
- Supprimer une Permission

---

## Supervision de la Plateforme

- Afficher les Journaux d'Audit
- Afficher les Notifications
- Afficher les Statistiques
- Surveiller les Organisations
- Surveiller les Utilisateurs
- Surveiller les Événements de Sécurité

---

# 2. ORGANIZATION_ADMIN

L'Administrateur d'Organisation gère uniquement son organisation.

Exemple :

- Clinique Espoir
- Hôpital Général

Il ne peut jamais modifier une autre organisation.

---

## Gestion des Organisations

- Mettre à jour le Profil de l'Organisation
- Créer un Établissement
- Mettre à jour un Établissement
- Créer un Département
- Mettre à jour un Département

---

## Gestion du Personnel

- Ajouter un Professionnel de Santé
- Supprimer un Professionnel de Santé
- Transférer un Professionnel de Santé
- Assigner un Département
- Gérer les Adhésions

---

## Gestion des Patients

- Afficher les Patients de l'Organisation
- Transférer un Patient
- Archiver un Patient
- Activer un Patient

---

## Gestion des Rendez-vous

- Afficher les Rendez-vous
- Replanifier un Rendez-vous
- Annuler un Rendez-vous

---

## Rapports

- Afficher les Statistiques de l'Organisation
- Afficher les Rapports d'Activité

---

# 3. CLINICIAN

Le Clinicien (Médecin) soigne les patients.

Il n'administre pas l'organisation.

---

## Gestion des Patients

- Afficher les Patients assignés
- Afficher le Dossier Médical
- Afficher le Profil Diabétique
- Afficher les Allergies
- Afficher les Consentements

---

## Médical

- Créer un Diagnostic
- Mettre à jour un Diagnostic
- Ajouter une Note Médicale

- Enregistrer la Glycémie
- Enregistrer la Pression Artérielle
- Enregistrer l'HbA1c
- Enregistrer le Poids
- Enregistrer l'Activité Physique
- Télécharger un Résultat de Laboratoire

---

## Traitement

- Créer une Prescription
- Mettre à jour une Prescription
- Annuler une Prescription
- Valider une Prescription

---

## Rendez-vous

- Créer un Rendez-vous
- Replanifier un Rendez-vous
- Annuler un Rendez-vous

---

## Communication

- Envoyer un Message
- Recevoir un Message
- Télécharger une Pièce Jointe

---

# 4. NUTRITIONIST

Le Nutritionniste gère uniquement la partie nutritionnelle.

---

## Catalogue Alimentaire

- Créer une Catégorie d'Aliments
- Mettre à jour une Catégorie d'Aliments

- Créer un Aliment
- Mettre à jour un Aliment
- Archiver un Aliment

---

## Gestion des Repas

- Créer un Plan de Repas
- Créer un Repas
- Ajouter un Élément au Repas
- Calculer les Calories
- Calculer les Glucides

---

## Suivi des Patients

- Afficher les Repas
- Afficher la Glycémie
- Donner des Conseils Nutritionnels

---

## Communication

- Envoyer un Message
- Télécharger un Document Nutritionnel

---

# 5. PATIENT

Le Patient est l'utilisateur principal.

---

## Profil

- Mettre à jour le Profil
- Mettre à jour l'Adresse
- Gérer les Contacts d'Urgence

---

## Suivi de Santé

- Enregistrer la Glycémie
- Enregistrer le Poids
- Enregistrer la Pression Artérielle
- Enregistrer l'Activité Physique

---

## Nutrition

- Enregistrer un Repas
- Afficher les Calories
- Afficher les Glucides

---

## Traitement

- Afficher les Prescriptions
- Enregistrer la Prise de Médicament
- Sauter un Médicament
- Afficher l'Historique des Médicaments

---

## Rendez-vous

- Afficher les Rendez-vous
- Confirmer un Rendez-vous
- Demander une Replanification

---

## Communication

- Envoyer un Message
- Lire les Messages
- Télécharger les Pièces Jointes

---

## Notification

- Afficher les Notifications
- Marquer la Notification comme Lue

---

# Matrice des Capacités

| Capacité | SUPER_ADMIN | ORGANIZATION_ADMIN | CLINICIAN | NUTRITIONIST | PATIENT |
|----------|-------------|--------------------|-----------|--------------|---------|
| Gérer les Organisations | Oui | Propre Organisation | Non | Non | Non |
| Gérer les Utilisateurs | Oui | Utilisateurs de l'Organisation | Non | Non | Non |
| Gérer les Rôles | Oui | Limité | Non | Non | Non |
| Afficher les Dossiers Médicaux | Oui | Selon Permissions | Oui | Lecture Seule | Propres |
| Créer un Diagnostic | Non | Non | Oui | Non | Non |
| Créer une Prescription | Non | Non | Oui | Non | Non |
| Gérer les Repas | Non | Non | Lecture | Oui | Propres Repas |
| Enregistrer la Glycémie | Non | Non | Oui | Lecture | Oui |
| Gérer les Rendez-vous | Supervision | Oui | Oui | Afficher | Propres |
| Envoyer des Messages | Oui | Oui | Oui | Oui | Oui |
| Afficher les Journaux d'Audit | Oui | Limité | Non | Non | Non |

---

Le système repose sur un RBAC (Role-Based Access Control).

Chaque utilisateur reçoit un ou plusieurs rôles.

Chaque rôle possède plusieurs permissions.

Des permissions spécifiques peuvent également être accordées directement à un utilisateur via UserPermission.

Cette architecture permet :

- Multi-tenant
- Multi-Organisation
- Multi-Rôle
- Substitution de Permissions (Permission Override)
- Autorisation Fine (Fine-grained Authorization)
- Extensibilité Future
