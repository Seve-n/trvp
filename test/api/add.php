<?php
session_start();
require_once '../config/config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$required = ['leaving', 'destination', 'leaving_date', 'price', 'id_vehicule'];
foreach ($required as $field) {
    if (empty($data[$field])) {
        echo json_encode(['success' => false, 'message' => "Champ $field manquant."]);
        exit;
    }
}

try {
    $pdo = new PDO("mysql:host=" . SERVER . ";dbname=" . BASE, USER, PASSWD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("INSERT INTO corides (leaving, destination, leaving_date, price, description, id_vehicule) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $data['leaving'],
        $data['destination'],
        $data['leaving_date'],
        $data['price'],
        $data['description'] ?? '',
        $data['id_vehicule']
    ]);

    echo json_encode(['success' => true, 'message' => 'Trajet ajouté avec succès.']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur serveur : ' . $e->getMessage()]);
}
