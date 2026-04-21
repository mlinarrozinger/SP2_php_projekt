<?php
session_start();
include 'header.php';
include 'db.php';

// Inicializiraj košarico, če še ne obstaja
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Dodajanje knjige v košarico
if (isset($_GET['add'])) {
    $bookID = intval($_GET['add']);

    // Če knjiga še ni v košarici → dodaj
    if (!isset($_SESSION['cart'][$bookID])) {
        $_SESSION['cart'][$bookID] = 1;
    } else {
        // Če je že v košarici → povečaj količino
        $_SESSION['cart'][$bookID]++;
    }

    header("Location: cart.php");
    exit;
}

// Odstranjevanje knjige iz košarice
if (isset($_GET['remove'])) {
    $bookID = intval($_GET['remove']);

    if (isset($_SESSION['cart'][$bookID])) {
        unset($_SESSION['cart'][$bookID]);
    }

    header("Location: cart.php");
    exit;
}
?>

<main class="container mt-4">
    <h2>Košarica</h2>
    <hr>

    <?php if (empty($_SESSION['cart'])): ?>
        <p>Košarica je prazna.</p>
    <?php else: ?>

        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Naslov</th>
                <th>Avtor</th>
                <th>Količina</th>
                <th>Odstrani</th>
            </tr>
            </thead>
            <tbody>

            <?php
            $totalItems = 0;

            foreach ($_SESSION['cart'] as $bookID => $qty):
                $sql = "SELECT * FROM Book WHERE BookID = $bookID LIMIT 1";
                $result = $conn->query($sql);
                $book = $result->fetch_assoc();

                $totalItems += $qty;
                ?>
                <tr>
                    <td><?= $book['Name'] ?></td>
                    <td><?= $book['Author'] ?></td>
                    <td><?= $qty ?></td>
                    <td>
                        <a href="cart.php?remove=<?= $bookID ?>" class="btn btn-danger btn-sm">
                            Odstrani
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>

            </tbody>
        </table>

        <h4>Skupaj knjig: <?= $totalItems ?></h4>

        <a href="checkout.php" class="btn btn-success mt-3">Nadaljuj na blagajno</a>

    <?php endif; ?>
</main>

<?php include 'footer.php'; ?>
