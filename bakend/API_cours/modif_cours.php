<?php
// ====================================================================
// API : Modification d'un cours existant par l'administrateur
// Fichier : backend/api/cours/update.php
// Rôle : Mateo Cambon (Backend)
// ====================================================================

require_once __DIR__ . '/../../config_cors.php';
require_once __DIR__ . '/../../classes_database.php';

$pdo = Database::getConnection();

// 1. Récupération des données JSON envoyées en PUT ou POST
$data = json_decode(file_get_contents("php://input"), true);

$id_cours     = isset($data['id_cours']) ? intval($data['id_cours']) : 0;
$intitule     = isset($data['intitule']) ? trim($data['intitule']) : '';
$code         = isset($data['code']) ? trim(strtoupper($data['code'])) : '';
$semestre     = isset($data['semestre']) ? trim(strtoupper($data['semestre'])) : '';
$capacite_max = isset($data['capacite_max']) ? intval($data['capacite_max']) : 0;
$id_enseignant = isset($data['id_enseignant']) ? intval($data['id_enseignant']) : null;

// 2. Vérification des identifiants et des champs obligatoires
if ($id_cours === 0 || empty($intitule) || empty($code) || empty($semestre) || $capacite_max <= 0) {
    http_response_code(400);
    echo json_encode([
        "success" => false, 
        "error" => "Données incomplètes ou invalides pour la modification."
    ]);
    exit;
}

try {
    // 3. RÈGLE MÉTIER : Vérifier que le code modifié n'est pas déjà pris par UN AUTRE cours
    $stmtCheck = $pdo->prepare("SELECT id_cours FROM cours WHERE code = :code AND id_cours != :id_cours");
    $stmtCheck->execute([
        'code' => $code,
        'id_cours' => $id_cours
    ]);
    
    if ($stmtCheck->fetch()) {
        http_response_code(400);
        echo json_encode([
            "success" => false, 
            "error" => "Erreur : Le code '$code' est déjà utilisé par un autre cours du catalogue."
        ]);
        exit;
    }

    // 4. Exécution de la mise à jour
    $sql = "UPDATE cours 
            SET intitule = :intitule, code = :code, semestre = :semestre, capacite_max = :capacite_max, id_enseignant = :id_enseignant 
            WHERE id_cours = :id_cours";
            
    $stmtUpdate = $pdo->prepare($sql);
    $stmtUpdate->execute([
        'intitule'      => $intitule,
        'code'          => $code,
        'semestre'      => $semestre,
        'capacite_max'  => $capacite_max,
        'id_enseignant' => $id_enseignant,
        'id_cours'      => $id_cours
    ]);

    http_response_code(200);
    echo json_encode([
        "success" => true,
        "message" => "Le cours a été modifié avec succès !"
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false, 
        "error" => "Erreur lors de la modification : " . $e->getMessage()
    ]);
}