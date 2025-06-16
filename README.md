# 🧾 System zarządzania zamówieniami komponentów

Aplikacja webowa do zarządzania zamówieniami produkcyjnymi dla firmy przemysłowej. Umożliwia dodawanie zamówień komponentów, śledzenie ich statusów oraz kontrolę postępu dostawy. System rozróżnia role użytkowników (np. dział biuro) i oferuje indywidualne widoki.

## 🔧 Technologie użyte w projekcie

- PHP 8
- PostgreSQL (z Dockerem)
- HTML + CSS + JS
- AJAX (aktualizacja danych bez przeładowania strony)
- Docker + docker-compose
- Git (repozytorium lokalne)
- CSRF token, haszowanie haseł (bezpieczeństwo)

## 🧩 Funkcje aplikacji

- ✅ Logowanie z uwzględnieniem ról użytkowników (dział biuro ma dostęp do archiwum i dodawania)
- ✅ Sesja użytkownika (PHP Session)
- ✅ Dodawanie nowych zamówień przez dział biuro
- ✅ Filtrowanie zamówień: po terminie, postępie, statusie
- ✅ Zmiana statusu zamówienia (Potwierdzone, W realizacji, Gotowe, Dostarczone)
- ✅ Automatyczne archiwizowanie zamówienia po statusie „Dostarczone”
- ✅ Eksport bazy danych do pliku `.sql`
- ✅ Obsługa postępu graficznie (procentowy pasek)
- ✅ Responsywny interfejs CSS
- ✅ Transakcje w SQL (`PDO::beginTransaction`)

## 🗂️ Struktura projektu

```
├── docker/                 # konfiguracja dockera
├── public/                 # obrazy, style
├── views/                  # pliki PHP
├── dump.sql                # eksport bazy danych PostgreSQL
├── diagram.png             # diagram ERD
├── README.md
```

## 🧠 Rola użytkownika

Użytkownicy są podzieleni na działy. Przykład:

- **dział = "biuro"** – widzi wszystkie przyciski, może dodawać i archiwizować zamówienia
- **inni użytkownicy** – widzą tylko przypisane im zamówienia


## 🖼️ Zrzut ekranu aplikacji

| Widok               | Zrzut ekranu                         |
|---------------------|--------------------------------------|
| 📝 Strona zamówień   | ![](/public/screenshots/orders.png)          |
| ➕ Formularz dodawania | ![](/public/screenshots/add_order.png)       |
| 🔒 Logowanie         | ![](/public/screenshots/login.png)           |
| 📁 Archiwum (biuro)  | ![](/public/screenshots/archive.png)         |

## 🛠️ Uruchamianie projektu (Docker)

1. Upewnij się, że masz zainstalowany Docker + Docker Compose
2. Uruchom kontenery:
```bash
docker-compose up -d
```
3. Wejdź w przeglądarce pod `http://localhost:8080`

## 💾 Baza danych

- Używana baza: PostgreSQL
- Nazwa bazy: `moja_baza`
- Eksport bazy znajduje się w pliku: `dump.sql`
- Możesz odtworzyć bazę:

```bash
docker exec -i lab01-konfiguracja-db-1 psql -U postgres -d moja_baza < dump.sql
```

## 🧱 Diagram ERD

> Umieść plik jako `diagram.png` lub `diagram.dbml`

📎 Przedstawia tabele `users`, `zamowienia`, relacje i klucze obce  
Dodaj obrazek w folderze projektu jako `diagram.png`.

