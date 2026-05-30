DROP DATABASE IF EXISTS smartcampus;
CREATE DATABASE smartcampus CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE smartcampus;

-- 1. Table ADMINISTRATEUR
CREATE TABLE administrateur (
    id_admin INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mdp VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

-- 2. Table ENSEIGNANT
CREATE TABLE enseignant (
    id_enseignant INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mdp VARCHAR(255) NOT NULL,
    departement VARCHAR(50) NOT NULL,
    grade VARCHAR(50)
) ENGINE=InnoDB;

-- 3. Table ÉTUDIANT
CREATE TABLE etudiant (
    id_etudiant INT AUTO_INCREMENT PRIMARY KEY,
    num_etudiant VARCHAR(20) NOT NULL UNIQUE,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mdp VARCHAR(255) NOT NULL,
    groupe INT NOT NULL, -- numéro du TD
    niveau VARCHAR(10) NOT NULL, -- ING1, ING2 etc
    date_inscription DATE NOT NULL
) ENGINE=InnoDB;

-- 4. Table COURS
CREATE TABLE cours (
    id_cours INT AUTO_INCREMENT PRIMARY KEY,
    code_cours VARCHAR(10) NOT NULL UNIQUE,
    intitule VARCHAR(100) NOT NULL,
    credits INT NOT NULL DEFAULT 0,
    coefficient FLOAT NOT NULL DEFAULT 1.0,
    semestre VARCHAR(5) NOT NULL,
    capacite_max INT NOT NULL,
    id_enseignant INT,
    CONSTRAINT fk_cours_enseignant FOREIGN KEY (id_enseignant) 
        REFERENCES enseignant(id_enseignant) ON DELETE SET NULL ON UPDATE CASCADE -- exemple pour ON DELETE SET NULL : si un prof quitte l'école on mets NULL au cours de ce dernier; exemple ON UPDATE CASCADE : si on change l'id d'un prof il faut le mettre à jour ici aussi
) ENGINE=InnoDB;

-- 5. Table INSCRIPTION (Table de liaison entre Étudiant et Cours)
CREATE TABLE inscription (
    id_inscription INT AUTO_INCREMENT PRIMARY KEY,
    date_inscription DATE NOT NULL,
    id_etudiant INT NOT NULL,
    id_cours INT NOT NULL,
    CONSTRAINT uk_etudiant_cours UNIQUE (id_etudiant, id_cours), -- afin d'éviter les doubles inscriptions à un cours
    CONSTRAINT fk_inscription_etudiant FOREIGN KEY (id_etudiant) 
        REFERENCES etudiant(id_etudiant) ON DELETE CASCADE ON UPDATE CASCADE, 
    CONSTRAINT fk_inscription_cours FOREIGN KEY (id_cours) 
        REFERENCES cours(id_cours) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 6. Table NOTE
CREATE TABLE note (
    id_note INT AUTO_INCREMENT PRIMARY KEY,
    valeur FLOAT NOT NULL,                   -- La note (ex: 15.5)
    type_evaluation VARCHAR(50) NOT NULL,    -- 'CC', 'DS', 'Projet'
    coefficient FLOAT NOT NULL DEFAULT 1.0,  -- Pour les calculs de moyennes
    date_saisie DATE NOT NULL,
    validee BOOLEAN NOT NULL DEFAULT FALSE,  -- Verrouillage de la note
    id_etudiant INT NOT NULL,
    id_cours INT NOT NULL,
    
    -- Clés étrangères directes (sans passer par l'inscription)
    CONSTRAINT fk_note_etudiant FOREIGN KEY (id_etudiant) REFERENCES etudiant(id_etudiant) ON DELETE CASCADE,
    CONSTRAINT fk_note_cours FOREIGN KEY (id_cours) REFERENCES cours(id_cours) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 7. Table SÉANCE (Pour l'emploi du temps)
CREATE TABLE seance (
    id_seance INT AUTO_INCREMENT PRIMARY KEY,
    date_seance DATE NOT NULL,
    heure_debut TIME NOT NULL,
    heure_fin TIME NOT NULL,
    salle VARCHAR(50) NOT NULL,
    id_cours INT NOT NULL,
    CONSTRAINT fk_seance_cours FOREIGN KEY (id_cours) 
        REFERENCES cours(id_cours) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 8. Table NOTIFICATION
CREATE TABLE notification (
    id_notif INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(100) NOT NULL,
    contenu TEXT NOT NULL,
    type_notif VARCHAR(50) NOT NULL, -- 'Note', 'EDT', 'Inscription'
    date_envoi DATETIME NOT NULL,
    statut_lu BOOLEAN NOT NULL DEFAULT FALSE,
    id_destinataire INT NOT NULL, -- ID de l'étudiant, prof ou admin selon le rôle indiqué dans role_destinataire
    role_destinataire VARCHAR(20) NOT NULL -- 'etudiant', 'enseignant', 'admin'
) ENGINE=InnoDB;

-- CREATION DES INDEX STRATÉGIQUES (Optimisation des requêtes fréquentes)
CREATE INDEX idx_etudiant_email ON etudiant(email);
CREATE INDEX idx_enseignant_email ON enseignant(email);
CREATE INDEX idx_inscription_etudiant ON inscription(id_etudiant);
CREATE INDEX idx_inscription_cours ON inscription(id_cours);
CREATE INDEX idx_seance_date ON seance(date_seance);
CREATE INDEX idx_notification_destinataire ON notification(id_destinataire, role_destinataire);
-- EXPLICATION DES INDEX STRATÉGIQUES :
-- Par défaut, pour chercher un utilisateur par son email (connexion) ou charger les notifications d'un étudiant (id_destinataire), MySQL doit parcourir l'intégralité des tables ligne par ligne.
-- Ces commandes créent des "index" (comme l'index à la fin d'un livre). MySQL va pré-trier ces colonnes spécifiques pour pouvoir retrouverinstantanément les données lors des requêtes fréquentes (Ex: chargement de l'emploi du temps par date ou vérification de l'email au login).
-- Cela évite de ralentir l'application client-serveur.
