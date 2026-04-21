<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <title>Spletna knjigarna</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="/php-trgovina/css/bootstrap.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="/php-trgovina/css/style.css">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">

        <!-- Logo -->
        <a class="navbar-brand" href="/php-trgovina/index.php">
            Spletna knjigarna
        </a>

        <!-- Hamburger -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- NAVIGATION -->
        <div class="collapse navbar-collapse" id="mainNavbar">

            <!-- Left menu -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                <li class="nav-item">
                    <a class="nav-link" href="/php-trgovina/books.php">Knjige</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="/php-trgovina/category.php">Kategorije</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="/php-trgovina/cart.php">Košarica</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="/php-trgovina/contact.php">Kontakt</a>
                </li>

            </ul>

            <!-- Search -->
            <form class="d-flex me-3" action="/php-trgovina/search.php" method="GET">
                <input class="form-control me-2" type="search" name="q" placeholder="Išči knjige...">
                <button class="btn btn-outline-light" type="submit">Išči</button>
            </form>

            <!-- Right menu -->
            <ul class="navbar-nav">

                <?php if (isset($_SESSION['admin'])): ?>

                    <li class="nav-item">
                        <a class="nav-link" href="/php-trgovina/admin/dashboard.php">Admin</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link text-danger" href="/php-trgovina/admin/logout.php">Odjava</a>
                    </li>

                <?php else: ?>

                    <li class="nav-item">
                        <a class="nav-link" href="/php-trgovina/admin/login.php">Prijava</a>
                    </li>

                <?php endif; ?>

            </ul>

        </div>
    </div>
</nav>
