<?php
// ====================================================================
// API : Désinscription d'un groupe/classe entière à un cours
// Fichier : backend/api/inscriptions/delete_classe.php
// Rôle : Mateo Cambon (Backend)
// ====================================================================

require_once __DIR__ . '/../../config_cors.php';
require_once __DIR__ . '/../../classes_database.php';

$pdo = Database::getConnection();

// 1. Récupération des données JSON envoyées par le frontend
$data = json_decode(file_get_contents("php://input"), true);

$niveau   = isset($data['niveau']) ? trim($data['niveau']) : '';  // ex: 'ING1'
$groupe   = isset($data['groupe']) ? intval($data['groupe']) : 0; // ex: 2
$id_cours = isset($data['id_cours']) ? intval($data['id_cours']) : 0;

if (empty($niveau) || $groupe === 0 || $id_cours === 0) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "Données incomplètes pour la désinscription de la classe."]);
    exit;
}

try {
    // 2. Trouver tous les ID des étudiants qui appartiennent à ce niveau et ce groupe
    $stmtEtudiants = $pdo->prepare("SELECT id_etudiant FROM etudiant WHERE niveau = :niveau AND groupe = :groupe");
    $stmtEtudiants->execute([
        'niveau' => $niveau,
        'groupe' => $groupe
    ]);
    $etudiants = $stmtEtudiants->fetchAll();

    if (count($etudiants) === 0) {
        http_response_code(404);
        echo json_encode(["success" => false, "error" => "Aucun étudiant trouvé dans la classe $niveau - Groupe $groupe."]);
        exit;
    }

    // 3. Extraction des IDs pour faire une suppression propre
    $ids = array_column($etudiants, 'id_etudiant');
    
    // On crée une chaîne de points d'interrogation (?, ?, ?) pour la clause IN de SQL
    $inClause = implode(',', array_fill(0, count($ids), '?'));

    // 4. Requête SQL pour désinscrire tous ces étudiants du cours ciblé
    $sql = "DELETE FROM inscription WHERE id_cours = ? AND id_etudiant IN ($inClause)";
    
    $stmtDelete = $pdo->prepare($sql);
    
    // On fusionne l'id_cours avec le tableau d'IDs des étudiants pour l'exécution
    $params = array_merge([$id_cours], $ids);
    $stmtDelete->execute($params);
    
    $nb_desinscriptions = $stmtDelete->rowCount();

    http_response_code(200);
    echo json_encode([
        "success" => true,
        "message" => "$nb_desinscriptions étudiants du groupe $niveau-$groupe ont été désinscrits du cours."
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => "Erreur BDD lors de la désinscription de masse : " . $e->getMessage()]);
}