<?php
require_once "inc/conectDB.php";

// Отримання даних з форми
$title = $_POST['title'];
$publishedYear = $_POST['published_year'];
$genre = $_POST['genre'];
$author = $_POST['author'];


function checkDuplicate($conn, $tableName, $columnName, $value)
{
    // Підготовка SQL-запиту з використанням підготовленого запиту
    $stmt = $conn->prepare("SELECT * FROM $tableName WHERE $columnName = ?");
    $stmt->bind_param("s", $value);
    $stmt->execute();
    $result = $stmt->get_result();

    $stmt->close();

    // Перевірка наявності запису з таким значенням
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if ($row["name"] == $value) {
                return [true, $row["id"]];
            }
        }
    } else {
        // Дублікат не знайдено
        return false;
    }
}

// Приклад використання для жанру
$genreExists = checkDuplicate($conn, "genres", "name", $genre);


// Приклад використання для автора
$authorExists = checkDuplicate($conn, "authors", "name", $author);


if (!$genreExists || !$authorExists) {
    die ("Error, go to <a href='/' style='color: #000; text-transform: uppercase; font-weight: bold;'>back</a> page");
}


if ($genreExists[0]) {

    $conn->begin_transaction();

    try {
        // Додавання книги в таблицю books
        $insertBookQuery = $conn->prepare("INSERT INTO books (title, published_year) VALUES (?, ?)");
        $insertBookQuery->bind_param("si", $title, $publishedYear);
        $insertBookQuery->execute();

        // Отримання ID вставленої книги
        $bookId = $insertBookQuery->insert_id;

        // Вставка автора

        if (!$authorExists[0]) {
            $insertAuthorQuery = $conn->prepare("INSERT INTO authors (`name`) VALUES (?)");
            $insertAuthorQuery->bind_param("s", $author);
            $insertAuthorQuery->execute();

            // Отримання ID вставленого автора
            $authorId = $insertAuthorQuery->insert_id;

            // Закриття підготовлених запитів
            $insertAuthorQuery->close();
        }

        // Вставка в таблицю book_author
        $insertBookAuthorQuery = $conn->prepare("INSERT INTO `book_author` (`book_id`, `author_id`) VALUES (?, ?)");
        $insertBookAuthorQuery->bind_param("ii", $bookId, $authorExists[1]);
        $insertBookAuthorQuery->execute();

        // Вставка в таблицю book_genre 
        $insertBookGenreQuery = $conn->prepare("INSERT INTO `book_genre` (`book_id`, `genre_id`) VALUES (?, ?)");
        $insertBookGenreQuery->bind_param("ii", $bookId, $genreExists[1]);
        $insertBookGenreQuery->execute();

        // Закриття підготовлених запитів
        $insertBookQuery->close();
        $insertBookAuthorQuery->close();
        $insertBookGenreQuery->close();

        $conn->commit();

        $conn->close();
    } catch (mysqli_sql_exception $exception) {

        $conn->rollback();

        throw $exception;
    }
}


header('Location: /');
