<?php
// config/db.php - Connexion à la base de données via PDO

define('DB_HOST', 'localhost');
define('DB_NAME', 'tickets_support');
define('DB_USER', 'root');       // À modifier selon ton config WAMP/XAMPP
define('DB_PASS', '');           // À modifier si tu as un mot de passe

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die('<p style="color:red;font-family:sans-serif;">Erreur de connexion BDD : ' . $e->getMessage() . '</p>');
}
