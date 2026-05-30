<?php
// ====================================================================
// API : Suppression d'un cours par l'administrateur
// Fichier : backend/api/cours/delete.php
// Rôle : Mateo Cambon (Backend)
// ====================================================================

require_once __DIR__ . '/../../config_cors.php';
require_once __DIR__ . '/../../classes_database.php';

$pdo = Database::getConnection();

// 1. Récupération des données (généralement en DELETE ou POST)
$data = json_decode(file_get_contents("php://input"), true);
$id_cours = isset($data['id_cours']) ? intval($data['id_cours']) : 0;

if ($id_cours === 0) {
    http_response_code(400);
    echo json_encode([
        "success" => false, 
        "error" => "Identifiant du cours (id_cours) manquant."
    ]);
    exit;
}

try {
    // 2. Vérifier si le cours existe avant de tenter la suppression
    $stmtCheck = $pdo->prepare("SELECT intitule FROM cours WHERE id_cours = :id_cours");
    $stmtCheck->execute(['id_cours' => $id_cours]);
    $cours = $stmtCheck->fetch();

    if (!$cours) {
        http_response_code(404);
        echo json_encode([
            "success" => false, 
            "error" => "Ce cours n'existe pas ou a déjà été supprimé."
        ]);
        exit;
    }

    // 3. Suppression du cours
    // (Le ON DELETE CASCADE de la BDD nettoie automatiquement les inscriptions liées !)
    $stmtDelete = $pdo->prepare("DELETE FROM cours WHERE id_cours = :id_cours");
    $stmtDelete->execute(['id_cours' => $id_cours]);

    http_response_code(200);
    echo json_encode([
        "success" => true,
        "message" => "Le cours '" . $cours['intitule'] . "' a bien été retiré du système."
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false, 
        "error" => "Erreur système lors de la suppression : " . $e->getMessage()
    ]);
}