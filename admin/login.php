<?php
session_start();

// Če je admin že prijavljen, ga preusmeri
if (isset($_SESSION['admin'])) {
    header("Location: dashboard.php");
    exit;
}

// Fiksno uporabniško ime in geslo
$correctUser = "admin";
$correctPass = "geslo123"; // spremeni po želji

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    if ($user === $correctUser && $pass === $correctPass) {
        $_SESSION['admin'] = true;
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Napačno uporabniško ime ali geslo.";
    }
}
?>

<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <title>Admin prijava</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5" style="max-width: 400px;">
    <h2>Admin prijava</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">

        <div class="mb-3">
            <label class="form-label">Uporabniško ime</label>
            <input type="text" name="username" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Geslo</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <button class="btn btn-primary w-100">Prijava</button>

    </form>
</div>

</body>
</html>
