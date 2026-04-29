<?php
requireAdmin();

include __DIR__ . '/../../header.php';
?>

    <main class="container mt-4">
        <h2>Administracija</h2>

        <p class="mt-3">
            Pozdravljeni
            <strong>
                <?php echo isset($_SESSION['admin_username']) ? htmlspecialchars($_SESSION['admin_username']) : 'administrator'; ?>
            </strong>.
        </p>

        <div class="row mt-4 g-3">
            <div class="col-md-6">
                <a href="index.php?modul=book&action=add" class="btn btn-primary w-100">
                    Dodaj knjigo
                </a>
            </div>

            <div class="col-md-6">
                <a href="index.php?modul=book&action=list" class="btn btn-outline-primary w-100">
                    Prikaži vse knjige
                </a>
            </div>

            <div class="col-md-6">
                <a href="index.php?modul=category&action=add" class="btn btn-success w-100">
                    Dodaj kategorijo
                </a>
            </div>

            <div class="col-md-6">
                <a href="index.php?modul=category&action=list" class="btn btn-outline-success w-100">
                    Prikaži vse kategorije
                </a>
            </div>
        </div>

        <form method="POST" action="index.php?modul=auth&action=logout" class="mt-4">
            <button type="submit" class="btn btn-outline-danger">Odjava</button>
        </form>
    </main>

<?php include __DIR__ . '/../../footer.php'; ?>