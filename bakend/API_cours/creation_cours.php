<?php
-- ====================================================================
-- API : Création d'un nouveau cours par l'administrateur
-- Fichier : backend/api/cours/create.php
-- Rôle : Mateo Cambon (Backend)
-- ====================================================================

require_once __DIR__ . '/../../config_cors.php';
require_once __DIR__ . '/../../classes_database.php';

$pdo = Database::getConnection();

-- 1. Récupération des données JSON envoyées en POST par le frontend
$data = json_decode(file_get_contents("php://input"), true);

$intitule     = isset($data['intitule']) ? trim($data['intitule']) : '';
$code         = isset($data['code']) ? trim(strtoupper($data['code'])) : ''; -- ex: ELEC4102
$semestre     = isset($data['semestre']) ? trim(strtoupper($data['semestre'])) : ''; -- ex: S3
$capacite_max = isset($data['capacite_max']) ? intval($data['capacite_max']) : 0;
$id_enseignant = isset($data['id_enseignant']) ? intval($data['id_enseignant']) : null;

-- 2. Vérification des champs obligatoires
if (empty($intitule) || empty($code) || empty($semestre) || $capacite_max <= 0) {
    http_response_code(400);
    echo json_encode([
        "success" => false, 
        "error" => "Données incomplètes ou capacité maximale invalide."
    ]);
    exit;
}

try {
    -- 3. RÈGLE MÉTIER : Vérifier si le code du cours existe déjà
    $stmtCheck = $pdo->prepare("SELECT id_cours FROM cours WHERE code = :code");
    $stmtCheck->execute(['code' => $code]);
    
    if ($stmtCheck->fetch()) {
        http_response_code(400);
        echo json_encode([
            "success" => false, 
            "error" => "Erreur : Un cours avec le code '$code' existe déjà dans le catalogue."
        ]);
        exit;
    }

    -- 4. Tout est OK ! Insertion du nouveau cours
    $sql = "INSERT INTO cours (intitule, code, semestre, capacite_max, id_enseignant) 
            VALUES (:intitule, :code, :semestre, :capacite_max, :id_enseignant)";
            
    $stmtInsert = $pdo->prepare($sql);
    $stmtInsert->execute([
        'intitule'     => $intitule,
        'code'         => $code,
        'semestre'     => $semestre,
        'capacite_max' => $capacite_max,
        'id_enseignant'=> $id_enseignant -- Peut être null si aucun prof n'est encore assigné
    ]);

    http_response_code(201);
    echo json_encode([
        "success" => true,
        "message" => "Le cours '$intitule' ($code) a été créé avec succès !",
        "id_cours" => $pdo->lastInsertId() -- Renvoie l'ID généré automatiquement
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false, 
        "error" => "Erreur lors de la création du cours : " . $e->getMessage()
    ]);
}