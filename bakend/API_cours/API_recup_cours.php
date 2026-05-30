<?php
-- API : Récupération de la liste des cours avec filtrage par semestre

require_once __DIR__ . '/config_cors.php';
require_once __DIR__ . '/classes_database.php';

$pdo = Database::getConnection();

$semestre = isset($_GET['semestre']) ? trim($_GET['semestre']) : ''; --  Récup du filtre semestre envoyé en GET par le frontend

$sql = "SELECT c.*, e.nom AS enseignant_nom, e.prenom AS enseignant_prenom -- requête SQL
        FROM cours c
        LEFT JOIN enseignant e ON c.id_enseignant = e.id_enseignant 
        WHERE 1=1";

$params = [];

if (!empty($semestre)) {					-- Si un flitre est envoyé on applique le semestre demandé sinon on envoi tout
    $sql .= " AND c.semestre = :semestre";
    $params['semestre'] = $semestre;
}

try {
   
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $cours = $stmt->fetchAll();

   
    http_response_code(200); -- envoi réponse format JSON
    echo json_encode([
        "success" => true,
        "count" => count($cours),
        "data" => $cours
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => "Erreur lors de la récupération des cours : " . $e->getMessage()
    ]);
}