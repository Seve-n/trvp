<?php
define('HAZELLY_PORTAL', true);
require_once __DIR__ . '/config.php';

if (session_status() === PHP_SESSION_NONE) session_start();
session_destroy();
header('Location: index.php');
exit;
