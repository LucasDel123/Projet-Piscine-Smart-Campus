<?php
// ====================================================================
// API : Liste des cours suivis par un étudiant spécifique
// Fichier : backend/api/inscriptions/list.php
// Rôle : Mateo Cambon (Backend)
// ====================================================================

require_once __DIR__ . '/../../config_cors.php';
require_once __DIR__ . '/../../classes_database.php';

$pdo = Database::getConnection();

// 1. Récupération de l'ID de l'étudiant passé en paramètre GET (ex: ?id_etudiant=3)
$id_etudiant = isset($_GET['id_etudiant']) ? intval($_GET['id_etudiant']) : 0;

if ($id_etudiant === 0) {
    http_response_code(400);
    echo json_encode([
        "success" => false, 
        "error" => "Identifiant de l'étudiant (id_etudiant) manquant ou invalide."
    ]);
    exit;
}

try {
    // 2. Requête SQL pour récupérer les cours de l'étudiant via une jointure
    $sql = "SELECT i.id_inscription, i.date_inscription, c.*, e.nom AS enseignant_nom, e.prenom AS enseignant_prenom
            FROM inscription i
            INNER JOIN cours c ON i.id_cours = c.id_cours
            LEFT JOIN enseignant e ON c.id_enseignant = e.id_enseignant
            WHERE i.id_etudiant = :id_etudiant";

    // On utilise l'index stratégique qu'on a créé sur id_etudiant pour que ce soit ultra rapide !
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id_etudiant' => $id_etudiant]);
    $cours_suivis = $stmt->fetchAll();

    http_response_code(200);
    echo json_encode([
        "success" => true,
        "count" => count($cours_suivis),
        "data" => $cours_suivis
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false, 
        "error" => "Erreur lors de la récupération des inscriptions : " . $e->getMessage()
    ]);
}