<?php
session_start();
require_once '../config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$required = ['name', 'email', 'password', 'confirm_password'];
foreach ($required as $field) {
    if (empty($data[$field])) {
        echo json_encode(['success' => false, 'message' => "Champ $field manquant."]);
        exit;
    }
}

if ($data['password'] !== $data['confirm_password']) {
    echo json_encode(['success' => false, 'message' => "Les mots de passe ne correspondent pas."]);
    exit;
}

try {
    $pdo = new PDO("mysql:host=" . SERVER . ";dbname=" . BASE, USER, PASSWD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Insertion utilisateur
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->execute([
        $data['name'],
        $data['email'],
        password_hash($data['password'], PASSWORD_DEFAULT)
    ]);

    $user_id = $pdo->lastInsertId();

    // Si propose des trajets, on enregistre aussi le véhicule
    if (!empty($data['proposeTrajets'])) {
        $stmt = $pdo->prepare("INSERT INTO vehicules (brand, model, license_number, id_user) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $data['vehicle_brand'] ?? '',
            $data['vehicle_model'] ?? '',
            $data['license_plate'] ?? '',
            $user_id
        ]);
    }

    $_SESSION['user'] = [
        'id' => $user_id,
        'name' => $data['name'],
        'email' => $data['email']
    ];

    echo json_encode(['success' => true, 'message' => 'Inscription réussie.', 'user' => $_SESSION['user']]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur serveur : ' . $e->getMessage()]);
}
