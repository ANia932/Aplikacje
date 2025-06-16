<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['dzial'] !== 'biuro') {
  header("Location: login.html");
  exit;
}

function generateOrderNumber($pdo) {
  $today = date("Ymd");
  $prefix = "ZAM$today";

  $stmt = $pdo->prepare("SELECT COUNT(*) FROM zamowienia WHERE TO_CHAR(data_dodania, 'YYYYMMDD') = ?");
  $stmt->execute([$today]);
  $count = $stmt->fetchColumn() + 1;

  return $prefix . "-" . str_pad($count, 3, "0", STR_PAD_LEFT);
}

$message = "";

try {
  $pdo = new PDO("pgsql:host=db;port=5432;dbname=moja_baza", "postgres", "postgres");
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Pobranie listy komponentów
  $komponenty = $pdo->query("SELECT DISTINCT nazwa FROM komponenty ORDER BY nazwa ASC")->fetchAll(PDO::FETCH_COLUMN);

  if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $numer = generateOrderNumber($pdo);
    $stmt = $pdo->prepare("INSERT INTO zamowienia (
      numer_zamowienia, komponent, ilosc, numer_produkcji, lokalizacja, miejsce_dostawy,
      status, przypisanie, termin_dostarczenia, realna_dostawa, postep
    ) VALUES (
      :numer, :komponent, :ilosc, :numer_produkcji, :lokalizacja, :miejsce_dostawy,
      :status, :przypisanie, :termin_dostarczenia, :realna_dostawa, :postep
    )");

    $stmt->execute([
      ':numer' => $numer,
      ':komponent' => $_POST['komponent'],
      ':ilosc' => $_POST['ilosc'],
      ':numer_produkcji' => $_POST['numer_produkcji'],
      ':lokalizacja' => $_POST['lokalizacja'],
      ':miejsce_dostawy' => $_POST['miejsce_dostawy'],
      ':status' => $_POST['status'],
      ':przypisanie' => $_POST['przypisanie'],
      ':termin_dostarczenia' => $_POST['termin_dostarczenia'],
      ':realna_dostawa' => null,
      ':postep' => 0
    ]);

    $message = "✅ Zamówienie zostało dodane. Numer: <strong>$numer</strong>";
  }
} catch (PDOException $e) {
  $message = "❌ Błąd: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8" />
  <title>Dodaj zamówienie</title>
  <link rel="stylesheet" href="/public/styles/pricing.css" />
  <link rel="stylesheet" href="/public/styles/navbar.css" />
</head>
<body>
  <header class="navbar">
    <div class="logo">
      <img src="/public/images/logo.png" alt="Logo">
    </div>
    <nav class="nav-wrapper">
    <ul class="nav-links">
      <li><a href="dashboard.php">Panel główny</a></li>
      <li><a href="orders.php">Zamówienia</a></li>
         <?php if ($_SESSION['dzial'] === 'biuro'): ?>
      <li><a href="archive.php">Archiwum</a></li>
         <?php endif; ?>
      <li><a href="pricing.php">Cennik</a></li>
      <li><a href="logout.php" class="logout-button">Wyloguj się</a></li>
    </ul>
    </nav>
  </header>

  <section class="form-section" style="padding: 20px;">
    <h2>DODAJ NOWE ZAMÓWIENIE</h2>
    <?php if ($message): ?>
      <p style="text-align:center;"><strong><?= $message ?></strong></p>
    <?php endif; ?>
    <form method="post">
      <select name="komponent" required>
        <option value="">-- Wybierz komponent --</option>
        <?php foreach ($komponenty as $k): ?>
          <option value="<?= htmlspecialchars($k) ?>"><?= htmlspecialchars($k) ?></option>
        <?php endforeach; ?>
      </select>

      <input type="number" name="ilosc" placeholder="Ilość" required>
      <input type="text" name="numer_produkcji" placeholder="Numer produkcji">
      <input type="text" name="lokalizacja" placeholder="Lokalizacja docelowa">
      <input type="text" name="miejsce_dostawy" placeholder="Miejsce dostawy">

      <select name="status">
        <option value="Nowe">Nowe</option>
        <option value="Potwierdzone">Potwierdzone</option>
        <option value="W realizacji">W realizacji</option>
        <option value="Gotowe">Gotowe</option>
        <option value="Dostarczone">Dostarczone</option>
      </select>

      <input type="text" name="przypisanie" placeholder="Przypisanie (np. Jan Kowalski)">
      <input type="date" name="termin_dostarczenia" placeholder="Termin dostarczenia">
      <button type="submit">Dodaj zamówienie</button>
    </form>
  </section>
</body>
</html>
