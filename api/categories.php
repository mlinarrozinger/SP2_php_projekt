<?php

require_once __DIR__ . '/api_init.php';

/*
    Vrne eno kategorijo po ID-ju
*/
function getCategoryById($conn, $id)
{
    $sql = "
        SELECT BookCategoryID, Title
        FROM BookCategory
        WHERE BookCategoryID = ?
        LIMIT 1
    ";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        serverError('Napaka pri pripravi poizvedbe: ' . $conn->error);
    }

    $id = (int)$id;
    $stmt->bind_param('i', $id);

    if (!$stmt->execute()) {
        $error = $stmt->error;
        $stmt->close();
        serverError('Napaka pri izvajanju poizvedbe: ' . $error);
    }

    $stmt->bind_result($categoryId, $title);

    if ($stmt->fetch()) {
        $category = array(
            'BookCategoryID' => $categoryId,
            'Title' => $title
        );
    } else {
        $category = null;
    }

    $stmt->close();

    return $category;
}

/*
    Vrne vse kategorije
*/
function getAllCategories($conn)
{
    $sql = "
        SELECT BookCategoryID, Title
        FROM BookCategory
        ORDER BY Title ASC
    ";

    $result = $conn->query($sql);

    if (!$result) {
        serverError('Napaka pri branju kategorij: ' . $conn->error);
    }

    $categories = array();

    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }

    return $categories;
}

/*
    Preveri, ali ima kategorija povezane knjige
*/
function categoryHasBooks($conn, $id)
{
    $sql = "
        SELECT COUNT(*) AS total
        FROM Book
        WHERE BookCategoryID = ?
    ";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        serverError('Napaka pri pripravi poizvedbe: ' . $conn->error);
    }

    $id = (int)$id;
    $stmt->bind_param('i', $id);

    if (!$stmt->execute()) {
        $error = $stmt->error;
        $stmt->close();
        serverError('Napaka pri izvajanju poizvedbe: ' . $error);
    }

    $stmt->bind_result($total);
    $stmt->fetch();
    $stmt->close();

    return ((int)$total > 0);
}

$method = getRequestMethod();
$id = getRequestId();

switch ($method) {

    case 'GET':

        if ($id !== null) {
            $category = getCategoryById($conn, $id);

            if (!$category) {
                notFound('Kategorija ne obstaja.');
            }

            jsonResponse(array(
                'success' => true,
                'data' => $category
            ), 200);
        }

        $categories = getAllCategories($conn);

        jsonResponse(array(
            'success' => true,
            'count' => count($categories),
            'data' => $categories
        ), 200);
        break;


    case 'POST':

        $data = readJsonInput();
        $title = isset($data['Title']) ? trim($data['Title']) : '';

        if ($title === '') {
            badRequest('Polje Title je obvezno.');
        }

        $stmt = $conn->prepare("
            INSERT INTO BookCategory (Title)
            VALUES (?)
        ");

        if (!$stmt) {
            serverError('Napaka pri pripravi INSERT poizvedbe: ' . $conn->error);
        }

        $stmt->bind_param('s', $title);

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();
            serverError('Napaka pri dodajanju kategorije: ' . $error);
        }

        $newId = $conn->insert_id;
        $stmt->close();

        $newCategory = getCategoryById($conn, $newId);

        jsonResponse(array(
            'success' => true,
            'message' => 'Kategorija je bila uspešno dodana.',
            'data' => $newCategory
        ), 201);
        break;


    case 'PUT':

        if ($id === null) {
            badRequest('Pri PUT zahtevku moraš podati ID, npr. categories.php?id=2');
        }

        $existingCategory = getCategoryById($conn, $id);

        if (!$existingCategory) {
            notFound('Kategorija za posodobitev ne obstaja.');
        }

        $data = readJsonInput();
        $title = isset($data['Title']) ? trim($data['Title']) : $existingCategory['Title'];

        if ($title === '') {
            badRequest('Polje Title ne sme biti prazno.');
        }

        $stmt = $conn->prepare("
            UPDATE BookCategory
            SET Title = ?
            WHERE BookCategoryID = ?
        ");

        if (!$stmt) {
            serverError('Napaka pri pripravi UPDATE poizvedbe: ' . $conn->error);
        }

        $stmt->bind_param('si', $title, $id);

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();
            serverError('Napaka pri posodobitvi kategorije: ' . $error);
        }

        $stmt->close();

        $updatedCategory = getCategoryById($conn, $id);

        jsonResponse(array(
            'success' => true,
            'message' => 'Kategorija je bila uspešno posodobljena.',
            'data' => $updatedCategory
        ), 200);
        break;


    case 'DELETE':

        if ($id === null) {
            badRequest('Pri DELETE zahtevku moraš podati ID, npr. categories.php?id=2');
        }

        $existingCategory = getCategoryById($conn, $id);

        if (!$existingCategory) {
            notFound('Kategorija za brisanje ne obstaja.');
        }

        if (categoryHasBooks($conn, $id)) {
            jsonResponse(array(
                'success' => false,
                'message' => 'Kategorije ni mogoče izbrisati, ker je povezana z obstoječimi knjigami.'
            ), 409);
        }

        $stmt = $conn->prepare("
            DELETE FROM BookCategory
            WHERE BookCategoryID = ?
        ");

        if (!$stmt) {
            serverError('Napaka pri pripravi DELETE poizvedbe: ' . $conn->error);
        }

        $stmt->bind_param('i', $id);

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();
            serverError('Napaka pri brisanju kategorije: ' . $error);
        }

        $stmt->close();

        jsonResponse(array(
            'success' => true,
            'message' => 'Kategorija je bila uspešno izbrisana.'
        ), 200);
        break;


    default:
        methodNotAllowed(array('GET', 'POST', 'PUT', 'DELETE'));
}
?>