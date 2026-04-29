<?php

require_once __DIR__ . '/api_init.php';

/*
    Vrne eno knjigo po ID-ju
*/
function getBookById($conn, $id)
{
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
        WHERE b.BookID = ?
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

    if ($stmt->fetch()) {
        $book = array(
            'BookID' => $bookId,
            'Name' => $name,
            'Author' => $author,
            'Description' => $description,
            'Content' => $content,
            'BookCover' => $bookCover,
            'BookCategoryID' => $bookCategoryId,
            'CategoryTitle' => $categoryTitle
        );
    } else {
        $book = null;
    }

    $stmt->close();

    return $book;
}

/*
    Vrne vse knjige
*/
function getAllBooks($conn)
{
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
        ORDER BY b.BookID DESC
    ";

    $result = $conn->query($sql);

    if (!$result) {
        serverError('Napaka pri branju knjig: ' . $conn->error);
    }

    $books = array();

    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }

    return $books;
}

$method = getRequestMethod();
$id = getRequestId();

switch ($method) {

    case 'GET':

        if ($id !== null) {
            $book = getBookById($conn, $id);

            if (!$book) {
                notFound('Knjiga ne obstaja.');
            }

            jsonResponse(array(
                'success' => true,
                'data' => $book
            ), 200);
        }

        $books = getAllBooks($conn);

        jsonResponse(array(
            'success' => true,
            'count' => count($books),
            'data' => $books
        ), 200);
        break;


    case 'POST':

        $data = readJsonInput();

        $name = isset($data['Name']) ? trim($data['Name']) : '';
        $author = isset($data['Author']) ? trim($data['Author']) : '';
        $description = isset($data['Description']) ? trim($data['Description']) : '';
        $content = isset($data['Content']) ? trim($data['Content']) : '';
        $bookCover = isset($data['BookCover']) ? $data['BookCover'] : null;
        $categoryId = isset($data['BookCategoryID']) ? (int)$data['BookCategoryID'] : 0;

        if ($name === '' || $author === '' || $categoryId <= 0) {
            badRequest('Polja Name, Author in BookCategoryID so obvezna.');
        }

        $stmt = $conn->prepare("
            INSERT INTO Book (Name, Author, Description, Content, BookCover, BookCategoryID)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        if (!$stmt) {
            serverError('Napaka pri pripravi INSERT poizvedbe: ' . $conn->error);
        }

        $stmt->bind_param('sssssi', $name, $author, $description, $content, $bookCover, $categoryId);

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();
            serverError('Napaka pri dodajanju knjige: ' . $error);
        }

        $newId = $conn->insert_id;
        $stmt->close();

        $newBook = getBookById($conn, $newId);

        jsonResponse(array(
            'success' => true,
            'message' => 'Knjiga je bila uspešno dodana.',
            'data' => $newBook
        ), 201);
        break;


    case 'PUT':

        if ($id === null) {
            badRequest('Pri PUT zahtevku moraš podati ID, npr. books.php?id=3');
        }

        $existingBook = getBookById($conn, $id);

        if (!$existingBook) {
            notFound('Knjiga za posodobitev ne obstaja.');
        }

        $data = readJsonInput();

        $name = isset($data['Name']) ? trim($data['Name']) : $existingBook['Name'];
        $author = isset($data['Author']) ? trim($data['Author']) : $existingBook['Author'];
        $description = isset($data['Description']) ? trim($data['Description']) : $existingBook['Description'];
        $content = isset($data['Content']) ? trim($data['Content']) : $existingBook['Content'];
        $bookCover = isset($data['BookCover']) ? $data['BookCover'] : $existingBook['BookCover'];
        $categoryId = isset($data['BookCategoryID']) ? (int)$data['BookCategoryID'] : (int)$existingBook['BookCategoryID'];

        if ($name === '' || $author === '' || $categoryId <= 0) {
            badRequest('Polja Name, Author in BookCategoryID morajo imeti veljavne vrednosti.');
        }

        $stmt = $conn->prepare("
            UPDATE Book
            SET Name = ?, Author = ?, Description = ?, Content = ?, BookCover = ?, BookCategoryID = ?
            WHERE BookID = ?
        ");

        if (!$stmt) {
            serverError('Napaka pri pripravi UPDATE poizvedbe: ' . $conn->error);
        }

        $stmt->bind_param('sssssii', $name, $author, $description, $content, $bookCover, $categoryId, $id);

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();
            serverError('Napaka pri posodobitvi knjige: ' . $error);
        }

        $stmt->close();

        $updatedBook = getBookById($conn, $id);

        jsonResponse(array(
            'success' => true,
            'message' => 'Knjiga je bila uspešno posodobljena.',
            'data' => $updatedBook
        ), 200);
        break;


    case 'DELETE':

        if ($id === null) {
            badRequest('Pri DELETE zahtevku moraš podati ID, npr. books.php?id=3');
        }

        $existingBook = getBookById($conn, $id);

        if (!$existingBook) {
            notFound('Knjiga za brisanje ne obstaja.');
        }

        $stmt = $conn->prepare("DELETE FROM Book WHERE BookID = ?");

        if (!$stmt) {
            serverError('Napaka pri pripravi DELETE poizvedbe: ' . $conn->error);
        }

        $stmt->bind_param('i', $id);

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();
            serverError('Napaka pri brisanju knjige: ' . $error);
        }

        $stmt->close();

        jsonResponse(array(
            'success' => true,
            'message' => 'Knjiga je bila uspešno izbrisana.'
        ), 200);
        break;


    default:
        methodNotAllowed(array('GET', 'POST', 'PUT', 'DELETE'));
}
?>