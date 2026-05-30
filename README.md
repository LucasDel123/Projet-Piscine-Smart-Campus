# SmartCampus

Application web de gestion académique (étudiants, enseignants, cours, inscriptions et notes), développée dans le cadre du projet « Projet Web dynamique » à l'ECE.

## Stack technique

- **Backend** : PHP 8 avec PDO (requêtes préparées), API REST renvoyant du JSON
- **Base de données** : MySQL
- **Frontend** : React 18 (via CDN) en Single Page Application, transpilé par Babel standalone
- **Serveur local** : WAMP

## Arborescence

```
smartcampus/
├── backend/
│   ├── api/
│   │   ├── auth/          Authentification (login, logout, me)
│   │   ├── etudiants/     CRUD étudiants
│   │   ├── enseignants/   CRUD enseignants
│   │   ├── cours/         CRUD cours + contrôle de capacité
│   │   ├── inscriptions/  Inscriptions (anti-doublon + capacité)
│   │   └── notes/         Notes (saisie, validation, verrouillage)
│   ├── classes/           Database (PDO singleton), Auth
│   ├── config/            Configuration de la base de données
│   └── utils/             Utilitaire de réponse JSON
├── database/              Schéma et données de test (SQL)
├── frontend/              Interface React (SPA)
└── docs/
```

## Installation

1. Cloner le dépôt dans le répertoire web de WAMP (`C:\wamp64\www\`).
2. Importer la base de données dans phpMyAdmin :
   - `database/schema.sql` puis `database/seed.sql`
3. Vérifier les identifiants MySQL dans `backend/config/config_database.php`.
4. Ouvrir `http://localhost/smartcampus/frontend/` dans le navigateur.

## Compte de démonstration

- **Email** : `lucas.delliste@edu.ece.fr` (administrateur)
- **Mot de passe** : `mateolpb`

## Règles métier principales

- Un cours ne peut pas dépasser sa capacité maximale.
- Un étudiant ne peut pas s'inscrire deux fois au même cours.
- Une note validée est verrouillée : elle ne peut plus être modifiée ni supprimée.
