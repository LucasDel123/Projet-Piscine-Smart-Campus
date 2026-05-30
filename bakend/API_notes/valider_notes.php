<?php
require_once __DIR__ . '/../../config_cors.php';
require_once __DIR__ . '/../../classes_database.php';

$pdo = Database::getConnection();
$data = json_decode(file_get_contents("php://input"), true);

$id_enseignant = isset($data['id_enseignant']) ? intval($data['id_enseignant']) : 0;
$id_etudiant   = isset($data['id_etudiant']) ? intval($data['id_etudiant']) : 0;
$id_cours      = isset($data['id_cours']) ? intval($data['id_cours']) : 0;

if ($id_enseignant === 0 || $id_etudiant === 0 || $id_cours === 0) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "Données incomplètes."]);
    exit;
}

try {
    // Validation prof
    $stmtCheck = $pdo->prepare("SELECT id_cours FROM cours WHERE id_cours = :id_cours AND id_enseignant = :id_enseignant");
    $stmtCheck->execute(['id_cours' => $id_cours, 'id_enseignant' => $id_enseignant]);
    if (!$stmtCheck->fetch()) {
        http_response_code(403);
        echo json_encode(["success" => false, "error" => "Action refusée."]);
        exit;
    }

    // On verrouille TOUTES les notes de cet élève dans ce cours d'un coup
    $stmtLock = $pdo->prepare("UPDATE note SET validee = 1 WHERE id_etudiant = :id_etudiant AND id_cours = :id_cours");
    $stmtLock->execute(['id_etudiant' => $id_etudiant, 'id_cours' => $id_cours]);

    http_response_code(200);
    echo json_encode(["success" => true, "message" => "Toutes les notes de l'étudiant pour ce cours sont verrouillées."]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => "Erreur : " . $e->getMessage()]);
}