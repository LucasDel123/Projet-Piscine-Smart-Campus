USE smartcampus;

SET FOREIGN_KEY_CHECKS = 0;

DELETE FROM note;
DELETE FROM seance;
DELETE FROM inscription;
DELETE FROM cours;
DELETE FROM etudiant;
DELETE FROM enseignant;
DELETE FROM administrateur;
DELETE FROM notification;

ALTER TABLE note AUTO_INCREMENT = 1;
ALTER TABLE seance AUTO_INCREMENT = 1;
ALTER TABLE inscription AUTO_INCREMENT = 1;
ALTER TABLE cours AUTO_INCREMENT = 1;
ALTER TABLE etudiant AUTO_INCREMENT = 1;
ALTER TABLE enseignant AUTO_INCREMENT = 1;
ALTER TABLE administrateur AUTO_INCREMENT = 1;
ALTER TABLE notification AUTO_INCREMENT = 1;

SET FOREIGN_KEY_CHECKS = 1;

INSERT INTO administrateur (nom, prenom, email, mdp) VALUES
('Delliste', 'Lucas', 'lucas.delliste@edu.ece.fr', '$2b$10$s5PPPb54sprdVqNfVsnkT.QQ8Dwpp/pCJUpZLIZObPX2/53umdkfm'),
('Chevalier', 'Audran', 'audran.chevalier@edu.ece.fr', '$2b$10$s5PPPb54sprdVqNfVsnkT.QQ8Dwpp/pCJUpZLIZObPX2/53umdkfm');

INSERT INTO enseignant (nom, prenom, email, mdp, departement, grade) VALUES
('Dupont', 'Jean', 'jean.dupont@ece.fr', '$2b$10$s5PPPb54sprdVqNfVsnkT.QQ8Dwpp/pCJUpZLIZObPX2/53umdkfm', 'Informatique', 'Professeur Principal'),
('Martin', 'Sophie', 'sophie.martin@ece.fr', '$2b$10$s5PPPb54sprdVqNfVsnkT.QQ8Dwpp/pCJUpZLIZObPX2/53umdkfm', 'Informatique', 'Maître de conférences'),
('Durand', 'Pierre', 'pierre.durand@ece.fr', '$2b$10$s5PPPb54sprdVqNfVsnkT.QQ8Dwpp/pCJUpZLIZObPX2/53umdkfm', 'Mathématiques', 'Professeur émérite'),
('Lefebvre', 'Chantal', 'chantal.lefebvre@ece.fr', '$2b$10$s5PPPb54sprdVqNfVsnkT.QQ8Dwpp/pCJUpZLIZObPX2/53umdkfm', 'Électronique', 'Intervenant extérieur'),
('Moreau', 'Robert', 'robert.moreau@ece.fr', '$2b$10$s5PPPb54sprdVqNfVsnkT.QQ8Dwpp/pCJUpZLIZObPX2/53umdkfm', 'Sciences Humaines', 'Enseignant-Chercheur'),
('Girard', 'Nathalie', 'nathalie.girard@ece.fr', '$2b$10$s5PPPb54sprdVqNfVsnkT.QQ8Dwpp/pCJUpZLIZObPX2/53umdkfm', 'Informatique', 'Maître de conférences'),
('Roux', 'Philippe', 'philippe.roux@ece.fr', '$2b$10$s5PPPb54sprdVqNfVsnkT.QQ8Dwpp/pCJUpZLIZObPX2/53umdkfm', 'Réseaux & Télécoms', 'Professeur Principal'),
('Fontaine', 'Isabelle', 'isabelle.fontaine@ece.fr', '$2b$10$s5PPPb54sprdVqNfVsnkT.QQ8Dwpp/pCJUpZLIZObPX2/53umdkfm', 'Cybersécurité', 'Enseignant-Chercheur'),
('Mercier', 'Julien', 'julien.mercier@ece.fr', '$2b$10$s5PPPb54sprdVqNfVsnkT.QQ8Dwpp/pCJUpZLIZObPX2/53umdkfm', 'Mathématiques', 'Maître de conférences'),
('Dumas', 'Caroline', 'caroline.dumas@ece.fr', '$2b$10$s5PPPb54sprdVqNfVsnkT.QQ8Dwpp/pCJUpZLIZObPX2/53umdkfm', 'Sciences Humaines', 'Intervenant extérieur');

INSERT INTO cours (code_cours, intitule, credits, coefficient, semestre, capacite_max, id_enseignant) VALUES
('INF410', 'Projet Web dynamique', 6, 2.5, 'S6', 150, 1),
('INF201', 'Algorithmique avancée', 4, 1.5, 'S3', 200, 1),
('INF305', 'Bases de données relationnelles', 5, 2.0, 'S5', 150, 2),
('INF420', 'Intelligence artificielle', 6, 2.5, 'S7', 120, 2),
('MAT101', 'Analyse et Algèbre linéaire', 4, 2.0, 'S1', 250, 3),
('MAT302', 'Probabilités et Statistiques', 4, 1.5, 'S4', 200, 3),
('ELN204', 'Systèmes Microcontrôleurs', 5, 2.0, 'S4', 120, 4),
('ELN308', 'Traitement du signal', 4, 1.5, 'S5', 100, 4),
('SHS105', 'Management et Éthique de l ingénieur', 2, 1.0, 'S2', 300, 5),
('SHS402', 'Anglais Professionnel - C1', 3, 1.0, 'S6', 120, 5),
('INF210', 'Programmation orientée objet', 4, 2.0, 'S3', 200, 6),
('INF330', 'Développement mobile', 5, 2.0, 'S6', 120, 6),
('INF515', 'Machine Learning', 6, 2.5, 'S8', 100, 2),
('RES220', 'Réseaux et protocoles', 4, 1.5, 'S4', 150, 7),
('RES410', 'Architecture Cloud', 5, 2.0, 'S7', 120, 7),
('CYB301', 'Sécurité des systèmes', 5, 2.0, 'S5', 130, 8),
('CYB405', 'Cryptographie appliquée', 5, 2.5, 'S7', 100, 8),
('MAT203', 'Mathématiques discrètes', 4, 1.5, 'S3', 200, 9),
('PHY110', 'Physique générale', 4, 2.0, 'S2', 250, 9),
('SHS210', 'Communication professionnelle', 2, 1.0, 'S3', 300, 10);

INSERT INTO etudiant (num_etudiant, nom, prenom, email, mdp, niveau, groupe, date_inscription)
WITH RECURSIVE seq AS (
    SELECT 1 AS n
    UNION ALL
    SELECT n + 1 FROM seq WHERE n < 500
)
SELECT
    CONCAT('ECE2026', LPAD(x.n, 4, '0')),
    x.nom,
    x.prenom,
    CONCAT(LOWER(x.prenom), '.', LOWER(x.nom), x.n, '@edu.ece.fr'),
    '$2b$10$s5PPPb54sprdVqNfVsnkT.QQ8Dwpp/pCJUpZLIZObPX2/53umdkfm',
    ELT(FLOOR(((x.n - 1) % 18) / 6) + 1, 'ING1', 'ING2', 'ING3'),
    (((x.n - 1) % 18) % 6) + 1,
    DATE_ADD('2025-09-01', INTERVAL (x.n % 25) DAY)
FROM (
    SELECT
        n,
        ELT((n % 20) + 1, 'Martin','Bernard','Thomas','Petit','Robert','Richard','Durand','Dubois','Moreau','Laurent','Simon','Michel','Lefevre','Leroy','Roux','David','Bertrand','Morel','Fournier','Girard') AS nom,
        ELT((n % 17) + 1, 'Lucas','Emma','Hugo','Lea','Nathan','Chloe','Louis','Manon','Tom','Jade','Enzo','Alice','Paul','Lina','Adam','Eva','Noah') AS prenom
    FROM seq
) AS x;

INSERT INTO inscription (date_inscription, id_etudiant, id_cours)
WITH RECURSIVE s AS (
    SELECT 1 AS n
    UNION ALL
    SELECT n + 1 FROM s WHERE n < 30
)
SELECT '2026-02-10', n, ((n - 1) % 20) + 1 FROM s
UNION ALL
SELECT '2026-02-11', n, ((n + 5) % 20) + 1 FROM s
UNION ALL
SELECT '2026-02-12', n, ((n + 11) % 20) + 1 FROM s;

INSERT INTO note (valeur, type_evaluation, coefficient, date_saisie, validee, id_inscription)
WITH RECURSIVE i AS (
    SELECT 1 AS n
    UNION ALL
    SELECT n + 1 FROM i WHERE n < 60
)
SELECT ROUND(8 + ((n * 7) % 11) + (n % 2) * 0.5, 1), 'CC1', 0.4, '2026-04-15', TRUE,  n FROM i
UNION ALL
SELECT ROUND(9 + ((n * 5) % 10) + (n % 2) * 0.5, 1), 'DS',  0.6, '2026-05-20', FALSE, n FROM i;

INSERT INTO seance (date_seance, heure_debut, heure_fin, salle, id_cours) VALUES
('2026-06-01', '08:30:00', '10:15:00', 'Salle E204', 1),
('2026-06-01', '10:30:00', '12:15:00', 'Salle E204', 2),
('2026-06-01', '08:30:00', '10:15:00', 'Amphi Turing', 3),
('2026-06-01', '10:30:00', '12:15:00', 'Amphi Turing', 4),
('2026-06-02', '08:30:00', '10:15:00', 'Labo Réseau', 14),
('2026-06-02', '10:30:00', '12:15:00', 'Labo Réseau', 15),
('2026-06-02', '08:30:00', '10:15:00', 'Labo Crypto', 16),
('2026-06-02', '14:00:00', '15:45:00', 'Amphi Turing', 5),
('2026-06-03', '08:30:00', '10:15:00', 'Salle E102', 11),
('2026-06-03', '10:30:00', '12:15:00', 'Salle E102', 12),
('2026-06-04', '14:00:00', '15:45:00', 'Amphi Turing', 18),
('2026-06-05', '09:00:00', '10:45:00', 'Salle E305', 20);

INSERT INTO notification (titre, contenu, type_notif, date_envoi, statut_lu, id_destinataire, role_destinataire) VALUES
('Nouvelle note publiée', 'Votre note de CC en Projet Web est en ligne.', 'Note', '2026-04-15 10:00:00', FALSE, 1, 'etudiant'),
('Changement d emploi du temps', 'Le cours d IA de mardi est déplacé en Amphi Turing.', 'EDT', '2026-05-28 14:30:00', FALSE, 1, 'etudiant'),
('Rappel saisie', 'Pensez à valider définitivement les notes de l examen final S6.', 'Inscription', '2026-05-29 09:00:00', FALSE, 1, 'enseignant'),
('Nouvelle inscription', 'Un nouvel étudiant s est inscrit au cours d Algorithmique avancée.', 'Inscription', '2026-02-15 16:00:00', TRUE, 1, 'admin'),
('Alerte système', 'Sauvegarde de la base de données MySQL réussie.', 'Inscription', '2026-05-29 00:00:00', TRUE, 2, 'admin'),
('Capacité atteinte', 'Le cours de Machine Learning approche de sa capacité maximale.', 'Inscription', '2026-05-30 11:00:00', FALSE, 2, 'admin');
