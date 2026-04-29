<?php
requireAdmin();

$message = '';
$action = isset($action) ? $action : 'list';

/*
    ADD knjiga
*/
if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $upload = uploadBookCover($_FILES['bookcover'], __DIR__ . '/../../images');

    if (!$upload['success']) {
        $message = '<div class="alert alert-danger">' . htmlspecialchars($upload['message']) . '</div>';
    } else {
        $data = array(
            'name' => isset($_POST['name']) ? $_POST['name'] : '',
            'author' => isset($_POST['author']) ? $_POST['author'] : '',
            'description' => isset($_POST['description']) ? $_POST['description'] : '',
            'content' => isset($_POST['content']) ? $_POST['content'] : '',
            'category' => isset($_POST['category']) ? $_POST['category'] : 0,
            'bookcover' => $upload['filename']
        );

        $result = createBook($conn, $data);

        $class = $result['success'] ? 'success' : 'danger';
        $message = '<div class="alert alert-' . $class . '">' . htmlspecialchars($result['message']) . '</div>';
    }
}

/*
    EDIT knjiga
*/
if ($action === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    $data = array(
        'name' => isset($_POST['name']) ? $_POST['name'] : '',
        'author' => isset($_POST['author']) ? $_POST['author'] : '',
        'description' => isset($_POST['description']) ? $_POST['description'] : '',
        'content' => isset($_POST['content']) ? $_POST['content'] : '',
        'category' => isset($_POST['category']) ? $_POST['category'] : 0
    );

    if (isset($_FILES['bookcover']) && isset($_FILES['bookcover']['name']) && $_FILES['bookcover']['name'] !== '') {
        $upload = uploadBookCover($_FILES['bookcover'], __DIR__ . '/../../images');

        if (!$upload['success']) {
            $message = '<div class="alert alert-danger">' . htmlspecialchars($upload['message']) . '</div>';
        } else {
            $data['bookcover'] = $upload['filename'];
        }
    }

    if ($message === '') {
        $result = updateBook($conn, $id, $data);

        $class = $result['success'] ? 'success' : 'danger';
        $message = '<div class="alert alert-' . $class . '">' . htmlspecialchars($result['message']) . '</div>';
    }
}

/*
    DELETE knjiga
*/
if ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    $result = deleteBook($conn, $id);

    $class = $result['success'] ? 'success' : 'danger';
    $message = '<div class="alert alert-' . $class . '">' . htmlspecialchars($result['message']) . '</div>';

    $action = 'list';
}

$categoryOptions = getAllCategories($conn);

include __DIR__ . '/../../header.php';
?>

    <main class="container mt-4">
        <h2>Upravljanje knjig</h2>

        <p>
            <a href="index.php?modul=dashboard" class="btn btn-secondary btn-sm">Nazaj na nadzorno ploščo</a>
            <a href="index.php?modul=book&action=add" class="btn btn-primary btn-sm">Dodaj knjigo</a>
            <a href="index.php?modul=book&action=list" class="btn btn-outline-primary btn-sm">Seznam knjig</a>
        </p>

        <?php echo $message; ?>

        <?php if ($action === 'add'): ?>

            <form method="POST" enctype="multipart/form-data" action="index.php?modul=book&action=add" class="mt-4">
                <div class="mb-3">
                    <label for="name" class="form-label">Naslov knjige</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="author" class="form-label">Avtor</label>
                    <input type="text" name="author" id="author" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Opis</label>
                    <textarea name="description" id="description" class="form-control" rows="3"></textarea>
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label">Vsebina</label>
                    <textarea name="content" id="content" class="form-control" rows="6"></textarea>
                </div>

                <div class="mb-3">
                    <label for="category" class="form-label">Kategorija</label>
                    <select name="category" id="category" class="form-select" required>
                        <option value="">Izberi kategorijo</option>
                        <?php foreach ($categoryOptions as $cat): ?>
                            <option value="<?php echo $cat['BookCategoryID']; ?>">
                                <?php echo htmlspecialchars($cat['Title']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="bookcover" class="form-label">Naslovnica knjige</label>
                    <input type="file" name="bookcover" id="bookcover" class="form-control">
                </div>

                <button type="submit" class="btn btn-primary">Dodaj knjigo</button>
            </form>

        <?php elseif ($action === 'edit'): ?>

            <?php
            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            $book = getBookById($conn, $id);
            ?>

            <?php if (!$book): ?>
                <div class="alert alert-danger mt-4">Knjiga ne obstaja.</div>
            <?php else: ?>
                <form method="POST" enctype="multipart/form-data" action="index.php?modul=book&action=edit&id=<?php echo $id; ?>" class="mt-4">
                    <input type="hidden" name="id" value="<?php echo $book['BookID']; ?>">

                    <div class="mb-3">
                        <label for="name" class="form-label">Naslov knjige</label>
                        <input type="text" name="name" id="name" class="form-control" required
                               value="<?php echo htmlspecialchars($book['Name']); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="author" class="form-label">Avtor</label>
                        <input type="text" name="author" id="author" class="form-control" required
                               value="<?php echo htmlspecialchars($book['Author']); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Opis</label>
                        <textarea name="description" id="description" class="form-control" rows="3"><?php echo htmlspecialchars($book['Description']); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="content" class="form-label">Vsebina</label>
                        <textarea name="content" id="content" class="form-control" rows="6"><?php echo htmlspecialchars($book['Content']); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="category" class="form-label">Kategorija</label>
                        <select name="category" id="category" class="form-select" required>
                            <option value="">Izberi kategorijo</option>
                            <?php foreach ($categoryOptions as $cat): ?>
                                <option value="<?php echo $cat['BookCategoryID']; ?>"
                                    <?php echo ((int)$cat['BookCategoryID'] === (int)$book['BookCategoryID']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['Title']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <?php if (!empty($book['BookCover'])): ?>
                        <div class="mb-3">
                            <p>Trenutna naslovnica:</p>
                            <img src="../images/<?php echo htmlspecialchars($book['BookCover']); ?>" alt="Platnica" style="max-width: 180px;">
                        </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="bookcover" class="form-label">Nova naslovnica knjige</label>
                        <input type="file" name="bookcover" id="bookcover" class="form-control">
                        <div class="form-text">Če ne izbereš nove slike, se obstoječa ohrani.</div>
                    </div>

                    <button type="submit" class="btn btn-primary">Shrani spremembe</button>
                </form>
            <?php endif; ?>

        <?php else: ?>

            <?php $books = getAllBooks($conn); ?>

            <?php if (empty($books)): ?>
                <div class="alert alert-info mt-4">Ni shranjenih knjig.</div>
            <?php else: ?>
                <div class="table-responsive mt-4">
                    <table class="table table-bordered table-striped align-middle">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Platnica</th>
                            <th>Naslov</th>
                            <th>Avtor</th>
                            <th>Kategorija</th>
                            <th style="width: 220px;">Dejanja</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($books as $book): ?>
                            <tr>
                                <td><?php echo $book['BookID']; ?></td>
                                <td>
                                    <?php if (!empty($book['BookCover'])): ?>
                                        <img src="../images/<?php echo htmlspecialchars($book['BookCover']); ?>" alt="Platnica" style="max-width: 70px;">
                                    <?php else: ?>
                                        <span class="text-muted">Ni slike</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($book['Name']); ?></td>
                                <td><?php echo htmlspecialchars($book['Author']); ?></td>
                                <td><?php echo htmlspecialchars($book['CategoryTitle']); ?></td>
                                <td>
                                    <a href="index.php?modul=book&action=edit&id=<?php echo $book['BookID']; ?>"
                                       class="btn btn-sm btn-warning">
                                        Uredi
                                    </a>

                                    <form method="POST"
                                          action="index.php?modul=book&action=delete"
                                          class="d-inline"
                                          onsubmit="return confirm('Ali res želiš izbrisati to knjigo?');">
                                        <input type="hidden" name="id" value="<?php echo $book['BookID']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">Izbriši</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

        <?php endif; ?>
    </main>

<?php include __DIR__ . '/../../footer.php'; ?>