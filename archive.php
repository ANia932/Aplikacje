<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['dzial'] !== 'biuro') {
  header("Location: dashboard.php");
  exit;
}

try {
  $pdo = new PDO("pgsql:host=db;port=5432;dbname=moja_baza", "postgres", "postgres");
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $zamowienia = $pdo->query("SELECT * FROM zamowienia WHERE archiwum = true ORDER BY data_dodania DESC")->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
  die("❌ Błąd połączenia z bazą: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8" />
  <title>Archiwum zamówień</title>
  <link rel="stylesheet" href="/public/styles/navbar.css" />
  <link rel="stylesheet" href="/public/styles/orders.css" />
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

  <main class="container">
    <h2 class="section-title">ARCHIWUM ZAMÓWIEŃ</h2>

    <div class="table-wrapper">
      <table>
        <thead>
          <tr>
            <th>Numer</th>
            <th>Nazwa komponentu</th>
            <th>Ilość</th>
            <th>Numer produkcji</th>
            <th>Lokalizacja docelowa</th>
            <th>Miejsce dostawy</th>
            <th>Status</th>
            <th>Przypisanie</th>
            <th>Termin dostarczenia</th>
            <th>Realna dostawa</th>
            <th>Postęp</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($zamowienia as $z): ?>
            <tr>
                <td><?= htmlspecialchars($z['numer_zamowienia']) ?></td>
                <td><?= htmlspecialchars($z['komponent']) ?></td>
                <td><?= (int)$z['ilosc'] ?></td>
                <td><?= htmlspecialchars($z['numer_produkcji']) ?></td>
                <td><?= htmlspecialchars($z['lokalizacja']) ?></td>
                <td><?= htmlspecialchars($z['miejsce_dostawy']) ?></td>
                <td><?= htmlspecialchars($z['status']) ?></td>
                <td><?= htmlspecialchars($z['przypisanie']) ?></td>
                <td><?= htmlspecialchars($z['termin_dostarczenia']) ?></td>
                <td><?= $z['realna_dostawa'] ?? "- Brak -" ?></td>
                <td>
                  <div class="progress-bar-container">
                    <div class="progress-bar-fill" style="width:<?= $z['postep'] ?>%;"></div>
                    <div class="progress-bar-text"><?= $z['postep'] ?>%</div>
                  </div>
                </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </main>
</body>
</html>