<?php
include 'header.php';
include 'db.php';

// Preberi kategorije
$categories = $conn->query("SELECT * FROM BookCategory ORDER BY Title ASC");

// Preveri, ali je izbrana kategorija
$filter = isset($_GET['category']) ? intval($_GET['category']) : 0;

// Preberi knjige
if ($filter > 0) {
    $books = $conn->query("SELECT * FROM Book WHERE BookCategoryID = $filter ORDER BY Name ASC");
} else {
    $books = $conn->query("SELECT * FROM Book ORDER BY Name ASC");
}
?>

<main class="container mt-4">

    <h2>Knjige</h2>

    <!-- Kategorije -->
    <div class="mb-4">
        <strong>Kategorije:</strong><br>

        <a href="books.php" class="btn btn-outline-secondary btn-sm me-2 mb-2">
            Vse
        </a>

        <?php while ($cat = $categories->fetch_assoc()): ?>
            <a href="books.php?category=<?= $cat['BookCategoryID'] ?>"
               class="btn btn-outline-primary btn-sm me-2 mb-2">
                <?= $cat['Title'] ?>
            </a>
        <?php endwhile; ?>
    </div>

    <hr>

    <!-- Izpis knjig -->
    <div class="row">

        <?php if ($books->num_rows === 0): ?>
            <p>Ni knjig v tej kategoriji.</p>
        <?php endif; ?>

        <?php while ($book = $books->fetch_assoc()): ?>
            <div class="col-md-3 mb-4">
                <div class="card h-100">

                    <img src="/php-trgovina/images/<?= $book['BookCover'] ?>"
                         class="card-img-top"
                         alt="<?= $book['Name'] ?>">

                    <div class="card-body">
                        <h5 class="card-title"><?= $book['Name'] ?></h5>
                        <p class="card-text text-muted"><?= $book['Author'] ?></p>

                        <a href="book.php?id=<?= $book['BookID'] ?>"
                           class="btn btn-primary btn-sm">
                            Podrobnosti
                        </a>
                    </div>

                </div>
            </div>
        <?php endwhile; ?>

    </div>

</main>

<?php include 'footer.php'; ?>
