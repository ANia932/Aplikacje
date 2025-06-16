<?php
session_start();

try {
  $pdo = new PDO("pgsql:host=db;port=5432;dbname=moja_baza", "postgres", "postgres");
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Kalendarz: unikalne numery produkcji + najbliższy termin
  $kalendarz = $pdo->query("
    SELECT numer_produkcji, MIN(termin_dostarczenia) as najblizszy_termin
    FROM zamowienia
    WHERE archiwum = false
    GROUP BY numer_produkcji
    ORDER BY najblizszy_termin ASC
  ")->fetchAll(PDO::FETCH_ASSOC);

  // Opóźnienia
  $dzis = date('Y-m-d');
  $plus7 = date('Y-m-d', strtotime('+7 days'));

  $opoznione = $pdo->query("
    SELECT DISTINCT numer_produkcji
    FROM zamowienia
    WHERE archiwum = false AND termin_dostarczenia < '$dzis'
  ")->fetchAll(PDO::FETCH_COLUMN);

  $blisko_terminu = $pdo->query("
    SELECT DISTINCT numer_produkcji
    FROM zamowienia
    WHERE archiwum = false AND termin_dostarczenia BETWEEN '$dzis' AND '$plus7'
  ")->fetchAll(PDO::FETCH_COLUMN);

} catch (PDOException $e) {
  die("❌ Błąd połączenia z bazą: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8" />
  <title>Panel główny</title>
  <link rel="stylesheet" href="/public/styles/navbar.css" />
  <link rel="stylesheet" href="/public/styles/dashboard.css" />
</head>
<body>
  <header class="navbar">
    <div class="logo">
      <img src="/public/images/logo.png" alt="Logo" />
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

  <main class="dashboard-content">

<!-- KALENDARZ -->
<div class="card calendar-box">
  <h2>📅 Kalendarz zamówień</h2>
  <?php foreach ($kalendarz as $row): ?>
    <p>
      <strong><?= date("d.m", strtotime($row['najblizszy_termin'])) ?></strong> — <?= htmlspecialchars($row['numer_produkcji']) ?>
    </p>
  <?php endforeach; ?>
</div>

<!-- OPÓŹNIENIA -->
<div class="card delay-box">
  <h2>⏱ Poziom opóźnień</h2>

  <?php if (count($opoznione) > 0): ?>
    <p><strong>🔴 <?= count($opoznione) ?> opóźnione zamówienia</strong></p>
    <ul class="delay-list red">
      <?php foreach ($opoznione as $nr): ?>
        <li>#<?= htmlspecialchars($nr) ?></li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <?php if (count($blisko_terminu) > 0): ?>
    <p><strong>🟡 <?= count($blisko_terminu) ?> zamówienia blisko terminu</strong></p>
    <ul class="delay-list yellow">
      <?php foreach ($blisko_terminu as $nr): ?>
        <li>#<?= htmlspecialchars($nr) ?></li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <?php if (count($opoznione) === 0 && count($blisko_terminu) === 0): ?>
    <p><strong>🟢 Wszystkie zamówienia w terminie</strong></p>
  <?php endif; ?>
</div>

</main>
</body>
</html>