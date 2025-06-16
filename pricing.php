<?php
session_start();
$message = ""; // <- tu dodaj, przed całą logiką

if (!isset($_SESSION['user'])) {
  header("Location: login.html");
  exit;
}

if ($_SESSION['dzial'] !== 'biuro') {
  header("Location: dashboard.php");
  exit;
}
try {
    $pdo = new PDO("pgsql:host=db;port=5432;dbname=moja_baza", "postgres", "postgres");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (isset($_POST['update_id'])) {
            $updateStmt = $pdo->prepare("UPDATE komponenty SET
                waga = :waga,
                dlugosc = :dlugosc,
                szerokosc = :szerokosc,
                wysokosc = :wysokosc,
                cena = :cena,
                waluta = :waluta,
                czas_produkcji = :czas_produkcji
                WHERE id = :id");

            $updateStmt->execute([
                ':waga' => $_POST['waga'],
                ':dlugosc' => $_POST['dlugosc'],
                ':szerokosc' => $_POST['szerokosc'],
                ':wysokosc' => $_POST['wysokosc'],
                ':cena' => $_POST['cena'],
                ':waluta' => $_POST['waluta'],
                ':czas_produkcji' => $_POST['czas_produkcji'],
                ':id' => $_POST['update_id']
            ]);

            $message = "✅ Zmiany zostały zapisane.";
        } elseif (isset($_POST['nazwa'])) {
            $generatedId = generateUniqueID();
            $stmt = $pdo->prepare("INSERT INTO komponenty (
                id, nazwa, kategoria, technologia, waga, dlugosc, szerokosc, wysokosc,
                cena, waluta, czas_produkcji
            ) VALUES (
                :id, :nazwa, :kategoria, :technologia, :waga, :dlugosc, :szerokosc, :wysokosc,
                :cena, :waluta, :czas_produkcji
            )");

            $stmt->execute([
                ':id' => $generatedId,
                ':nazwa' => $_POST['nazwa'],
                ':kategoria' => $_POST['kategoria'],
                ':technologia' => $_POST['technologia'],
                ':waga' => $_POST['waga'],
                ':dlugosc' => $_POST['dlugosc'],
                ':szerokosc' => $_POST['szerokosc'],
                ':wysokosc' => $_POST['wysokosc'],
                ':cena' => $_POST['cena'],
                ':waluta' => $_POST['waluta'],
                ':czas_produkcji' => $_POST['czas_produkcji']
            ]);

            $message = "✅ Komponent został dodany. ID: <strong>{$generatedId}</strong>";
        }
    }

    $components = $pdo->query("SELECT * FROM komponenty WHERE odwolana = false ORDER BY data_oferty DESC")->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $message = "❌ Błąd: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Cennik komponentów</title>
  <link rel="stylesheet" href="/public/styles/pricing.css" />
  <link rel="stylesheet" href="/public/styles/navbar.css"/>
  <script src="https://kit.fontawesome.com/8fd9367667.js" crossorigin="anonymous"></script>
</head>
<body>
  <header class="navbar">
    <div class="logo">
      <img src="/public/images/logo.png" alt="Logo">
    </div>
    <nav class="nav-wrapper">
      <button class="menu-toggle" id="menu-toggle">
        <i class="fas fa-bars"></i>
      </button>
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

  <?php if ($message): ?>
    <p style="text-align:center;"><strong><?php echo $message; ?></strong></p>
  <?php endif; ?>

  <section class="form-section" style="padding: 20px;">
    <h2>DODAJ NOWY KOMPONENT</h2>
    <form method="post">
      <input type="text" name="nazwa" placeholder="Nazwa komponentu" required>
      <input type="text" name="kategoria" placeholder="Kategoria">
      <input type="text" name="technologia" placeholder="Technologia">
      <input type="number" step="0.01" name="waga" placeholder="Waga (kg)">
      <input type="number" name="dlugosc" placeholder="Długość">
      <input type="number" name="szerokosc" placeholder="Szerokość">
      <input type="number" name="wysokosc" placeholder="Wysokość">
      <input type="number" step="0.01" name="cena" placeholder="Cena">
      <select name="waluta">
        <option value="PLN">PLN</option>
        <option value="EUR">EUR</option>
      </select>
      <input type="number" name="czas_produkcji" placeholder="Czas produkcji (dni)">
      <button type="submit">Dodaj komponent</button>
    </form>
  </section>

  
<section class="filters-toggle">
  <div class="filters-bar" id="filter-toggle" style="cursor:pointer; text-align:center; margin:20px 0;">
    <span><i class="fas fa-chevron-down"></i> Filtruj komponenty <i class="fas fa-chevron-down"></i></span>
  </div>

  <div class="filters-form" id="filters-panel" style="display:none; text-align:center; margin-bottom:20px;">
    <input type="text" id="filter-nazwa" placeholder="Szukaj po nazwie" />
    <input type="text" id="filter-kategoria" placeholder="Szukaj po kategorii" />
    <button onclick="applyFilters()">Szukaj</button>
    <button onclick="clearFilters()">Wyczyść</button>
  </div>
</section>

<script>
  const toggle = document.getElementById("filter-toggle");
  const panel = document.getElementById("filters-panel");
  toggle.addEventListener("click", () => {
    panel.style.display = panel.style.display === "none" ? "block" : "none";
  });

  function applyFilters() {
    const nazwa = document.getElementById("filter-nazwa").value.toLowerCase();
    const kategoria = document.getElementById("filter-kategoria").value.toLowerCase();
    const rows = document.querySelectorAll("tbody tr");
    rows.forEach(row => {
      const textNazwa = row.cells[1].textContent.toLowerCase();
      const textKategoria = row.cells[2].textContent.toLowerCase();
      const show = textNazwa.includes(nazwa) && textKategoria.includes(kategoria);
      row.style.display = show ? "" : "none";
    });
  }

  function clearFilters() {
    document.getElementById("filter-nazwa").value = "";
    document.getElementById("filter-kategoria").value = "";
    applyFilters();
  }
</script>

<main class="container">
    <h2 class="section-title">ZŁOŻONE OFERTY NA KOMPONENTY</h2>
    <div class="table-wrapper">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Nazwa komponentu</th>
            <th>Kategoria</th>
            <th>Technologia</th>
            <th>Waga (kg)</th>
            <th>Długość</th>
            <th>Szerokość</th>
            <th>Wysokość</th>
            <th>Cena</th>
            <th>Waluta</th>
            <th>Czas prod. (dni)</th>
            <th>Złożona</th>
            <th>Akcja</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($components as $row): ?>
            <tr>
              <form method="post">
              <td><?= htmlspecialchars($row['id']) ?><input type="hidden" name="update_id" value="<?= $row['id'] ?>"></td>
              <td><?= htmlspecialchars($row['nazwa']) ?></td>
              <td><?= htmlspecialchars($row['kategoria']) ?></td>
              <td><?= htmlspecialchars($row['technologia']) ?></td>
              <td><input type="number" step="0.01" name="waga" value="<?= $row['waga'] ?>"></td>
              <td><input type="number" name="dlugosc" value="<?= $row['dlugosc'] ?>"></td>
              <td><input type="number" name="szerokosc" value="<?= $row['szerokosc'] ?>"></td>
              <td><input type="number" name="wysokosc" value="<?= $row['wysokosc'] ?>"></td>
              <td><input type="number" step="0.01" name="cena" value="<?= $row['cena'] ?>"></td>
              <td>
                <select name="waluta">
                  <option value="PLN" <?= $row['waluta'] === 'PLN' ? 'selected' : '' ?>>PLN</option>
                  <option value="EUR" <?= $row['waluta'] === 'EUR' ? 'selected' : '' ?>>EUR</option>
                </select>
              </td>
              <td><input type="number" name="czas_produkcji" value="<?= $row['czas_produkcji'] ?>"></td>
              <td><?= htmlspecialchars($row['data_oferty']) ?></td>
              <td><button class="cancel-button">Zapisz</button></td>
              </form>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </main>

  <script>
    const menuToggle = document.getElementById('menu-toggle');
    const navLinks = document.getElementById('nav-links');
    menuToggle.addEventListener('click', () => {
      navLinks.classList.toggle('show');
    });
  </script>
</body>
</html>
