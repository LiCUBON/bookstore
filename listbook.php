<?php
require_once "inc/conectDB.php";


$sql = "SELECT books.id, books.title, books.published_year, genres.name AS genre
        FROM books
        JOIN genres ON books.genre_id = genres.id
        ORDER BY books.published_year ASC";

$result = $conn->query($sql);

if ($result->num_rows > 0) :
?>
    <table>
        <tr>
            <th>Назва</th>
            <th>Рік публікації</th>
            <th>Жанр</th>
        </tr>
        <?php
        while ($row = $result->fetch_assoc()) :
        ?>
            <tr>
                <td><?= $row["title"]  ?></td>
                <td><?= $row["published_year"] ?></td>
                <td><?= $row["genre"]  ?></td>
            </tr>
        <?php

        endwhile;
    else :
        echo "Не знайдено жодної книги.";
        ?>
    </table>
<?php
    endif;
    $conn->close();
?>


<p><a href="/bookstore">Back</a></p>