<?php

require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../utils/response.php';

Auth::requireRole('administrateur');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Méthode non autorisée. Utilisez POST.', 405);
}

$input = json_decode(file_get_contents('php://input'), true);

$idSeance   = (int) ($input['id_seance'] ?? 0);
$idCours    = (int) ($input['id_cours'] ?? 0);
$date       = trim($input['date_seance'] ?? '');
$heureDebut = trim($input['heure_debut'] ?? '');
$heureFin   = trim($input['heure_fin'] ?? '');
$salle      = trim($input['salle'] ?? '');

if ($idSeance <= 0) {
    jsonError("L'identifiant de la séance est manquant ou invalide.", 400);
}
if ($idCours <= 0 || $date === '' || $heureDebut === '' || $heureFin === '' || $salle === '') {
    jsonError('Tous les champs (cours, date, heures, salle) sont obligatoires.', 400);
}
if ($heureFin <= $heureDebut) {
    jsonError("L'heure de fin doit être postérieure à l'heure de début.", 400);
}

$pdo = Database::getConnection();

$stmt = $pdo->prepare("SELECT id_seance FROM seance WHERE id_seance = :id");
$stmt->execute([':id' => $idSeance]);
if (!$stmt->fetch()) {
    jsonError('Séance introuvable.', 404);
}

$stmt = $pdo->prepare("SELECT id_enseignant FROM cours WHERE id_cours = :id");
$stmt->execute([':id' => $idCours]);
$coursRow = $stmt->fetch();
if (!$coursRow) {
    jsonError('Cours introuvable.', 404);
}
$idEnseignant = $coursRow['id_enseignant'];

$sqlSalle = "SELECT s.id_seance, c.code_cours
             FROM seance s
             JOIN cours c ON c.id_cours = s.id_cours
             WHERE s.salle = :salle
               AND s.date_seance = :date
               AND s.heure_debut < :fin
               AND s.heure_fin   > :debut
               AND s.id_seance  <> :self
             LIMIT 1";
$stmt = $pdo->prepare($sqlSalle);
$stmt->execute([':salle' => $salle, ':date' => $date, ':fin' => $heureFin, ':debut' => $heureDebut, ':self' => $idSeance]);
$conflitSalle = $stmt->fetch();
if ($conflitSalle) {
    jsonError("Conflit : la salle « $salle » est déjà occupée par {$conflitSalle['code_cours']} sur ce créneau.", 409);
}

if (!empty($idEnseignant)) {
    $sqlEns = "SELECT s.id_seance, c.code_cours
               FROM seance s
               JOIN cours c ON c.id_cours = s.id_cours
               WHERE c.id_enseignant = :ens
                 AND s.date_seance = :date
                 AND s.heure_debut < :fin
                 AND s.heure_fin   > :debut
                 AND s.id_seance  <> :self
               LIMIT 1";
    $stmt = $pdo->prepare($sqlEns);
    $stmt->execute([':ens' => $idEnseignant, ':date' => $date, ':fin' => $heureFin, ':debut' => $heureDebut, ':self' => $idSeance]);
    $conflitEns = $stmt->fetch();
    if ($conflitEns) {
        jsonError("Conflit : l'enseignant de ce cours a déjà la séance {$conflitEns['code_cours']} sur ce créneau.", 409);
    }
}

$sql = "UPDATE seance
        SET date_seance = :date, heure_debut = :debut, heure_fin = :fin, salle = :salle, id_cours = :id_cours
        WHERE id_seance = :id";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':date'     => $date,
        ':debut'    => $heureDebut,
        ':fin'      => $heureFin,
        ':salle'    => $salle,
        ':id_cours' => $idCours,
        ':id'       => $idSeance,
    ]);

    jsonSuccess(['id_seance' => $idSeance], 'Séance modifiée avec succès.');
} catch (PDOException $e) {
    jsonError('Erreur lors de la modification : ' . $e->getMessage(), 500);
}
