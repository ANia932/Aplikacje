<?php
require_once "token.php";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["email"], $_POST["haslo"], $_POST["csrf_token"])) {
    if (!verifyToken($_POST["csrf_token"])) {
        exit("Nieprawidłowy token CSRF.");
    }

    try {
        $pdo = new PDO("pgsql:host=db;port=5432;dbname=moja_baza", "postgres", "postgres");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $email = $_POST['email'];
        $hashed_password = password_hash($_POST['haslo'], PASSWORD_DEFAULT);

        $check = $pdo->prepare("SELECT id FROM users WHERE email = :email");
        $check->execute([':email' => $email]);
        if ($check->rowCount() > 0) {
            exit("Użytkownik o tym adresie e-mail już istnieje.");
        }

        $stmt = $pdo->prepare("INSERT INTO users (imie, nazwisko, email, password, stanowisko, dzial, is_active)
                               VALUES (:imie, :nazwisko, :email, :password, :stanowisko, :dzial, true)");

        $stmt->execute([
            ':imie' => $_POST['imie'] ?? '',
            ':nazwisko' => $_POST['nazwisko'] ?? '',
            ':email' => $email,
            ':password' => $hashed_password,
            ':stanowisko' => $_POST['stanowisko'] ?? '',
            ':dzial' => $_POST['dzial'] ?? ''
        ]);

        echo "✅ Rejestracja zakończona sukcesem. Możesz się teraz zalogować.";
    } catch (PDOException $e) {
        echo "Błąd: " . $e->getMessage();
    }
} else {
    echo "Nieprawidłowe dane wejściowe.";
}
?>