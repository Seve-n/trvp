<?php
session_start();
header('Content-Type: application/json');
require_once '../config/config.php';

try {
  $pdo = new PDO("mysql:host=" . SERVER . ";dbname=" . BASE, USER, PASSWD);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(['success' => false, 'message' => 'Connexion BDD échouée.']);
  exit;
}

// 🔎 RECHERCHE (GET)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  $depart = $_GET['depart'] ?? '';
  $destination = $_GET['destination'] ?? '';
  $date = $_GET['date'] ?? '';

  $query = "SELECT 
      c.id, c.leaving, c.destination, c.leaving_date, c.price, c.description,
      v.brand, v.model, v.license_number,
      u.name
    FROM corides c
    JOIN vehicules v ON c.id_vehicule = v.id
    JOIN users u ON v.id_user = u.id
    WHERE 1=1";

  $params = [];

  if (!empty($depart)) {
    $query .= " AND c.leaving LIKE :depart";
    $params[':depart'] = "%$depart%";
  }
  if (!empty($destination)) {
    $query .= " AND c.destination LIKE :destination";
    $params[':destination'] = "%$destination%";
  }
  if (!empty($date)) {
    $query .= " AND DATE(c.leaving_date) = :date";
    $params[':date'] = $date;
  }

  $stmt = $pdo->prepare($query);
  $stmt->execute($params);
  echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
  exit;
}

// 📌 RÉSERVATION (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Connexion requise.']);
    exit;
  }

  $id_user = $_SESSION['user']['id'];
  $id_course = $_POST['id_course'] ?? null;

  if (!$id_course) {
    echo json_encode(['success' => false, 'message' => 'Trajet non spécifié.']);
    exit;
  }

  // Empêcher les doublons
  $check = $pdo->prepare("SELECT * FROM reservations WHERE id_user = ? AND id_course = ?");
  $check->execute([$id_user, $id_course]);
  if ($check->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Déjà réservé.']);
    exit;
  }

  // Réserver
  $stmt = $pdo->prepare("INSERT INTO reservations (id_user, id_course, status) VALUES (?, ?, 'waiting')");
  $stmt->execute([$id_user, $id_course]);

  echo json_encode(['success' => true, 'message' => 'Réservation enregistrée.']);
  exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
