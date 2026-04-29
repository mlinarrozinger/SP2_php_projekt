<?php

/*
    Vrne vse kategorije
*/
function getAllCategories($conn)
{
    $sql = "SELECT BookCategoryID, Title FROM BookCategory ORDER BY Title ASC";
    $result = $conn->query($sql);

    if (!$result) {
        return array();
    }

    $categories = array();

    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }

    return $categories;
}

/*
    Vrne eno kategorijo po ID-ju
*/
function getCategoryById($conn, $id)
{
    $id = (int)$id;

    $stmt = $conn->prepare("
        SELECT BookCategoryID, Title
        FROM BookCategory
        WHERE BookCategoryID = ?
        LIMIT 1
    ");

    if (!$stmt) {
        return null;
    }

    $stmt->bind_param('i', $id);

    if (!$stmt->execute()) {
        $stmt->close();
        return null;
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
    Doda novo kategorijo
*/
function createCategory($conn, $title)
{
    $title = trim($title);

    if ($title === '') {
        return array(
            'success' => false,
            'message' => 'Vnesi ime kategorije.'
        );
    }

    $stmt = $conn->prepare("
        INSERT INTO BookCategory (Title)
        VALUES (?)
    ");

    if (!$stmt) {
        return array(
            'success' => false,
            'message' => 'Napaka pri pripravi poizvedbe: ' . $conn->error
        );
    }

    $stmt->bind_param('s', $title);

    if (!$stmt->execute()) {
        $error = $stmt->error;
        $stmt->close();

        return array(
            'success' => false,
            'message' => 'Napaka pri dodajanju kategorije: ' . $error
        );
    }

    $newId = $conn->insert_id;
    $stmt->close();

    return array(
        'success' => true,
        'message' => 'Kategorija uspešno dodana!',
        'id' => $newId
    );
}

/*
    Posodobi kategorijo
*/
function updateCategory($conn, $id, $title)
{
    $id = (int)$id;
    $title = trim($title);

    if ($id <= 0 || $title === '') {
        return array(
            'success' => false,
            'message' => 'Neveljavni podatki za posodobitev kategorije.'
        );
    }

    $stmt = $conn->prepare("
        UPDATE BookCategory
        SET Title = ?
        WHERE BookCategoryID = ?
    ");

    if (!$stmt) {
        return array(
            'success' => false,
            'message' => 'Napaka pri pripravi poizvedbe: ' . $conn->error
        );
    }

    $stmt->bind_param('si', $title, $id);

    if (!$stmt->execute()) {
        $error = $stmt->error;
        $stmt->close();

        return array(
            'success' => false,
            'message' => 'Napaka pri posodobitvi kategorije: ' . $error
        );
    }

    $stmt->close();

    return array(
        'success' => true,
        'message' => 'Kategorija uspešno posodobljena.'
    );
}

/*
    Preveri, ali ima kategorija povezane knjige
*/
function categoryHasBooks($conn, $id)
{
    $id = (int)$id;

    $stmt = $conn->prepare("
        SELECT COUNT(*) AS total
        FROM Book
        WHERE BookCategoryID = ?
    ");

    if (!$stmt) {
        return false;
    }

    $stmt->bind_param('i', $id);

    if (!$stmt->execute()) {
        $stmt->close();
        return false;
    }

    $stmt->bind_result($total);
    $stmt->fetch();
    $stmt->close();

    return ((int)$total > 0);
}

/*
    Izbriše kategorijo
*/
function deleteCategory($conn, $id)
{
    $id = (int)$id;

    if ($id <= 0) {
        return array(
            'success' => false,
            'message' => 'Neveljaven ID kategorije.'
        );
    }

    if (categoryHasBooks($conn, $id)) {
        return array(
            'success' => false,
            'message' => 'Kategorije ni mogoče izbrisati, ker je povezana z obstoječimi knjigami.'
        );
    }

    $stmt = $conn->prepare("
        DELETE FROM BookCategory
        WHERE BookCategoryID = ?
    ");

    if (!$stmt) {
        return array(
            'success' => false,
            'message' => 'Napaka pri pripravi poizvedbe: ' . $conn->error
        );
    }

    $stmt->bind_param('i', $id);

    if (!$stmt->execute()) {
        $error = $stmt->error;
        $stmt->close();

        return array(
            'success' => false,
            'message' => 'Napaka pri brisanju kategorije: ' . $error
        );
    }

    $stmt->close();

    return array(
        'success' => true,
        'message' => 'Kategorija uspešno izbrisana.'
    );
}
?>
