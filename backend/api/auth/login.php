<?php

require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../utils/response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Méthode non autorisée. Utilisez POST.', 405);
}

$input = json_decode(file_get_contents('php://input'), true);

$email = trim($input['email'] ?? '');
$motDePasse = $input['mot_de_passe'] ?? ($input['password'] ?? '');

if ($email === '' || $motDePasse === '') {
    jsonError('Email et mot de passe sont obligatoires.', 400);
}

$pdo = Database::getConnection();

$tables = [
    ['table' => 'etudiant',       'idCol' => 'id_etudiant',     'role' => 'etudiant'],
    ['table' => 'enseignant',     'idCol' => 'id_enseignant',   'role' => 'enseignant'],
    ['table' => 'administrateur', 'idCol' => 'id_admin',        'role' => 'administrateur'],
];

foreach ($tables as $t) {
    $sql = "SELECT * FROM {$t['table']} WHERE email = :email LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if ($user) {
        if (password_verify($motDePasse, $user['mdp'])) {

            Auth::login(
                $user[$t['idCol']],
                $t['role'],
                [
                    'nom'    => $user['nom'],
                    'prenom' => $user['prenom'],
                    'email'  => $user['email'],
                ]
            );

            jsonSuccess(
                [
                    'id'     => $user[$t['idCol']],
                    'role'   => $t['role'],
                    'nom'    => $user['nom'],
                    'prenom' => $user['prenom'],
                    'email'  => $user['email'],
                ],
                'Connexion réussie. Bienvenue ' . $user['prenom'] . ' !'
            );
        }

        jsonError('Email ou mot de passe incorrect.', 401);
    }
}

jsonError('Email ou mot de passe incorrect.', 401);
