<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

include '../db.php';
include '../header.php';

// Če je obrazec oddan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = $conn->real_escape_string($_POST['name']);
    $author = $conn->real_escape_string($_POST['author']);
    $description = $conn->real_escape_string($_POST['description']);
    $content = $conn->real_escape_string($_POST['content']);
    $category = intval($_POST['category']);

    // Obdelava slike
    $imageName = null;

    if (!empty($_FILES['bookcover']['name'])) {
        $imageName = time() . "_" . basename($_FILES['bookcover']['name']);
        $target = "../images/" . $imageName;   // ← POPRAVEK
        move_uploaded_file($_FILES['bookcover']['tmp_name'], $target);
    }

    // Vstavi knjigo v bazo
    $sql = "INSERT INTO Book (Name, Author, Description, Content, BookCover, BookCategoryID)
            VALUES ('$name', '$author', '$description', '$content', '$imageName', $category)";

    if ($conn->query($sql)) {
        echo "<div class='container mt-4'><div class='alert alert-success'>Knjiga uspešno dodana!</div></div>";
    } else {
        echo "<div class='container mt-4'><div class='alert alert-danger'>Napaka: " . $conn->error . "</div></div>";
    }
}

// Preberi kategorije za dropdown
$categories = $conn->query("SELECT * FROM BookCategory ORDER BY Title ASC");
?>

<main class="container mt-4">
    <h2>Dodaj novo knjigo</h2>

    <form method="POST" enctype="multipart/form-data" class="mt-4">

        <div class="mb-3">
            <label class="form-label">Naslov knjige</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Avtor</label>
            <input type="text" name="author" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Opis (kratki opis)</label>
            <textarea name="description" class="form-control" rows="3"></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Vsebina (daljši opis)</label>
            <textarea name="content" class="form-control" rows="6"></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Kategorija</label>
            <select name="category" class="form-select" required>
                <option value="">Izberi kategorijo</option>
                <?php while ($cat = $categories->fetch_assoc()): ?>
                    <option value="<?= $cat['BookCategoryID'] ?>">
                        <?= $cat['Title'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Naslovnica knjige (slika)</label>
            <input type="file" name="bookcover" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Dodaj knjigo</button>

    </form>
</main>

<?php include '../footer.php'; ?>
