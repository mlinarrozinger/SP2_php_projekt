<?php
include 'header.php';
include 'db.php';

$query = isset($_GET['q']) ? trim($_GET['q']) : '';

?>

<main class="container mt-4">

    <h2>Rezultati iskanja</h2>
    <p class="text-muted">Iskalni niz: <strong><?= htmlspecialchars($query) ?></strong></p>

    <hr>

    <?php
    if ($query === '') {
        echo "<p>Vnesite iskalni niz.</p>";
        include 'footer.php';
        exit;
    }

    // Iskanje po naslovu ali avtorju
    $stmt = $conn->prepare("
        SELECT * FROM Book 
        WHERE Name LIKE ? OR Author LIKE ?
        ORDER BY Name ASC
    ");

    $like = "%$query%";
    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();
    ?>

    <div class="row">

        <?php if ($result->num_rows === 0): ?>
            <p>Ni najdenih knjig.</p>
        <?php endif; ?>

        <?php while ($book = $result->fetch_assoc()): ?>
            <div class="col-md-3 mb-4">
                <div class="card h-100">

                    <img src="/php-trgovina/images/<?= $book['BookCover'] ?>"
                         class="book-cover"
                         alt="<?= $book['Name'] ?>">

                    <div class="card-body">
                        <h5 class="card-title"><?= $book['Name'] ?></h5>
                        <p class="text-muted"><?= $book['Author'] ?></p>

                        <a href="book.php?id=<?= $book['BookID'] ?>" class="btn btn-primary btn-sm">
                            Podrobnosti
                        </a>
                    </div>

                </div>
            </div>
        <?php endwhile; ?>

    </div>

</main>

<?php include 'footer.php'; ?>
