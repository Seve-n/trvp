<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json');

require_once '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode([
        'connected' => isset($_SESSION['user']),
        'user' => $_SESSION['user'] ?? null
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    try {
        if (isset($_SESSION['user'])) {
            $pdo = new PDO("mysql:host=" . SERVER . ";dbname=" . BASE, USER, PASSWD);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $pdo->prepare("UPDATE users SET remember_token = NULL WHERE id = ?");
            $stmt->execute([$_SESSION['user']['id']]);
        }
    } catch (PDOException $e) {
        // log or debug
        echo json_encode(['connected' => false, 'error' => 'Erreur base de données']);
        exit;
    }

    setcookie('remember_token', '', time() - 3600, "/");
    session_destroy();
    echo json_encode(['connected' => false]);
    exit;
}

http_response_code(405);
