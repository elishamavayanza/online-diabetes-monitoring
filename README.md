# DiabCare API

## Description

**DiabCare** est une plateforme numérique conçue pour faciliter le suivi et la prise en charge des personnes vivant avec le diabète. Elle permet aux patients et aux professionnels de santé de collaborer à travers un système centralisé de gestion des données médicales, des prescriptions, des paramètres de santé et de la communication.

Ce dépôt contient le **backend** de l'application, développé avec **Symfony** et exposant une API REST sécurisée destinée à être consommée par une application web, mobile ou tout autre client compatible.

---

## Objectifs

L'objectif principal de DiabCare est de contribuer à une meilleure prise en charge des patients diabétiques en offrant une plateforme permettant de :

* gérer les comptes utilisateurs ;
* suivre les paramètres médicaux des patients ;
* gérer les prescriptions médicales ;
* faciliter la communication entre patients et professionnels de santé ;
* centraliser les informations des structures de santé ;
* offrir une API moderne, sécurisée et facilement extensible.

---

## Fonctionnalités

### Authentification

* Inscription
* Connexion
* Authentification JWT
* Gestion des rôles
* Réinitialisation du mot de passe
* Validation des comptes

### Gestion des utilisateurs

* Patients
* Cliniciens
* Nutritionnistes
* Administrateurs
* Super administrateurs

### Structures de santé

* Gestion des établissements de santé
* Gestion des accès des utilisateurs
* Administration des structures

### Suivi médical

* Glycémie
* Poids
* Tension artérielle
* HbA1c
* Insuline
* Comprimés
* Activité physique
* Mouvements
* Examens de laboratoire
* Historique des paramètres

### Prescriptions

* Création de prescriptions
* Gestion des traitements
* Types d'insuline
* Types de comprimés

### Communication

* Conversations
* Messages
* Accusés de réception

### Gestion des événements

* Rappels
* Événements médicaux
* Calendrier de suivi

---

## Technologies

### Backend

* PHP 8+
* Symfony
* Doctrine ORM
* PostgreSQL
* JWT Authentication
* API REST
* Composer

### Documentation

* NelmioApiDocBundle
* OpenAPI (Swagger)

---

## Architecture

Le projet adopte une architecture orientée services afin de garantir une bonne séparation des responsabilités.

```text
src/
├── Controller/
├── DTO/
├── Entity/
├── Enum/
├── Event/
├── Exception/
├── Mapper/
├── Repository/
├── Security/
├── Service/
├── Trait/
├── Utils/
└── Validator/
```

---

## Installation

### Cloner le projet

```bash
git clone <repository-url>
cd diabcare-api
```

### Installer les dépendances

```bash
composer install
```

### Configurer les variables d'environnement

Créer ou modifier le fichier `.env.local` :

```env
DATABASE_URL=
APP_SECRET=
MAILER_DSN=
```

### Créer la base de données

```bash
php bin/console doctrine:database:create
```

### Exécuter les migrations

```bash
php bin/console doctrine:migrations:migrate
```

### Lancer le serveur

```bash
symfony server:start
```

ou

```bash
php -S 127.0.0.1:8000 -t public
```

---

## Documentation de l'API

Une fois l'application démarrée, la documentation Swagger est disponible à l'adresse :

```
/api/doc
```

---

## Sécurité

L'API met en œuvre :

* authentification JWT ;
* contrôle des rôles et permissions ;
* validation des données ;
* protection des mots de passe avec les algorithmes recommandés par Symfony.

---

## Frontend

L'interface utilisateur est développée séparément avec **React** et **TypeScript**, qui consomment les endpoints de cette API.

---

## Licence

Ce projet est destiné à un usage académique et éducatif. Toute réutilisation doit respecter les conditions définies par ses auteurs.
