<?php

/*
    Naloži platnico knjige v mapo images/
    in vrne ime datoteke
*/
function uploadBookCover($file, $targetDir)
{
    if (!isset($file) || !isset($file['name']) || $file['name'] === '') {
        return array(
            'success' => true,
            'filename' => null
        );
    }

    if (!isset($file['error']) || $file['error'] !== 0) {
        return array(
            'success' => false,
            'message' => 'Napaka pri nalaganju slike.'
        );
    }

    $allowedExtensions = array('jpg', 'jpeg', 'png', 'webp', 'gif');
    $originalName = basename($file['name']);
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    if (!in_array($extension, $allowedExtensions, true)) {
        return array(
            'success' => false,
            'message' => 'Dovoljene so samo slike JPG, JPEG, PNG, WEBP ali GIF.'
        );
    }

    if (!is_dir($targetDir)) {
        if (!mkdir($targetDir, 0777, true)) {
            return array(
                'success' => false,
                'message' => 'Mape za slike ni bilo mogoče ustvariti.'
            );
        }
    }

    $safeName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);
    $imageName = time() . '_' . $safeName;
    $targetPath = rtrim($targetDir, '/\\') . DIRECTORY_SEPARATOR . $imageName;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        return array(
            'success' => false,
            'message' => 'Shranjevanje slike ni uspelo.'
        );
    }

    return array(
        'success' => true,
        'filename' => $imageName
    );
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
        return array();
    }

    $books = array();

    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }

    return $books;
}

/*
    Vrne eno knjigo po ID-ju
*/
function getBookById($conn, $id)
{
    $id = (int)$id;

    $stmt = $conn->prepare("
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
    ");

    if (!$stmt) {
        return null;
    }

    $stmt->bind_param('i', $id);

    if (!$stmt->execute()) {
        $stmt->close();
        return null;
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
    Doda novo knjigo
*/
function createBook($conn, $data)
{
    $name = isset($data['name']) ? trim($data['name']) : '';
    $author = isset($data['author']) ? trim($data['author']) : '';
    $description = isset($data['description']) ? trim($data['description']) : '';
    $content = isset($data['content']) ? trim($data['content']) : '';
    $category = isset($data['category']) ? (int)$data['category'] : 0;
    $bookCover = isset($data['bookcover']) ? $data['bookcover'] : null;

    if ($name === '' || $author === '' || $category <= 0) {
        return array(
            'success' => false,
            'message' => 'Naslov, avtor in kategorija so obvezni.'
        );
    }

    $stmt = $conn->prepare("
        INSERT INTO Book (Name, Author, Description, Content, BookCover, BookCategoryID)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    if (!$stmt) {
        return array(
            'success' => false,
            'message' => 'Napaka pri pripravi poizvedbe: ' . $conn->error
        );
    }

    $stmt->bind_param('sssssi', $name, $author, $description, $content, $bookCover, $category);

    if (!$stmt->execute()) {
        $error = $stmt->error;
        $stmt->close();

        return array(
            'success' => false,
            'message' => 'Napaka pri dodajanju knjige: ' . $error
        );
    }

    $newId = $conn->insert_id;
    $stmt->close();

    return array(
        'success' => true,
        'message' => 'Knjiga uspešno dodana!',
        'id' => $newId
    );
}

/*
    Posodobi knjigo
*/
function updateBook($conn, $id, $data)
{
    $id = (int)$id;
    $existingBook = getBookById($conn, $id);

    if (!$existingBook) {
        return array(
            'success' => false,
            'message' => 'Knjiga ne obstaja.'
        );
    }

    $name = isset($data['name']) ? trim($data['name']) : $existingBook['Name'];
    $author = isset($data['author']) ? trim($data['author']) : $existingBook['Author'];
    $description = isset($data['description']) ? trim($data['description']) : $existingBook['Description'];
    $content = isset($data['content']) ? trim($data['content']) : $existingBook['Content'];
    $category = isset($data['category']) ? (int)$data['category'] : (int)$existingBook['BookCategoryID'];
    $bookCover = isset($data['bookcover']) ? $data['bookcover'] : $existingBook['BookCover'];

    if ($name === '' || $author === '' || $category <= 0) {
        return array(
            'success' => false,
            'message' => 'Naslov, avtor in kategorija morajo imeti veljavne vrednosti.'
        );
    }

    $stmt = $conn->prepare("
        UPDATE Book
        SET Name = ?, Author = ?, Description = ?, Content = ?, BookCover = ?, BookCategoryID = ?
        WHERE BookID = ?
    ");

    if (!$stmt) {
        return array(
            'success' => false,
            'message' => 'Napaka pri pripravi poizvedbe: ' . $conn->error
        );
    }

    $stmt->bind_param('sssssii', $name, $author, $description, $content, $bookCover, $category, $id);

    if (!$stmt->execute()) {
        $error = $stmt->error;
        $stmt->close();

        return array(
            'success' => false,
            'message' => 'Napaka pri posodobitvi knjige: ' . $error
        );
    }

    $stmt->close();

    return array(
        'success' => true,
        'message' => 'Knjiga uspešno posodobljena.'
    );
}

/*
    Izbriše knjigo
*/
function deleteBook($conn, $id)
{
    $id = (int)$id;

    if ($id <= 0) {
        return array(
            'success' => false,
            'message' => 'Neveljaven ID knjige.'
        );
    }

    $stmt = $conn->prepare("
        DELETE FROM Book
        WHERE BookID = ?
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
            'message' => 'Napaka pri brisanju knjige: ' . $error
        );
    }

    $stmt->close();

    return array(
        'success' => true,
        'message' => 'Knjiga uspešno izbrisana.'
    );
}
?>