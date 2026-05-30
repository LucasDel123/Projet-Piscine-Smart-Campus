<?php
require_once __DIR__ . '/../../config_cors.php';
require_once __DIR__ . '/../../classes_database.php';

$pdo = Database::getConnection();
$data = json_decode(file_get_contents("php://input"), true);

$id_enseignant    = isset($data['id_enseignant']) ? intval($data['id_enseignant']) : 0;
$id_etudiant      = isset($data['id_etudiant']) ? intval($data['id_etudiant']) : 0;
$id_cours         = isset($data['id_cours']) ? intval($data['id_cours']) : 0;
$valeur           = isset($data['valeur']) ? floatval($data['valeur']) : -1;
$type_evaluation  = isset($data['type_evaluation']) ? trim($data['type_evaluation']) : ''; // 'CC', 'DS', 'Projet'
$coefficient      = isset($data['coefficient']) ? floatval($data['coefficient']) : 1.0;

if ($id_enseignant === 0 || $id_etudiant === 0 || $id_cours === 0 || $valeur < 0 || empty($type_evaluation)) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "Données incomplètes pour l'ajout de la note."]);
    exit;
}

try {
    // Vérification prof
    $stmtCheck = $pdo->prepare("SELECT id_cours FROM cours WHERE id_cours = :id_cours AND id_enseignant = :id_enseignant");
    $stmtCheck->execute(['id_cours' => $id_cours, 'id_enseignant' => $id_enseignant]);
    if (!$stmtCheck->fetch()) {
        http_response_code(403);
        echo json_encode(["success" => false, "error" => "Vous n'enseignez pas dans ce cours."]);
        exit;
    }

    // Insertion d'une nouvelle note indépendante
    $sql = "INSERT INTO note (valeur, type_evaluation, coefficient, date_saisie, validee, id_etudiant, id_cours) 
            VALUES (:valeur, :type, :coef, :date_saisie, 0, :id_etudiant, :id_cours)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'valeur'      => $valeur,
        'type'        => $type_evaluation,
        'coef'        => $coefficient,
        'date_saisie' => date('Y-m-d'),
        'id_etudiant' => $id_etudiant,
        'id_cours'    => $id_cours
    ]);

    http_response_code(201);
    echo json_encode(["success" => true, "message" => "Note ajoutée avec succès !"]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => "Erreur BDD : " . $e->getMessage()]);
}
