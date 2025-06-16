<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.html");
    exit;
}

try {
    $pdo = new PDO("pgsql:host=db;port=5432;dbname=moja_baza", "postgres", "postgres");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obsługa usuwania
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_id'])) {
        $stmt = $pdo->prepare("DELETE FROM zamowienia WHERE numer_zamowienia = :id");
        $stmt->execute(['id' => $_POST['delete_id']]);
    }

    // Obsługa aktualizacji
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_id']) && !isset($_POST['delete_id'])) {
        $status = $_POST['status'] ?? $_POST['hidden_status'] ?? '';
        $mapa = [
            'Potwierdzone' => 5,
            'W realizacji' => 25,
            'Gotowe' => 60,
            'Dostarczone' => 100
        ];
        $postep = $mapa[$status] ?? 0;
        $archiwum = ($status === 'Dostarczone' && $_POST['submit'] === 'Zapisz');

        $stmt = $pdo->prepare("UPDATE zamowienia 
                            SET status = :status, 
                                postep = :postep, 
                                archiwum = :archiwum 
                            WHERE numer_zamowienia = :id");

        $stmt->bindValue(':status', $status, PDO::PARAM_STR);
        $stmt->bindValue(':postep', $postep, PDO::PARAM_INT);
        $stmt->bindValue(':archiwum', $archiwum, PDO::PARAM_BOOL);
        $stmt->bindValue(':id', $_POST['update_id'], PDO::PARAM_STR);
        $stmt->execute();
    }

    $sort = $_GET['sort'] ?? 'data_dodania';
    $allowed = ['data_dodania', 'termin_dostarczenia', 'postep'];
    $sort = in_array($sort, $allowed) ? $sort : 'data_dodania';

    $zamowienia = $pdo->query("SELECT * FROM zamowienia WHERE archiwum = false ORDER BY $sort")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Błąd: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8" />
  <title>Aktualne zamówienia</title>
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

  <main class="container">
    <h2 class="section-title">AKTYWNE ZAMÓWIENIA KOMPONENTÓW</h2>

    <?php if ($_SESSION['dzial'] === 'biuro'): ?>
      <div style="text-align: right; margin-bottom: 1em;">
        <a href="add_order.php" style="background:#dc3545;color:white;padding:8px 14px;border-radius:6px;text-decoration:none;font-weight:bold;">➕ Dodaj zamówienie</a>
      </div>
    <?php endif; ?>

    <div style="text-align: right; margin-bottom: 1em;">
      Sortuj wg:
      <a href="?sort=termin_dostarczenia">📅 Termin</a> |
      <a href="?sort=postep">📈 Postęp</a>
    </div>

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
            <th>Postęp</th>
            <th>Akcja</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($zamowienia as $z): ?>
            <tr>
              <form method="post">
                <input type="hidden" name="update_id" value="<?= htmlspecialchars($z['numer_zamowienia']) ?>">
                <input type="hidden" name="hidden_status" value="<?= htmlspecialchars($z['status']) ?>">
                <td><?= htmlspecialchars($z['numer_zamowienia']) ?></td>
                <td><?= htmlspecialchars($z['komponent']) ?></td>
                <td><?= (int)$z['ilosc'] ?></td>
                <td><?= htmlspecialchars($z['numer_produkcji']) ?></td>
                <td><?= htmlspecialchars($z['lokalizacja']) ?></td>
                <td><?= htmlspecialchars($z['miejsce_dostawy']) ?></td>
                <td>
                  <select name="status">
                    <option value="Potwierdzone" <?= $z['status'] === 'Potwierdzone' ? 'selected' : '' ?>>Potwierdzone</option>
                    <option value="W realizacji" <?= $z['status'] === 'W realizacji' ? 'selected' : '' ?>>W realizacji</option>
                    <option value="Gotowe" <?= $z['status'] === 'Gotowe' ? 'selected' : '' ?>>Gotowe</option>
                    <option value="Dostarczone" <?= $z['status'] === 'Dostarczone' ? 'selected' : '' ?>>Dostarczone</option>
                  </select>
                </td>
                <td><?= htmlspecialchars($z['przypisanie']) ?></td>
                <td><?= htmlspecialchars($z['termin_dostarczenia']) ?></td>
                <td>
                  <div style="width:60px;height:20px;background-color:#ddaadd;border-radius:4px;position:relative;overflow:hidden;">
                    <div style="height:100%;width:<?= is_numeric($z['postep']) ? (int)$z['postep'] : 0 ?>%;background-color:#66cc33;position:absolute;top:0;left:0;"></div>
                    <div style="position:absolute;width:100%;text-align:center;color:white;font-size:12px;line-height:20px;">
                      <?= is_numeric($z['postep']) ? (int)$z['postep'] : 0 ?>%
                    </div>
                  </div>
                </td>
                <td>
                  <button name="submit" value="Zapisz" class="cancel-button">Zapisz</button>
                </form>
                <form method="post" onsubmit="return confirm('Czy na pewno chcesz usunąć to zamówienie?');" style="margin-top: 5px;">
                  <input type="hidden" name="delete_id" value="<?= htmlspecialchars($z['numer_zamowienia']) ?>">
                  <button type="submit" class="cancel-button" style="background:#dc3545;padding:6px 10px;font-size:16px;" title="Usuń">&#128465;</button>
                </form>
                </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </main>
</body>
</html> 