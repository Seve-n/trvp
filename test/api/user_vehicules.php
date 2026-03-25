<?php
session_start();
require_once '../config/config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté']);
    exit;
}

$user_id = $_SESSION['user']['id'];

try {
    $pdo = new PDO("mysql:host=" . SERVER . ";dbname=" . BASE, USER, PASSWD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT id, brand, model FROM vehicules WHERE id_user = ?");
    $stmt->execute([$user_id]);

    echo json_encode(['success' => true, 'vehicules' => $stmt->fetchAll()]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur serveur']);
}
