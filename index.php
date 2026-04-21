<?php
include 'header.php';
include 'db.php';

// Pridobi zadnje 4 knjige
$latestBooks = $conn->query("SELECT * FROM Book ORDER BY BookID DESC LIMIT 4");
?>

<main>

    <!-- HERO SEKCIJA -->
    <section class="bg-light py-5 mb-5">
        <div class="container text-center">
            <h1 class="display-5 fw-bold">Dobrodošli v spletni knjigarni</h1>
            <p class="lead text-muted mt-3">
                Odkrijte nove knjige, priljubljene kategorije in priporočila za branje.
            </p>

            <a href="books.php" class="btn btn-primary btn-lg mt-3">
                Prebrskaj knjige
            </a>
        </div>
    </section>

    <!-- NAJNOVEJŠE KNJIGE -->
    <section class="container mb-5">
        <h2 class="mb-4">Najnovejše knjige</h2>

        <div class="row">

            <?php if ($latestBooks->num_rows === 0): ?>
                <p>Trenutno ni dodanih knjig.</p>
            <?php endif; ?>

            <?php while ($book = $latestBooks->fetch_assoc()): ?>
                <div class="col-md-3 mb-4">
                    <div class="card h-100">

                        <img src="/php-trgovina/images/<?= $book['BookCover'] ?>"
                             class="book-cover"
                             alt="<?= $book['Name'] ?>">

                        <div class="card-body">
                            <h5 class="card-title"><?= $book['Name'] ?></h5>
                            <p class="card-text text-muted"><?= $book['Author'] ?></p>

                            <a href="book.php?id=<?= $book['BookID'] ?>"
                               class="btn btn-outline-primary btn-sm">
                                Podrobnosti
                            </a>
                        </div>

                    </div>
                </div>
            <?php endwhile; ?>

        </div>
    </section>

</main>

<?php include 'footer.php'; ?>
