<?php
require_once __DIR__ . '/../../config_cors.php';
require_once __DIR__ . '/../../classes_database.php';

$pdo = Database::getConnection();
$data = json_decode(file_get_contents("php://input"), true);

$id_note          = isset($data['id_note']) ? intval($data['id_note']) : 0;
$id_enseignant    = isset($data['id_enseignant']) ? intval($data['id_enseignant']) : 0;
$nouvelle_valeur  = isset($data['valeur']) ? floatval($data['valeur']) : -1;

if ($id_note === 0 || $id_enseignant === 0 || $nouvelle_valeur < 0) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "Champs requis manquants."]);
    exit;
}

try {
    // Récupérer la note et vérifier le verrouillage
    $stmtNote = $pdo->prepare("SELECT n.*, c.id_enseignant FROM note n INNER JOIN cours c ON n.id_cours = c.id_cours WHERE n.id_note = :id_note");
    $stmtNote->execute(['id_note' => $id_note]);
    $note = $stmtNote->fetch();

    if (!$note) {
        http_response_code(404);
        echo json_encode(["success" => false, "error" => "Note introuvable."]);
        exit;
    }

    if ($note['id_enseignant'] != $id_enseignant) {
        http_response_code(403);
        echo json_encode(["success" => false, "error" => "Vous n'êtes pas le professeur de ce cours."]);
        exit;
    }

    if ($note['validee'] == 1) {
        http_response_code(403);
        echo json_encode(["success" => false, "error" => "Verrouillage actif : Cette note est validée définitivement."]);
        exit;
    }

    $stmtUpdate = $pdo->prepare("UPDATE note SET valeur = :valeur WHERE id_note = :id_note");
    $stmtUpdate->execute(['valeur' => $nouvelle_valeur, 'id_note' => $id_note]);

    http_response_code(200);
    echo json_encode(["success" => true, "message" => "Note mise à jour !"]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => "Erreur système : " . $e->getMessage()]);
}