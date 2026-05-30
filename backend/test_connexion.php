<?php

require_once __DIR__ . '/classes/Database.php';
require_once __DIR__ . '/utils/response.php';

try {
    $pdo = Database::getConnection();

    $stmt = $pdo->query('SHOW TABLES');
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    jsonSuccess(
        [
            'base_de_donnees' => DB_NAME,
            'nombre_de_tables' => count($tables),
            'tables' => $tables,
        ],
        'Connexion à la base de données réussie !'
    );

} catch (PDOException $e) {
    jsonError('Échec de la connexion : ' . $e->getMessage(), 500);
}
