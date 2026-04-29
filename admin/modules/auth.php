<?php

$error = '';

if ($action === 'logout') {
    logoutAdmin();
    header('Location: index.php?modul=auth&action=login');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (loginAdmin($username, $password)) {
        header('Location: index.php?modul=dashboard');
        exit;
    } else {
        $error = 'Napačno uporabniško ime ali geslo.';
    }
}

include __DIR__ . '/../../header.php';
?>

<main class="container mt-4">
    <h2>Prijava v administracijo</h2>

    <?php if ($error !== ''): ?>
        <div class="alert alert-danger mt-3">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="index.php?modul=auth&action=login" class="mt-4">
        <div class="mb-3">
            <label for="username" class="form-label">Uporabniško ime</label>
            <input type="text" name="username" id="username" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Geslo</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Prijava</button>
    </form>
</main>

<?php include __DIR__ . '/../../footer.php'; ?>
