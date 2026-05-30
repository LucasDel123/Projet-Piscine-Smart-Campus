DROP DATABASE IF EXISTS smartcampus;
CREATE DATABASE smartcampus CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE smartcampus;

CREATE TABLE administrateur (
    id_admin INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mdp VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE enseignant (
    id_enseignant INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mdp VARCHAR(255) NOT NULL,
    departement VARCHAR(50) NOT NULL,
    grade VARCHAR(50)
) ENGINE=InnoDB;

CREATE TABLE etudiant (
    id_etudiant INT AUTO_INCREMENT PRIMARY KEY,
    num_etudiant VARCHAR(20) NOT NULL UNIQUE,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mdp VARCHAR(255) NOT NULL,
    filiere VARCHAR(50) NOT NULL,
    niveau VARCHAR(10) NOT NULL,
    date_inscription DATE NOT NULL
) ENGINE=InnoDB;

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
        REFERENCES enseignant(id_enseignant) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE inscription (
    id_inscription INT AUTO_INCREMENT PRIMARY KEY,
    date_inscription DATE NOT NULL,
    id_etudiant INT NOT NULL,
    id_cours INT NOT NULL,
    CONSTRAINT uk_etudiant_cours UNIQUE (id_etudiant, id_cours),
    CONSTRAINT fk_inscription_etudiant FOREIGN KEY (id_etudiant)
        REFERENCES etudiant(id_etudiant) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_inscription_cours FOREIGN KEY (id_cours)
        REFERENCES cours(id_cours) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE note (
    id_note INT AUTO_INCREMENT PRIMARY KEY,
    valeur FLOAT NOT NULL,
    type_evaluation VARCHAR(50) NOT NULL,
    coefficient FLOAT NOT NULL DEFAULT 1.0,
    date_saisie DATE NOT NULL,
    validee BOOLEAN NOT NULL DEFAULT FALSE,
    id_inscription INT NOT NULL,
    CONSTRAINT fk_note_inscription FOREIGN KEY (id_inscription)
        REFERENCES inscription(id_inscription) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

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

CREATE TABLE notification (
    id_notif INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(100) NOT NULL,
    contenu TEXT NOT NULL,
    type_notif VARCHAR(50) NOT NULL,
    date_envoi DATETIME NOT NULL,
    statut_lu BOOLEAN NOT NULL DEFAULT FALSE,
    id_destinataire INT NOT NULL,
    role_destinataire VARCHAR(20) NOT NULL
) ENGINE=InnoDB;

CREATE INDEX idx_etudiant_email ON etudiant(email);
CREATE INDEX idx_enseignant_email ON enseignant(email);
CREATE INDEX idx_admin_email ON administrateur(email);
CREATE INDEX idx_inscription_etudiant ON inscription(id_etudiant);
CREATE INDEX idx_inscription_cours ON inscription(id_cours);
CREATE INDEX idx_seance_date ON seance(date_seance);
CREATE INDEX idx_notification_destinataire ON notification(id_destinataire, role_destinataire);
