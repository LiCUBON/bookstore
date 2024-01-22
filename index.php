<?php
require_once "inc/conectDB.php";

function getBooksList($conn)
{
    $sql = "SELECT books.id, books.title, books.published_year, 
    GROUP_CONCAT(DISTINCT genres.name) AS genre, 
    GROUP_CONCAT(DISTINCT authors.name) AS author 
    FROM books 
    JOIN book_genre ON books.id = book_genre.book_id 
    JOIN genres ON book_genre.genre_id = genres.id 
    JOIN book_author ON books.id = book_author.book_id 
    JOIN authors ON book_author.author_id = authors.id 
    GROUP BY books.id, books.title, books.published_year";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->get_result();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/main.min.css">
    <title>bookstore</title>
</head>

<body>
    <div class="wrap-form">
        <h2>Form add book</h2>
        <form action="addbook.php" method="post">
            <p>
                <label for="title">Title book</label>
                <input id="title" type="text" name="title" required>
            </p>
            <p>
                <label for="title">Publish</label>
                <input id="published_year" type="text" name="published_year" maxlength="4" required>
            </p>
            <p>
                <label for="genre">Genre</label>
                <select id="genre" name="genre" id="">
                    <option value="Novel">Novel</option>
                    <option value="Detective">Detective</option>
                    <option value="Fantasy">Fantasy</option>
                    <option value="Sci-fi">Sci-fi</option>
                    <option value="Horrors">Horrors</option>
                    <option value="Adventures">Adventures</option>
                    <option value="Drama">Drama</option>
                    <option value="Comedy">Comedy</option>
                    <option value="Poetry">Poetry</option>
                </select>
            </p>
            <p>
                <label for="author">Author</label>
                <input id="author" type="text" name="author" required>
            </p>
            <p><input type="submit" value="Send"></p>


        </form>
    </div>

    <table class="bookList">
        <tr>
            <th>Name</th>
            <th>Рік публікації</th>
            <th>Genre</th>
            <th>Author</th>
            <th>Delete</th>
        </tr>
        <?php
       
        
        $result = getBooksList($conn);
       
        if ($result->num_rows > 0) {
            foreach ($result as $row) {
        ?>
                <tr>
                    <td><?= $row["title"] ?></td>
                    <td><?= $row["published_year"] ?></td>
                    <td><?= $row["genre"] ?></td>
                    <th><?= $row["author"] ?></th>
                    <th><a href="delete.php?id_memeber=<?= $row["id"] ?>">Delete</a></th>
                </tr>
        <?php
            }
        }else {
            echo "<tr><td colspan='3' style=\"text-align: center\">Нажаль книг не має(</td></tr>";
        }
        ?>
    </table>
    <?php $conn->close();?>
</body>

</html>