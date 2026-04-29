# PHP knjigarna

Projektna naloga pri predmetu Spletno programiranje 2.

Gre za spletno knjigarno, izdelano v PHP in MySQL. Aplikacija omogoča pregled knjig, prikaz posamezne knjige, iskanje, košarico, zaključek nakupa ter administracijo za upravljanje knjig in kategorij.

## Funkcionalnosti

- prikaz vseh knjig
- prikaz posamezne knjige
- filtriranje po kategorijah
- iskanje knjig
- košarica
- checkout
- pošiljanje potrditvenega e-maila
- administracija za knjige in kategorije
- REST API

## Uporabljene tehnologije

- PHP
- MySQL
- HTML/CSS
- Bootstrap
- PHPMailer
- Composer
- XAMPP

## REST API

Projekt vsebuje REST API z naslednjimi endpointi:

- `api/books.php` – CRUD za knjige
- `api/categories.php` – CRUD za kategorije
- `api/search.php?q=...` – iskanje knjig

Primeri:

- `GET /api/books.php`
- `GET /api/books.php?id=1`
- `POST /api/books.php`
- `PUT /api/books.php?id=1`
- `DELETE /api/books.php?id=1`

## Zagon projekta

Projekt je bil razvit v okolju XAMPP.

1. Projekt postavi v mapo `htdocs`
2. Zaženi Apache in MySQL
3. Ustvari bazo v phpMyAdmin
4. Nastavi povezavo v `db.php`
5. Namesti Composer odvisnosti:
   ```bash
   composer install