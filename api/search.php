<?php

require_once __DIR__ . '/api_init.php';

$method = getRequestMethod();

if ($method !== 'GET') {
    methodNotAllowed(array('GET'));
}

$q = getSearchQuery();

if ($q === '') {
    badRequest('Iskalni parameter q je obvezen, npr. search.php?q=roman');
}

$search = '%' . $q . '%';

$sql = "
    SELECT 
        b.BookID,
        b.Name,
        b.Author,
        b.Description,
        b.Content,
        b.BookCover,
        b.BookCategoryID,
        c.Title AS CategoryTitle
    FROM Book b
    LEFT JOIN BookCategory c ON b.BookCategoryID = c.BookCategoryID
    WHERE 
        b.Name LIKE ?
        OR b.Author LIKE ?
        OR b.Description LIKE ?
        OR b.Content LIKE ?
    ORDER BY b.BookID DESC
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    serverError('Napaka pri pripravi iskalne poizvedbe: ' . $conn->error);
}

$stmt->bind_param('ssss', $search, $search, $search, $search);

if (!$stmt->execute()) {
    $error = $stmt->error;
    $stmt->close();
    serverError('Napaka pri izvajanju iskanja: ' . $error);
}

$stmt->bind_result(
    $bookId,
    $name,
    $author,
    $description,
    $content,
    $bookCover,
    $bookCategoryId,
    $categoryTitle
);

$books = array();

while ($stmt->fetch()) {
    $books[] = array(
        'BookID' => $bookId,
        'Name' => $name,
        'Author' => $author,
        'Description' => $description,
        'Content' => $content,
        'BookCover' => $bookCover,
        'BookCategoryID' => $bookCategoryId,
        'CategoryTitle' => $categoryTitle
    );
}

$stmt->close();

jsonResponse(array(
    'success' => true,
    'query' => $q,
    'count' => count($books),
    'data' => $books
), 200);
?>