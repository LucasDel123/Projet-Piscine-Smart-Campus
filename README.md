# Projet-Piscine-Smart-Campus
Projet Web Dynamique ECE 2025/2026

SmartCampus est une plateforme web dynamique de gestion académique conçue spécialement pour une école d'ingénieurs. Elle a pour objectif de centraliser et de simplifier les interactions entre les étudiants, les enseignants et l'administration autour des activités pédagogiques principales (cours, notes, emplois du temps)


Fonctionnalités Principales
L'application respecte des règles métier strictes et propose des espaces différenciés:
Système de rôles : 3 niveaux d'accès sécurisés (Étudiant, Enseignant, Administrateur).  
Tableaux de bord : Vues personnalisées par rôle affichant les cours, statistiques et notifications.
Administration complète : Gestion des utilisateurs, des cours, et des inscriptions aux enseignements.
Gestion des évaluations : Saisie des notes (CC, DS, Projets) et calcul automatique des moyennes.  Emploi du temps : Affichage dynamique des séances, des salles et des enseignants.


Architecture & Technologies
Le projet repose sur une architecture client-serveur stricte garantissant la séparation du frontend et du backend:
Frontend (Client) : Interface utilisateur développée en React (HTML, CSS, JavaScript).
Backend (Serveur) : API REST développée en PHP.
Base de données : Relationnelle, gérée sous MySQL (requêtes préparées avec PDO pour la sécurité).
Communication : Requêtes HTTP (Fetch/Axios) avec réponses standardisées au format JSON.  


Prérequis et Installation
Pour faire tourner ce projet localement, vous aurez besoin d'un environnement de développement comme WAMP ou XAMPP, ainsi que de Node.js pour la partie React. 

1. Base de données
Lancez Apache et MySQL via votre panneau de contrôle WAMP/XAMPP. Ouvrez phpMyAdmin.Créez une base de données nommée smartcampus.
Importez le fichier SQL fourni dans le dossier database/ pour créer la structure et le jeu de données.

3. Configuration du Backend (PHP)
Placez le dossier backend/ dans le répertoire web de votre serveur local. Configurez les accès à la base de données dans le fichier de configuration dédié.
Assurez-vous que le serveur Apache est bien en cours d'exécution.

5. Lancement du Frontend (React)
Ouvrez un terminal et placez-vous dans le dossier frontend/. Installez les dépendances nécessaires en exécutant la commande : npm install . Lancez le serveur de développement React : npm start.
L'application devrait s'ouvrir automatiquement dans votre navigateur.
