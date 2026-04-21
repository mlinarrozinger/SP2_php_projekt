<?php
include 'header.php';
include 'db.php';

// Preberi vse kategorije
$sql = "SELECT * FROM BookCategory ORDER BY Title ASC";
$result = $conn->query($sql);
?>

<main class="container">
    <h2>Kategorije knjig</h2>

    <div class="row mt-4">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="col-md-3 mb-3">
                <a href="books.php?category=<?= $row['BookCategoryID'] ?>"
                   class="btn btn-primary w-100">
                    <?= $row['Title'] ?>
                </a>
            </div>
        <?php endwhile; ?>
    </div>
</main>

<?php include 'footer.php'; ?>
