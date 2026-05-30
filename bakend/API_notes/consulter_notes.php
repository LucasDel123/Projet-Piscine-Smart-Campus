<?php
require_once __DIR__ . '/../../config_cors.php';
require_once __DIR__ . '/../../classes_database.php';

$pdo = Database::getConnection();
$id_etudiant = isset($_GET['id_etudiant']) ? intval($_GET['id_etudiant']) : 0;

if ($id_etudiant === 0) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "ID étudiant manquant."]);
    exit;
}

try {
    $sql = "SELECT n.*, c.intitule, c.code, c.semestre 
            FROM note n
            INNER JOIN cours c ON n.id_cours = c.id_cours
            WHERE n.id_etudiant = :id_etudiant";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id_etudiant' => $id_etudiant]);
    $notes = $stmt->fetchAll();

    // Structuration des notes par cours
    $DonneesParCours = [];
    foreach ($notes as $n) {
        $id_c = $n['id_cours'];
        if (!isset($DonneesParCours[$id_c])) {
            $DonneesParCours[$id_c] = [
                "code" => $n['code'],
                "intitule" => $n['intitule'],
                "semestre" => $n['semestre'],
                "evaluations" => [],
                "CC" => ["somme" => 0, "coef_total" => 0],
                "DS" => ["somme" => 0, "coef_total" => 0],
                "Projet" => ["somme" => 0, "coef_total" => 0]
            ];
        }
        
        $DonneesParCours[$id_c]['evaluations'][] = [
            "id_note" => $n['id_note'],
            "valeur" => $n['valeur'],
            "type" => $n['type_evaluation'],
            "coefficient" => $n['coefficient'],
            "date" => $n['date_saisie'],
            "validee" => ($n['validee'] == 1)
        ];

        // Cumul pour calcul des sous-moyennes par type
        $t = $n['type_evaluation']; // 'CC', 'DS' ou 'Projet'
        if (isset($DonneesParCours[$id_c][$t])) {
            $DonneesParCours[$id_c][$t]['somme'] += ($n['valeur'] * $n['coefficient']);
            $DonneesParCours[$id_c][$t]['coef_total'] += $n['coefficient'];
        }
    }

    // Calcul final des moyennes pondérées du bulletin
    $bulletin = [];
    foreach ($DonneesParCours as $id_c => $c) {
        $moy_cc = $c['CC']['coef_total'] > 0 ? ($c['CC']['somme'] / $c['CC']['coef_total']) : null;
        $moy_ds = $c['DS']['coef_total'] > 0 ? ($c['DS']['somme'] / $c['DS']['coef_total']) : null;
        $moy_proj = $c['Projet']['coef_total'] > 0 ? ($c['Projet']['somme'] / $c['Projet']['coef_total']) : null;

        // Application des poids officiels (20% CC / 40% DS / 40% Projet)
        $ponderation_total = 0;
        $note_cumulee = 0;

        if ($moy_cc !== null) { $note_cumulee += ($moy_cc * 0.20); $ponderation_total += 0.20; }
        if ($moy_ds !== null) { $note_cumulee += ($moy_ds * 0.40); $ponderation_total += 0.40; }
        if ($moy_proj !== null) { $note_cumulee += ($moy_proj * 0.40); $ponderation_total += 0.40; }

        $moyenne_generale = $ponderation_total > 0 ? round(($note_cumulee / $ponderation_total), 2) : null;

        $bulletin[] = [
            "id_cours" => $id_c,
            "code" => $c['code'],
            "intitule" => $c['intitule'],
            "semestre" => $c['semestre'],
            "liste_notes" => $c['evaluations'],
            "moyenne_cc" => $moy_cc !== null ? round($moy_cc, 2) : null;
            "moyenne_ds" => $moy_ds !== null ? round($moy_ds, 2) : null;
            "moyenne_projet" => $moy_proj !== null ? round($moy_proj, 2) : null;
            "moyenne_generale" => $moyenne_generale
        ];
    }

    http_response_code(200);
    echo json_encode(["success" => true, "bulletin" => $bulletin]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => "Erreur : " . $e->getMessage()]);
}