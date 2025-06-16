<?php
session_start();
require_once "token.php";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["email"], $_POST["password"], $_POST["csrf_token"])) {
    if (!verifyToken($_POST["csrf_token"])) {
        exit("Nieprawidłowy token CSRF.");
    }

    try {
        $pdo = new PDO("pgsql:host=db;port=5432;dbname=moja_baza", "postgres", "postgres");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $_POST['email']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($_POST['password'], $user['password'])) {
            exit("Nieprawidłowy e-mail lub hasło.");
        }

        $_SESSION['user'] = $user['email'];
        $_SESSION['dzial'] = $user['dzial'];

        header("Location: dashboard.php");
        exit;
    } catch (PDOException $e) {
        echo "Błąd połączenia: " . $e->getMessage();
    }
} else {
    echo "Nieprawidłowe dane wejściowe.";
}
?>