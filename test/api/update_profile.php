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

$user_id = $_SESSION['user']['id'];
$name = trim($data['name'] ?? '');
$email = trim($data['email'] ?? '');
$old_pass = $data['old_password'] ?? '';
$new_pass = $data['new_password'] ?? '';
$confirm_new_pass = $data['confirm_new_password'] ?? '';

if (!$name || !$email) {
    echo json_encode(['success' => false, 'message' => 'Nom ou email manquant.']);
    exit;
}

try {
    $pdo = new PDO("mysql:host=" . SERVER . ";dbname=" . BASE, USER, PASSWD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // MAJ nom + email
    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
    $stmt->execute([$name, $email, $user_id]);

    // MAJ mot de passe
    if ($old_pass && $new_pass && $new_pass === $confirm_new_pass) {
        $check = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $check->execute([$user_id]);
        $user = $check->fetch();

        if (password_verify($old_pass, $user['password'])) {
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([password_hash($new_pass, PASSWORD_DEFAULT), $user_id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Mot de passe actuel incorrect.']);
            exit;
        }
    }

    // MAJ ou insertion véhicule
    if (!empty($data['proposeTrajets'])) {
        $veh = $pdo->prepare("SELECT id FROM vehicules WHERE id_user = ?");
        $veh->execute([$user_id]);

        if ($veh->fetch()) {
            $stmt = $pdo->prepare("UPDATE vehicules SET brand = ?, model = ?, license_number = ? WHERE id_user = ?");
        } else {
            $stmt = $pdo->prepare("INSERT INTO vehicules (brand, model, license_number, id_user) VALUES (?, ?, ?, ?)");
        }

        $stmt->execute([
            $data['vehicle_brand'] ?? '',
            $data['vehicle_model'] ?? '',
            $data['license_plate'] ?? '',
            $user_id
        ]);
    }

    echo json_encode(['success' => true, 'message' => 'Profil mis à jour.']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur serveur : ' . $e->getMessage()]);
}
