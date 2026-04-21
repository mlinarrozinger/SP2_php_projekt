<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

include '../db.php';
include '../header.php';

$message = "";

// Če je obrazec oddan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $conn->real_escape_string($_POST['title']);

    if (!empty($title)) {
        $sql = "INSERT INTO BookCategory (Title) VALUES ('$title')";
        if ($conn->query($sql)) {
            $message = "<div class='alert alert-success'>Kategorija uspešno dodana!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Napaka: " . $conn->error . "</div>";
        }
    } else {
        $message = "<div class='alert alert-warning'>Vnesi ime kategorije.</div>";
    }
}
?>

<main class="container mt-4">
    <h2>Dodaj kategorijo</h2>

    <?= $message ?>

    <form method="POST" class="mt-4">

        <div class="mb-3">
            <label class="form-label">Ime kategorije</label>
            <input type="text" name="title" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Dodaj kategorijo</button>

    </form>
</main>

<?php include '../footer.php'; ?>
