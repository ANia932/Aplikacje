<?php
// Upewnij się, że nie ma nic przed tym otwarciem PHP!
session_start();

$allowedPages = [
    "login" => "public/views/login.html",
    "register" => "public/views/register.html",
    "dashboard" => "public/views/dashboard.html",
    "orders" => "public/views/orders.html",
    "pricing" => "public/views/pricing.html"
];

$page = $_GET['page'] ?? 'login';

if (!isset($allowedPages[$page])) {
    http_response_code(404);
    echo "404 - Nie znaleziono strony";
    exit;
}

// Blokada stron wymagających logowania
$requiresLogin = ["dashboard", "orders", "pricing"];
if (in_array($page, $requiresLogin) && !isset($_SESSION['user'])) {
    header("Location: index.php?page=login");
    exit;
}

// Wczytaj plik HTML
include $allowedPages[$page];
