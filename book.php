<?php
include 'header.php';
include 'db.php';

// Preveri, ali je ID podan
if (!isset($_GET['id'])) {
    echo "<div class='container mt-4'><div class='alert alert-danger'>Knjiga ni bila najdena.</div></div>";
    include 'footer.php';
    exit;
}

$bookID = intval($_GET['id']);
$result = $conn->query("SELECT * FROM Book WHERE BookID = $bookID LIMIT 1");

if ($result->num_rows === 0) {
    echo "<div class='container mt-4'><div class='alert alert-danger'>Knjiga ne obstaja.</div></div>";
    include 'footer.php';
    exit;
}

$book = $result->fetch_assoc();
?>

<main class="container mt-5">

    <div class="row">

        <!-- Slika knjige -->
        <div class="col-md-4">
            <img src="/php-trgovina/images/<?= $book['BookCover'] ?>"
                 class="img-fluid rounded shadow book-cover-large"
                 alt="<?= $book['Name'] ?>">
        </div>

        <!-- Podatki o knjigi -->
        <div class="col-md-8">

            <h2><?= $book['Name'] ?></h2>
            <h5 class="text-muted mb-3">Avtor: <?= $book['Author'] ?></h5>

            <p class="lead"><?= nl2br($book['Description']) ?></p>

            <hr>

            <h4>Vsebina</h4>
            <p><?= nl2br($book['Content']) ?></p>

            <div class="mt-4">
                <a href="cart.php?add=<?= $book['BookID'] ?>" class="btn btn-success btn-lg me-2">
                    Dodaj v košarico
                </a>

                <a href="books.php" class="btn btn-outline-secondary btn-lg">
                    Nazaj na seznam knjig
                </a>
            </div>

        </div>
    </div>

</main>

<?php include 'footer.php'; ?>
