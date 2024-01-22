<?php
require_once "inc/conectDB.php";

// Get data from the form
$title = $_POST['title'];
$publishedYear = $_POST['published_year'];
$genre = $_POST['genre'];
$author = $_POST['author'];


function checkDuplicate($conn, $tableName, $columnName, $value)
{
    // Preparing an SQL query using a prepared query
    $stmt = $conn->prepare("SELECT * FROM $tableName WHERE $columnName = ?");
    $stmt->bind_param("s", $value);
    $stmt->execute();
    $result = $stmt->get_result();

    $stmt->close();

    // Check for duplicate with this value
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if ($row["name"] == $value) {
                return [true, $row["id"]];
            }
        }
    } else {
        // No duplicate found
        return [false, null];
    }
}


$genreExists = checkDuplicate($conn, "genres", "name", $genre);

$authorExists = checkDuplicate($conn, "authors", "name", $author);


if ($genreExists[0]) {

    $conn->begin_transaction();

    try {
        // add a book to the books table
        $insertBookQuery = $conn->prepare("INSERT INTO books (title, published_year) VALUES (?, ?)");
        $insertBookQuery->bind_param("si", $title, $publishedYear);
        $insertBookQuery->execute();

        // Get ID of the inserted book
        $bookId = $insertBookQuery->insert_id;
        $insertBookQuery->close();

        // add author

        if (!$authorExists[0]) {
            $insertAuthorQuery = $conn->prepare("INSERT INTO authors (`name`) VALUES (?)");
            $insertAuthorQuery->bind_param("s", $author);
            $insertAuthorQuery->execute();

            // Отримання ID вставленого автора
            $authorId = $insertAuthorQuery->insert_id;
            $insertAuthorQuery->close();
        }else {
            $authorId = $authorExists[1];
        }

        // add in table book_author
        $insertBookAuthorQuery = $conn->prepare("INSERT INTO `book_author` (`book_id`, `author_id`) VALUES (?, ?)");
        $insertBookAuthorQuery->bind_param("ii", $bookId, $authorId);
        $insertBookAuthorQuery->execute();
        $insertBookAuthorQuery->close();

        // add in table book_genre 
        $insertBookGenreQuery = $conn->prepare("INSERT INTO `book_genre` (`book_id`, `genre_id`) VALUES (?, ?)");
        $insertBookGenreQuery->bind_param("ii", $bookId, $genreExists[1]);
        $insertBookGenreQuery->execute();
        $insertBookGenreQuery->close();
      
        $conn->commit();

    } catch (mysqli_sql_exception $exception) {

        $conn->rollback();

        throw $exception;
        die ("Error, go to <a href='/' style='color: #000; text-transform: uppercase; font-weight: bold;'>back</a> page");
    }
}else {
    die ("Error, go to <a href='/' style='color: #000; text-transform: uppercase; font-weight: bold;'>back</a> page");
}


header('Location: /');
