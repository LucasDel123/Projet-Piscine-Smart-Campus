<?php
-- ====================================================================
-- API : Inscription d'un groupe/classe spécifique (Niveau + Groupe INT)
-- Fichier : bakend/api_inscription_classe.php
-- Rôle : Mateo Cambon (Backend)
-- ====================================================================

require_once __DIR__ . '/config_cors.php';
require_once __DIR__ . '/classes_database.php';

$pdo = Database::getConnection();

-- Récupération des données JSON envoyées par le frontend React
$data = json_decode(file_get_contents("php://input"), true);

$niveau   = isset($data['niveau']) ? trim($data['niveau']) : '';     -- ex: 'ING1'
$groupe   = isset($data['groupe']) ? intval($data['groupe']) : 0;    -- ex: 2 (Forcé en INT)
$id_cours = isset($data['id_cours']) ? intval($data['id_cours']) : 0;

-- Validation de la présence des paramètres obligatoires
if (empty($niveau) || $groupe === 0 || $id_cours === 0) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "Données incomplètes (niveau, groupe ou id_cours manquant)."]);
    exit;
}

try {
    -- 1. Récupérer les informations du cours (intitulé et capacité maximale)
    $stmtCours = $pdo->prepare("SELECT intitule, capacite_max FROM cours WHERE id_cours = :id_cours");
    $stmtCours->execute(['id_cours' => $id_cours]);
    $cours = $stmtCours->fetch();

    if (!$cours) {
        http_response_code(404);
        echo json_encode(["success" => false, "error" => "Ce cours n'existe pas."]);
        exit;
    }

    -- 2. Sélectionner tous les étudiants qui correspondent au combo Niveau + Groupe
    $stmtEtudiants = $pdo->prepare("SELECT id_etudiant FROM etudiant WHERE niveau = :niveau AND groupe = :groupe");
    $stmtEtudiants->execute([
        'niveau' => $niveau,
        'groupe' => $groupe
    ]);
    $etudiants = $stmtEtudiants->fetchAll();

    if (count($etudiants) === 0) {
        http_response_code(404);
        echo json_encode(["success" => false, "error" => "Aucun étudiant trouvé dans la classe : $niveau - Groupe $groupe."]);
        exit;
    }

    -- 3. Comptabiliser les places déjà occupées dans le cours
    $stmtCount = $pdo->prepare("SELECT COUNT(*) AS total FROM inscription WHERE id_cours = :id_cours");
    $stmtCount->execute(['id_cours' => $id_cours]);
    $nb_inscrits = $stmtCount->fetch()['total'];

    $places_restantes = $cours['capacite_max'] - $nb_inscrits;
    $nb_nouveaux = count($etudiants);

    -- Bloquer l'inscription si le groupe dépasse les capacités d'accueil restantes
    if ($nb_nouveaux > $places_restantes) {
        http_response_code(400);
        echo json_encode([
            "success" => false, 
            "error" => "Désolé, places insuffisantes. Le groupe compte $nb_nouveaux étudiants mais il ne reste que $places_restantes places dans le cours de " . $cours['intitule']
        ]);
        exit;
    }

    -- 4. Inscription de masse sécurisée
    $date_du_jour = date('Y-m-d');
    
    -- Le INSERT IGNORE évite les crashs si un étudiant du groupe est déjà inscrit individuellement
    $stmtInsert = $pdo->prepare("
        INSERT IGNORE INTO inscription (date_inscription, id_etudiant, id_cours) 
        VALUES (:date_inscr, :id_etudiant, :id_cours)
    ");

    $compteur_inscriptions = 0;
    foreach ($etudiants as $etudiant) {
        $stmtInsert->execute([
            'date_inscr'  => $date_du_jour,
            'id_etudiant' => $etudiant['id_etudiant'],
            'id_cours'    => $id_cours
        ]);
        -- Incrémente uniquement si la ligne a effectivement été ajoutée
        $compteur_inscriptions += $stmtInsert->rowCount();
    }

    http_response_code(201);
    echo json_encode([
        "success" => true,
        "message" => "$compteur_inscriptions étudiants de la classe $niveau (Groupe $groupe) ont été inscrits avec succès au cours de " . $cours['intitule']
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => "Erreur système BDD : " . $e->getMessage()]);
}