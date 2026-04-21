<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

include '../header.php';
?>

<main class="container mt-4">
    <h2>Admin nadzorna plošča</h2>
    <p>Dobrodošel v administraciji spletne knjigarne.</p>

    <div class="mt-4">
        <a href="add-category.php" class="btn btn-secondary mb-2">Dodaj kategorijo</a><br>
        <a href="add-book.php" class="btn btn-primary mb-2">Dodaj knjigo</a><br>
        <a href="logout.php" class="btn btn-danger">Odjava</a>
    </div>
</main>

<?php include '../footer.php'; ?>
