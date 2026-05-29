USE smartcampus;

-- Désactivation des contraintes pour vider proprement les tables
SET FOREIGN_KEY_CHECKS = 0;


DELETE FROM note;
DELETE FROM seance;
DELETE FROM inscription;
DELETE FROM cours;
DELETE FROM etudiant;
DELETE FROM enseignant;
DELETE FROM administrateur;
DELETE FROM notification;

-- Ici on remet tous les compteurs d'auto-incrémentation à 1 pour éviter que les id de la nouvelle base suivent la suite du dernier id en mémoire
ALTER TABLE note AUTO_INCREMENT = 1;
ALTER TABLE seance AUTO_INCREMENT = 1;
ALTER TABLE inscription AUTO_INCREMENT = 1;
ALTER TABLE cours AUTO_INCREMENT = 1;
ALTER TABLE etudiant AUTO_INCREMENT = 1;
ALTER TABLE enseignant AUTO_INCREMENT = 1;
ALTER TABLE administrateur AUTO_INCREMENT = 1;
ALTER TABLE notification AUTO_INCREMENT = 1;

SET FOREIGN_KEY_CHECKS = 1;
-- 1. INSERTION DES ADMINISTRATEURS (2)

INSERT INTO administrateur (id_admin, nom, prenom, email, mdp) VALUES
(1, 'Delliste', 'Lucas', 'lucas.delliste@edu.ece.fr', '$2y$10$7R9vK3xG8uO.eJ9vA6Yy8U9Z21S6W7xX8Y9Z01K2L3M4N5O6P7Q6'), -- le mdp est le HASH du mdp : mateolpb
(2, 'Chevalier', 'Audran', 'audran.chevalier@edu.ece.fr', '$2y$10$7R9vK3xG8uO.eJ9vA6Yy8U9Z21S6W7xX8Y9Z01K2L3M4N5O6P7Q6');

-- 2. INSERTION DES ENSEIGNANTS (5)

INSERT INTO enseignant (id_enseignant, nom, prenom, email, mdp, departement, grade) VALUES
(1, 'Dupont', 'Jean', 'jean.dupont@ece.fr', '$2y$10$7R9vK3xG8uO.eJ9vA6Yy8U9Z21S6W7xX8Y9Z01K2L3M4N5O6P7Q6', 'Informatique', 'Professeur Principal'), -- le mdp est le HASH du mdp : mateolpb
(2, 'Martin', 'Sophie', 'sophie.martin@ece.fr', '$2y$10$7R9vK3xG8uO.eJ9vA6Yy8U9Z21S6W7xX8Y9Z01K2L3M4N5O6P7Q6', 'Informatique', 'Maître de conférences'),
(3, 'Durand', 'Pierre', 'pierre.durand@ece.fr', '$2y$10$7R9vK3xG8uO.eJ9vA6Yy8U9Z21S6W7xX8Y9Z01K2L3M4N5O6P7Q6', 'Mathématiques', 'Professeur émérite'),
(4, 'Lefebvre', 'Chantal', 'chantal.lefebvre@ece.fr', '$2y$10$7R9vK3xG8uO.eJ9vA6Yy8U9Z21S6W7xX8Y9Z01K2L3M4N5O6P7Q6', 'Électronique', 'Intervenant extérieur'),
(5, 'Moreau', 'Robert', 'robert.moreau@ece.fr', '$2y$10$7R9vK3xG8uO.eJ9vA6Yy8U9Z21S6W7xX8Y9Z01K2L3M4N5O6P7Q6', 'Sciences Humaines', 'Enseignant-Chercheur');

-- 3. INSERTION DES ÉTUDIANTS (20)

INSERT INTO etudiant (id_etudiant, num_etudiant, nom, prenom, email, mdp, filiere, niveau, date_inscription) VALUES
(1, 'ECE202601', 'Cambon', 'Mateo', 'mateo.cambon@edu.ece.fr', '$2y$10$7R9vK3xG8uO.eJ9vA6Yy8U9Z21S6W7xX8Y9Z01K2L3M4N5O6P7Q6', 'Informatique', 'ING2', '2025-09-01'), -- le mdp est le HASH du mdp : mateolpb
(2, 'ECE202602', 'Le Nilias', 'Mathieu', 'mathieu.lenilias@edu.ece.fr', '$2y$10$7R9vK3xG8uO.eJ9vA6Yy8U9Z21S6W7xX8Y9Z01K2L3M4N5O6P7Q6', 'Informatique', 'ING2', '2025-09-01'),
(3, 'ECE202603', 'Bernard', 'Julien', 'julien.bernard@edu.ece.fr', '$2y$10$7R9vK3xG8uO.eJ9vA6Yy8U9Z21S6W7xX8Y9Z01K2L3M4N5O6P7Q6', 'Informatique', 'ING2', '2025-09-02'),
(4, 'ECE202604', 'Thomas', 'Alexandre', 'alexandre.thomas@edu.ece.fr', '$2y$10$7R9vK3xG8uO.eJ9vA6Yy8U9Z21S6W7xX8Y9Z01K2L3M4N5O6P7Q6', 'Systèmes Embarqués', 'ING2', '2025-09-02'),
(5, 'ECE202605', 'Petit', 'Marine', 'marine.petit@edu.ece.fr', '$2y$10$7R9vK3xG8uO.eJ9vA6Yy8U9Z21S6W7xX8Y9Z01K2L3M4N5O6P7Q6', 'Systèmes Embarqués', 'ING2', '2025-09-03'),
(6, 'ECE202606', 'Robert', 'Nicolas', 'nicolas.robert@edu.ece.fr', '$2y$10$7R9vK3xG8uO.eJ9vA6Yy8U9Z21S6W7xX8Y9Z01K2L3M4N5O6P7Q6', 'Informatique', 'ING1', '2025-09-03'),
(7, 'ECE202607', 'Richard', 'Sarah', 'sarah.richard@edu.ece.fr', '$2y$10$7R9vK3xG8uO.eJ9vA6Yy8U9Z21S6W7xX8Y9Z01K2L3M4N5O6P7Q6', 'Informatique', 'ING1', '2025-09-04'),
(8, 'ECE202608', 'Dubois', 'Thomas', 'thomas.dubois@edu.ece.fr', '$2y$10$7R9vK3xG8uO.eJ9vA6Yy8U9Z21S6W7xX8Y9Z01K2L3M4N5O6P7Q6', 'Généraliste', 'ING1', '2025-09-04'),
(9, 'ECE202609', 'Guerin', 'Camille', 'camille.guerin@edu.ece.fr', '$2y$10$7R9vK3xG8uO.eJ9vA6Yy8U9Z21S6W7xX8Y9Z01K2L3M4N5O6P7Q6', 'Généraliste', 'ING3', '2025-09-05'),
(10, 'ECE202610', 'Laurent', 'Maxime', 'maxime.laurent@edu.ece.fr', '$2y$10$7R9vK3xG8uO.eJ9vA6Yy8U9Z21S6W7xX8Y9Z01K2L3M4N5O6P7Q6', 'Informatique', 'ING3', '2025-09-05'),
(11, 'ECE202611', 'Simon', 'Chloé', 'chloe.simon@edu.ece.fr', '$2y$10$7R9vK3xG8uO.eJ9vA6Yy8U9Z21S6W7xX8Y9Z01K2L3M4N5O6P7Q6', 'Informatique', 'ING2', '2025-09-05'),
(12, 'ECE202612', 'Michel', 'Antoine', 'antoine.michel@edu.ece.fr', '$2y$10$7R9vK3xG8uO.eJ9vA6Yy8U9Z21S6W7xX8Y9Z01K2L3M4N5O6P7Q6', 'Systèmes Embarqués', 'ING2', '2025-09-06'),
(13, 'ECE202613', 'Garcia', 'Emma', 'emma.garcia@edu.ece.fr', '$2y$10$7R9vK3xG8uO.eJ9vA6Yy8U9Z21S6W7xX8Y9Z01K2L3M4N5O6P7Q6', 'Informatique', 'ING2', '2025-09-06'),
(14, 'ECE202614', 'Martinez', 'Lucas', 'lucas.martinez@edu.ece.fr', '$2y$10$7R9vK3xG8uO.eJ9vA6Yy8U9Z21S6W7xX8Y9Z01K2L3M4N5O6P7Q6', 'Informatique', 'ING1', '2025-09-07'),
(15, 'ECE202615', 'Lopez', 'Manon', 'manon.lopez@edu.ece.fr', '$2y$10$7R9vK3xG8uO.eJ9vA6Yy8U9Z21S6W7xX8Y9Z01K2L3M4N5O6P7Q6', 'Généraliste', 'ING1', '2025-09-07'),
(16, 'ECE202616', 'Gonzalez', 'Hugo', 'hugo.gonzalez@edu.ece.fr', '$2y$10$7R9vK3xG8uO.eJ9vA6Yy8U9Z21S6W7xX8Y9Z01K2L3M4N5O6P7Q6', 'Informatique', 'ING2', '2025-09-08'),
(17, 'ECE202617', 'Garnier', 'Clara', 'clara.garnier@edu.ece.fr', '$2y$10$7R9vK3xG8uO.eJ9vA6Yy8U9Z21S6W7xX8Y9Z01K2L3M4N5O6P7Q6', 'Informatique', 'ING2', '2025-09-08'),
(18, 'ECE202618', 'Faure', 'Clément', 'clement.faure@edu.ece.fr', '$2y$10$7R9vK3xG8uO.eJ9vA6Yy8U9Z21S6W7xX8Y9Z01K2L3M4N5O6P7Q6', 'Systèmes Embarqués', 'ING3', '2025-09-09'),
(19, 'ECE202619', 'Rousseau', 'Laura', 'laura.rousseau@edu.ece.fr', '$2y$10$7R9vK3xG8uO.eJ9vA6Yy8U9Z21S6W7xX8Y9Z01K2L3M4N5O6P7Q6', 'Informatique', 'ING2', '2025-09-09'),
(20, 'ECE202620', 'Blanc', 'Arthur', 'arthur.blanc@edu.ece.fr', '$2y$10$7R9vK3xG8uO.eJ9vA6Yy8U9Z21S6W7xX8Y9Z01K2L3M4N5O6P7Q6', 'Informatique', 'ING2', '2025-09-10');

-- 4. INSERTION DES COURS (10)

INSERT INTO cours (id_cours, code_cours, intitule, credits, coefficient, semestre, capacite_max, id_enseignant) VALUES
(1, 'INF410', 'Projet Web dynamique', 6, 2.5, 'S6', 35, 1),
(2, 'INF201', 'Algorithmique avancée', 4, 1.5, 'S3', 45, 1),
(3, 'INF305', 'Bases de données relationnelles', 5, 2.0, 'S5', 40, 2),
(4, 'INF420', 'Intelligence artificielle', 6, 2.5, 'S7', 30, 2),
(5, 'MAT101', 'Analyse et Algèbre linéaire', 4, 2.0, 'S1', 50, 3),
(6, 'MAT302', 'Probabilités et Statistiques', 4, 1.5, 'S4', 50, 3),
(7, 'ELN204', 'Systèmes Microcontrôleurs', 5, 2.0, 'S4', 35, 4),
(8, 'ELN308', 'Traitement du signal', 4, 1.5, 'S5', 30, 4),
(9, 'SHS105', 'Management et Éthique de l\'ingénieur', 2, 1.0, 'S2', 60, 5), -- Le "\" sert à pas couper le texte à cause de '
(10, 'SHS402', 'Anglais Professionnel - C1', 3, 1.0, 'S6', 25, 5);

-- 5. INSERTION DES INSCRIPTIONS (50)

INSERT INTO inscription (id_inscription, date_inscription, id_etudiant, id_cours) VALUES
(1, '2026-02-10', 1, 1), (2, '2026-02-10', 1, 2), (3, '2026-02-10', 1, 3),
(4, '2026-02-10', 2, 1), (5, '2026-02-10', 2, 2), (6, '2026-02-10', 2, 4),
(7, '2026-02-11', 3, 1), (8, '2026-02-11', 3, 3), (9, '2026-02-11', 3, 5),
(10, '2026-02-11', 4, 2), (11, '2026-02-11', 4, 4), (12, '2026-02-11', 4, 6),
(13, '2026-02-12', 5, 2), (14, '2026-02-12', 5, 5), (15, '2026-02-12', 5, 7),
(16, '2026-02-12', 6, 3), (17, '2026-02-12', 6, 6), (18, '2026-02-12', 6, 8),
(19, '2026-02-13', 7, 3), (20, '2026-02-13', 7, 7), (21, '2026-02-13', 7, 9),
(22, '2026-02-13', 8, 4), (23, '2026-02-13', 8, 8), (24, '2026-02-13', 8, 10),
(25, '2026-02-14', 9, 4), (26, '2026-02-14', 9, 9), (27, '2026-02-14', 9, 1),
(28, '2026-02-14', 10, 5), (29, '2026-02-14', 10, 10), (30, '2026-02-14', 10, 2),
(31, '2026-02-15', 11, 1), (32, '2026-02-15', 11, 6),
(33, '2026-02-15', 12, 2), (34, '2026-02-15', 12, 7),
(35, '2026-02-16', 13, 3), (36, '2026-02-16', 13, 8),
(37, '2026-02-16', 14, 4), (38, '2026-02-16', 14, 9),
(39, '2026-02-17', 15, 5), (40, '2026-02-17', 15, 10),
(41, '2026-02-17', 16, 6), (42, '2026-02-17', 16, 1),
(43, '2026-02-18', 17, 7), (44, '2026-02-18', 17, 2),
(45, '2026-02-18', 18, 8), (46, '2026-02-18', 18, 3),
(47, '2026-02-19', 19, 9), (48, '2026-02-19', 19, 4),
(49, '2026-02-19', 20, 10), (50, '2026-02-19', 20, 5);

-- 6. INSERTION DES NOTES (100 notes avec 2 notes par id_inscription)

INSERT INTO note (valeur, type_evaluation, coefficient, date_saisie, validee, id_inscription) VALUES
(14.5, 'CC', 0.2, '2026-04-15', TRUE, 1), (15.0, 'DS', 0.4, '2026-05-20', FALSE, 1),
(12.0, 'CC', 0.2, '2026-04-15', TRUE, 2), (11.5, 'DS', 0.4, '2026-05-20', FALSE, 2),
(16.0, 'CC', 0.2, '2026-04-15', TRUE, 3), (14.0, 'DS', 0.4, '2026-05-20', FALSE, 3),
(13.5, 'CC', 0.2, '2026-04-15', TRUE, 4), (15.5, 'DS', 0.4, '2026-05-20', FALSE, 4),
(10.0, 'CC', 0.2, '2026-04-15', TRUE, 5), (12.5, 'DS', 0.4, '2026-05-20', FALSE, 5),
(17.0, 'CC', 0.2, '2026-04-15', TRUE, 6), (18.0, 'DS', 0.4, '2026-05-20', FALSE, 6),
(11.0, 'CC', 0.2, '2026-04-15', TRUE, 7), (13.0, 'DS', 0.4, '2026-05-20', FALSE, 7),
(15.0, 'CC', 0.2, '2026-04-15', TRUE, 8), (14.5, 'DS', 0.4, '2026-05-20', FALSE, 8),
(09.5, 'CC', 0.2, '2026-04-15', TRUE, 9), (11.0, 'DS', 0.4, '2026-05-20', FALSE, 9),
(14.0, 'CC', 0.2, '2026-04-15', TRUE, 10), (13.5, 'DS', 0.4, '2026-05-20', FALSE, 10),
(12.5, 'CC', 0.2, '2026-04-15', TRUE, 11), (14.0, 'DS', 0.4, '2026-05-20', FALSE, 11),
(16.5, 'CC', 0.2, '2026-04-15', TRUE, 12), (15.0, 'DS', 0.4, '2026-05-20', FALSE, 12),
(13.0, 'CC', 0.2, '2026-04-15', TRUE, 13), (12.0, 'DS', 0.4, '2026-05-20', FALSE, 13),
(14.0, 'CC', 0.2, '2026-04-15', TRUE, 14), (16.0, 'DS', 0.4, '2026-05-20', FALSE, 14),
(11.5, 'CC', 0.2, '2026-04-15', TRUE, 15), (10.5, 'DS', 0.4, '2026-05-20', FALSE, 15),
(15.0, 'CC', 0.2, '2026-04-15', TRUE, 16), (14.0, 'DS', 0.4, '2026-05-20', FALSE, 16),
(12.0, 'CC', 0.2, '2026-04-15', TRUE, 17), (13.5, 'DS', 0.4, '2026-05-20', FALSE, 17),
(10.0, 'CC', 0.2, '2026-04-15', TRUE, 18), (11.0, 'DS', 0.4, '2026-05-20', FALSE, 18),
(16.0, 'CC', 0.2, '2026-04-15', TRUE, 19), (17.5, 'DS', 0.4, '2026-05-20', FALSE, 19),
(14.0, 'CC', 0.2, '2026-04-15', TRUE, 20), (13.0, 'DS', 0.4, '2026-05-20', FALSE, 20),
(13.5, 'CC', 0.2, '2026-04-15', TRUE, 21), (14.0, 'DS', 0.4, '2026-05-20', FALSE, 21),
(12.0, 'CC', 0.2, '2026-04-15', TRUE, 22), (11.5, 'DS', 0.4, '2026-05-20', FALSE, 22),
(15.5, 'CC', 0.2, '2026-04-15', TRUE, 23), (16.0, 'DS', 0.4, '2026-05-20', FALSE, 23),
(14.0, 'CC', 0.2, '2026-04-15', TRUE, 24), (15.0, 'DS', 0.4, '2026-05-20', FALSE, 24),
(11.0, 'CC', 0.2, '2026-04-15', TRUE, 25), (12.5, 'DS', 0.4, '2026-05-20', FALSE, 25),
(16.5, 'CC', 0.2, '2026-04-15', TRUE, 26), (17.0, 'DS', 0.4, '2026-05-20', FALSE, 26),
(13.0, 'CC', 0.2, '2026-04-15', TRUE, 27), (14.0, 'DS', 0.4, '2026-05-20', FALSE, 27),
(12.0, 'CC', 0.2, '2026-04-15', TRUE, 28), (11.0, 'DS', 0.4, '2026-05-20', FALSE, 28),
(15.0, 'CC', 0.2, '2026-04-15', TRUE, 29), (16.0, 'DS', 0.4, '2026-05-20', FALSE, 29),
(14.0, 'CC', 0.2, '2026-04-15', TRUE, 30), (13.5, 'DS', 0.4, '2026-05-20', FALSE, 30),
(10.5, 'CC', 0.2, '2026-04-15', TRUE, 31), (12.0, 'DS', 0.4, '2026-05-20', FALSE, 31),
(13.0, 'CC', 0.2, '2026-04-15', TRUE, 32), (14.5, 'DS', 0.4, '2026-05-20', FALSE, 32),
(16.0, 'CC', 0.2, '2026-04-15', TRUE, 33), (15.0, 'DS', 0.4, '2026-05-20', FALSE, 33),
(14.0, 'CC', 0.2, '2026-04-15', TRUE, 34), (13.0, 'DS', 0.4, '2026-05-20', FALSE, 34),
(11.5, 'CC', 0.2, '2026-04-15', TRUE, 35), (12.5, 'DS', 0.4, '2026-05-20', FALSE, 35),
(15.0, 'CC', 0.2, '2026-04-15', TRUE, 36), (16.0, 'DS', 0.4, '2026-05-20', FALSE, 36),
(13.5, 'CC', 0.2, '2026-04-15', TRUE, 37), (14.0, 'DS', 0.4, '2026-05-20', FALSE, 37),
(12.0, 'CC', 0.2, '2026-04-15', TRUE, 38), (11.0, 'DS', 0.4, '2026-05-20', FALSE, 38),
(14.5, 'CC', 0.2, '2026-04-15', TRUE, 39), (15.5, 'DS', 0.4, '2026-05-20', FALSE, 39),
(16.0, 'CC', 0.2, '2026-04-15', TRUE, 40), (17.0, 'DS', 0.4, '2026-05-20', FALSE, 40),
(12.5, 'CC', 0.2, '2026-04-15', TRUE, 41), (13.0, 'DS', 0.4, '2026-05-20', FALSE, 41),
(11.0, 'CC', 0.2, '2026-04-15', TRUE, 42), (12.5, 'DS', 0.4, '2026-05-20', FALSE, 42),
(15.0, 'CC', 0.2, '2026-04-15', TRUE, 43), (14.0, 'DS', 0.4, '2026-05-20', FALSE, 43),
(13.5, 'CC', 0.2, '2026-04-15', TRUE, 44), (15.0, 'DS', 0.4, '2026-05-20', FALSE, 44),
(10.0, 'CC', 0.2, '2026-04-15', TRUE, 45), (11.5, 'DS', 0.4, '2026-05-20', FALSE, 45),
(16.5, 'CC', 0.2, '2026-04-15', TRUE, 46), (15.5, 'DS', 0.4, '2026-05-20', FALSE, 46),
(14.0, 'CC', 0.2, '2026-04-15', TRUE, 47), (13.0, 'DS', 0.4, '2026-05-20', FALSE, 47),
(12.0, 'CC', 0.2, '2026-04-15', TRUE, 48), (13.5, 'DS', 0.4, '2026-05-20', FALSE, 48),
(15.5, 'CC', 0.2, '2026-04-15', TRUE, 49), (16.0, 'DS', 0.4, '2026-05-20', FALSE, 49),
(13.0, 'CC', 0.2, '2026-04-15', TRUE, 50), (14.0, 'DS', 0.4, '2026-05-20', FALSE, 50);

-- 7. INSERTION DES SÉANCES (6 - Emploi du temps)
INSERT INTO seance (date_seance, heure_debut, heure_fin, salle, id_cours) VALUES
('2026-06-01', '08:30:00', '10:15:00', 'Salle E204', 1),
('2026-06-01', '10:30:00', '12:15:00', 'Salle E204', 2),
('2026-06-01', '14:00:00', '15:45:00', 'Amphi Turing', 3),
('2026-06-02', '08:30:00', '10:15:00', 'Labo Élec 1', 7),
('2026-06-02', '10:30:00', '12:15:00', 'Salle E102', 10),
('2026-06-03', '14:00:00', '15:45:00', 'Amphi Turing', 4);

-- 8. INSERTION DES NOTIFICATIONS D'EXEMPLE (5)

INSERT INTO notification (titre, contenu, type_notif, date_envoi, statut_lu, id_destinataire, role_destinataire) VALUES
('Nouvelle note publiée', 'Votre note de CC en Projet Web est en ligne.', 'Note', '2026-04-15 10:00:00', FALSE, 1, 'etudiant'),
('Changement d\'emploi du temps', 'Le cours d\'IA de mardi est déplacé en Amphi Turing.', 'EDT', '2026-05-28 14:30:00', FALSE, 1, 'etudiant'), -- statut_lu = FALSE l'utilisateur à pas lu la notif
('Rappel saisie', 'Pensez à valider définitivement les notes de l\'examen final S6.', 'Inscription', '2026-05-29 09:00:00', FALSE, 1, 'enseignant'), -- Le "\" sert à pas couper le texte à cause de '
('Nouvelle inscription', 'Un nouvel étudiant s\'est inscrit au cours d\'Algorithmique avancée.', 'Inscription', '2026-02-15 16:00:00', TRUE, 1, 'admin'), -- statut_lu = TRUE l'utilisateur à lu la notif
('Alerte système', 'Sauvegarde de la base de données MySQL réussie.', 'Inscription', '2026-05-29 00:00:00', TRUE, 2, 'admin');