<?php
requireAdmin();

$message = '';
$action = isset($action) ? $action : 'list';

/*
    ADD kategorija
*/
if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = isset($_POST['title']) ? $_POST['title'] : '';
    $result = createCategory($conn, $title);

    $class = $result['success'] ? 'success' : 'danger';
    $message = '<div class="alert alert-' . $class . '">' . htmlspecialchars($result['message']) . '</div>';
}

/*
    EDIT kategorija
*/
if ($action === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $title = isset($_POST['title']) ? $_POST['title'] : '';

    $result = updateCategory($conn, $id, $title);

    $class = $result['success'] ? 'success' : 'danger';
    $message = '<div class="alert alert-' . $class . '">' . htmlspecialchars($result['message']) . '</div>';
}

/*
    DELETE kategorija
*/
if ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    $result = deleteCategory($conn, $id);

    $class = $result['success'] ? 'success' : 'danger';
    $message = '<div class="alert alert-' . $class . '">' . htmlspecialchars($result['message']) . '</div>';

    $action = 'list';
}

include __DIR__ . '/../../header.php';
?>

    <main class="container mt-4">
        <h2>Upravljanje kategorij</h2>

        <p>
            <a href="index.php?modul=dashboard" class="btn btn-secondary btn-sm">Nazaj na nadzorno ploščo</a>
            <a href="index.php?modul=category&action=add" class="btn btn-success btn-sm">Dodaj kategorijo</a>
            <a href="index.php?modul=category&action=list" class="btn btn-outline-success btn-sm">Seznam kategorij</a>
        </p>

        <?php echo $message; ?>

        <?php if ($action === 'add'): ?>

            <form method="POST" action="index.php?modul=category&action=add" class="mt-4">
                <div class="mb-3">
                    <label for="title" class="form-label">Ime kategorije</label>
                    <input type="text" name="title" id="title" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary">Dodaj kategorijo</button>
            </form>

        <?php elseif ($action === 'edit'): ?>

            <?php
            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            $category = getCategoryById($conn, $id);
            ?>

            <?php if (!$category): ?>
                <div class="alert alert-danger mt-4">Kategorija ne obstaja.</div>
            <?php else: ?>
                <form method="POST" action="index.php?modul=category&action=edit&id=<?php echo $id; ?>" class="mt-4">
                    <input type="hidden" name="id" value="<?php echo $category['BookCategoryID']; ?>">

                    <div class="mb-3">
                        <label for="title" class="form-label">Ime kategorije</label>
                        <input type="text" name="title" id="title" class="form-control" required
                               value="<?php echo htmlspecialchars($category['Title']); ?>">
                    </div>

                    <button type="submit" class="btn btn-primary">Shrani spremembe</button>
                </form>
            <?php endif; ?>

        <?php else: ?>

            <?php $categories = getAllCategories($conn); ?>

            <?php if (empty($categories)): ?>
                <div class="alert alert-info mt-4">Ni shranjenih kategorij.</div>
            <?php else: ?>
                <div class="table-responsive mt-4">
                    <table class="table table-bordered table-striped align-middle">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Naslov</th>
                            <th style="width: 220px;">Dejanja</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td><?php echo $category['BookCategoryID']; ?></td>
                                <td><?php echo htmlspecialchars($category['Title']); ?></td>
                                <td>
                                    <a href="index.php?modul=category&action=edit&id=<?php echo $category['BookCategoryID']; ?>"
                                       class="btn btn-sm btn-warning">
                                        Uredi
                                    </a>

                                    <form method="POST"
                                          action="index.php?modul=category&action=delete"
                                          class="d-inline"
                                          onsubmit="return confirm('Ali res želiš izbrisati to kategorijo?');">
                                        <input type="hidden" name="id" value="<?php echo $category['BookCategoryID']; ?>">
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