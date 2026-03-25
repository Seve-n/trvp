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

    $userStmt = $pdo->prepare("SELECT name, email FROM users WHERE id = ?");
    $userStmt->execute([$user_id]);
    $user = $userStmt->fetch();

    $vehStmt = $pdo->prepare("SELECT brand, model, license_number FROM vehicules WHERE id_user = ?");
    $vehStmt->execute([$user_id]);
    $vehicule = $vehStmt->fetch();

    echo json_encode(['success' => true, 'user' => $user, 'vehicule' => $vehicule]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur serveur']);
}
