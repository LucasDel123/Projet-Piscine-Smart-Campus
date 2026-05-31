<?php

require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../utils/response.php';

Auth::requireRole('administrateur');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Méthode non autorisée. Utilisez POST.', 405);
}

$input = json_decode(file_get_contents('php://input'), true);
$niveau  = trim($input['niveau'] ?? '');
$groupe  = (int) ($input['groupe'] ?? 0);
$idCours = (int) ($input['id_cours'] ?? 0);

if ($niveau === '' || $groupe <= 0 || $idCours <= 0) {
    jsonError('Vous devez fournir un niveau, un groupe et un cours.', 400);
}

$pdo = Database::getConnection();

$stmt = $pdo->prepare("SELECT intitule, capacite_max FROM cours WHERE id_cours = :id");
$stmt->execute([':id' => $idCours]);
$cours = $stmt->fetch();
if (!$cours) {
    jsonError('Cours introuvable.', 404);
}

$stmt = $pdo->prepare("SELECT id_etudiant FROM etudiant WHERE niveau = :niveau AND groupe = :groupe");
$stmt->execute([':niveau' => $niveau, ':groupe' => $groupe]);
$etudiants = $stmt->fetchAll();

if (count($etudiants) === 0) {
    jsonError("Aucun étudiant trouvé pour la classe $niveau - Groupe $groupe.", 404);
}

$stmt = $pdo->prepare("SELECT id_etudiant FROM inscription WHERE id_cours = :id");
$stmt->execute([':id' => $idCours]);
$dejaInscrits = array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));

$aInscrire = [];
foreach ($etudiants as $e) {
    $id = (int) $e['id_etudiant'];
    if (!in_array($id, $dejaInscrits, true)) {
        $aInscrire[] = $id;
    }
}

if (count($aInscrire) === 0) {
    jsonError('Tous les étudiants de cette classe sont déjà inscrits à ce cours.', 409);
}

$placesRestantes = (int) $cours['capacite_max'] - count($dejaInscrits);

if (count($aInscrire) > $placesRestantes) {
    jsonError(
        'Places insuffisantes : ' . count($aInscrire) . ' étudiant(s) à inscrire pour seulement '
        . $placesRestantes . ' place(s) restante(s) dans « ' . $cours['intitule'] . ' ».',
        409
    );
}

$stmt = $pdo->prepare("INSERT INTO inscription (id_etudiant, id_cours, date_inscription)
                       VALUES (:idEtu, :idCours, CURDATE())");

$compteur = 0;
try {
    foreach ($aInscrire as $idEtu) {
        $stmt->execute([':idEtu' => $idEtu, ':idCours' => $idCours]);
        $compteur += $stmt->rowCount();
    }
} catch (PDOException $e) {
    jsonError('Erreur lors de l\'inscription du groupe : ' . $e->getMessage(), 500);
}

jsonSuccess(
    ['inscrits' => $compteur, 'id_cours' => $idCours],
    $compteur . ' étudiant(s) de la classe ' . $niveau . ' (Groupe ' . $groupe
        . ') inscrit(s) au cours « ' . $cours['intitule'] . ' ».',
    201
);
