<?php

require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../utils/response.php';

Auth::requireLogin();

$idEtudiant = (int) ($_GET['id_etudiant'] ?? 0);

if ($idEtudiant <= 0) {
    jsonError("L'identifiant de l'étudiant est manquant ou invalide.", 400);
}

$pdo = Database::getConnection();

$stmt = $pdo->prepare("SELECT nom, prenom FROM etudiant WHERE id_etudiant = :id");
$stmt->execute([':id' => $idEtudiant]);
$etudiant = $stmt->fetch();
if (!$etudiant) {
    jsonError('Étudiant introuvable.', 404);
}

$sql = "SELECT
            n.id_note, n.valeur, n.type_evaluation, n.coefficient, n.validee, n.date_saisie,
            c.id_cours, c.code_cours, c.intitule, c.semestre, c.credits
        FROM note n
        JOIN inscription i ON i.id_inscription = n.id_inscription
        JOIN cours c ON c.id_cours = i.id_cours
        WHERE i.id_etudiant = :id
        ORDER BY c.code_cours, n.date_saisie";

$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $idEtudiant]);
$lignes = $stmt->fetchAll();

$cours = [];
foreach ($lignes as $n) {
    $idCours = (int) $n['id_cours'];

    if (!isset($cours[$idCours])) {
        $cours[$idCours] = [
            'id_cours'         => $idCours,
            'code_cours'       => $n['code_cours'],
            'intitule'         => $n['intitule'],
            'semestre'         => $n['semestre'],
            'credits'          => (int) $n['credits'],
            'notes'            => [],
            'parType'          => [],
            'sommePonderee'    => 0.0,
            'sommeCoefficients' => 0.0,
        ];
    }

    $valeur = (float) $n['valeur'];
    $coef   = (float) $n['coefficient'];
    $type   = $n['type_evaluation'];

    $cours[$idCours]['notes'][] = [
        'id_note'         => (int) $n['id_note'],
        'valeur'          => $valeur,
        'type_evaluation' => $type,
        'coefficient'     => $coef,
        'validee'         => ((int) $n['validee'] === 1),
        'date_saisie'     => $n['date_saisie'],
    ];

    if (!isset($cours[$idCours]['parType'][$type])) {
        $cours[$idCours]['parType'][$type] = ['sommePonderee' => 0.0, 'sommeCoefficients' => 0.0];
    }
    $cours[$idCours]['parType'][$type]['sommePonderee']    += $valeur * $coef;
    $cours[$idCours]['parType'][$type]['sommeCoefficients'] += $coef;

    $cours[$idCours]['sommePonderee']    += $valeur * $coef;
    $cours[$idCours]['sommeCoefficients'] += $coef;
}

$bulletin = [];
$sommeGenerale = 0.0;
$sommeCreditsAvecNote = 0;

foreach ($cours as $c) {
    $moyennesParType = [];
    foreach ($c['parType'] as $type => $agg) {
        $moyennesParType[$type] = $agg['sommeCoefficients'] > 0
            ? round($agg['sommePonderee'] / $agg['sommeCoefficients'], 2)
            : null;
    }

    $moyenneCours = $c['sommeCoefficients'] > 0
        ? round($c['sommePonderee'] / $c['sommeCoefficients'], 2)
        : null;

    if ($moyenneCours !== null) {
        $sommeGenerale += $moyenneCours * $c['credits'];
        $sommeCreditsAvecNote += $c['credits'];
    }

    $bulletin[] = [
        'id_cours'         => $c['id_cours'],
        'code_cours'       => $c['code_cours'],
        'intitule'         => $c['intitule'],
        'semestre'         => $c['semestre'],
        'credits'          => $c['credits'],
        'notes'            => $c['notes'],
        'moyennes_par_type' => $moyennesParType,
        'moyenne_cours'    => $moyenneCours,
    ];
}

$moyenneGenerale = $sommeCreditsAvecNote > 0
    ? round($sommeGenerale / $sommeCreditsAvecNote, 2)
    : null;

jsonSuccess(
    [
        'etudiant'         => $etudiant['prenom'] . ' ' . $etudiant['nom'],
        'id_etudiant'      => $idEtudiant,
        'moyenne_generale' => $moyenneGenerale,
        'bulletin'         => $bulletin,
    ],
    'Bulletin généré (' . count($bulletin) . ' cours).'
);
