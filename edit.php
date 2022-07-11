<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP & MySQL. Редактировани и удаление.</title>
    <link rel="stylesheet" href="css/bootstrap-grid.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="learnphp.css">
</head>

<body>
    <h1><a href="https://intop24.ru/article_15_lesson_6.php" target="_blank">Редактирование данных в MySql с помощью PHP </a></h1>
    <?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "practice_01";
    $conn = mysqli_connect($servername, $username, $password, $database);
    if (!$conn) {
        die('<p style="color:red">' . mysqli_connect_errno() . ' - ' . mysqli_connect_error() . '</p>');
    }
    echo "Соединение с БД установленно" . '<br>';
    mysqli_set_charset($conn, "utf8");
    //Если переменная Name передана
    if (isset($_POST["Name"])) {
        //Если это запрос на редактирование $_POST['edit_id'], то редактируем
        if (isset($_POST['edit_id'])) {
            $sql = mysqli_query($conn, "UPDATE `products` SET `Name` = '{$_POST['Name']}',`Price` = '{$_POST['Price']}' WHERE `ID`={$_GET['edit_id']}");
        } else {
            //Иначе отправляем данные, вводя их в форму в ручную
            $sql = mysqli_query($conn, "INSERT INTO `products` (`Name`, `Price`) VALUES ('{$_POST['Name']}', '{$_POST['Price']}')");
        }

        //Если вставка прошла успешно
        if ($sql) {
            echo '<p>Успешно!</p>';
        } else {
            echo '<p>Произошла ошибка: ' . mysqli_error($conn) . '</p>';
        }
    }
    // Удаление
    if (isset($_GET['del_id'])) { //проверяем, есть ли переменная
        //удаляем строку из таблицы
        $sql = mysqli_query($conn, "DELETE FROM `products` WHERE `ID` = {$_GET['del_id']}");
        if ($sql) {
            echo "<p>Товар удален.</p>";
        } else {
            echo '<p>Произошла ошибка: ' . mysqli_error($conn) . '</p>';
        }
    }

    //Если передана переменная edit_id, то надо обновлять данные. Для начала достанем их из БД
    if (isset($_GET['edit_id'])) {
        $sql = mysqli_query($conn, "SELECT `ID`, `Name`, `Price` FROM `products` WHERE `ID`={$_GET['edit_id']}");
        $product = mysqli_fetch_array($sql);
    }
    ?>
    <article>
        <!-- с помощью формы добавляем новый товар или редактируем имеющийся: проверяем isset($_GET['edit_id'] если перешли по ссылке изменить, то в поле формы подставляются данные согласно выбранного ID -->
        <form action="" method="post">
            <table>
                <tr>
                    <td>Наименование:</td>
                    <td><input type="text" name="Name" value="<?= isset($_GET['edit_id']) ? $product['Name'] : ''; ?>"></td>
                </tr>
                <tr>
                    <td>Цена:</td>
                    <td><input type="text" name="Price" size="3" value="<?= isset($_GET['edit_id']) ? $product['Price'] : ''; ?>"> руб.</td>
                </tr>
                <tr>
                    <td colspan="2"><input type="submit" value="OK"></td>
                </tr>
            </table>
        </form>
        <div class="block">
            <table>
                <tr>
                    <td>Идентификатор</td>
                    <td>Наименование</td>
                    <td>Цена</td>
                    <td>Удаление</td>
                    <td>Редактирование</td>
                </tr>
                <?php
                $sql = mysqli_query($conn, 'SELECT `ID`, `Name`, `Price` FROM `products`');
                while ($result = mysqli_fetch_array($sql)) {
                    echo '<tr>' .
                        "<td>{$result['ID']}</td>" .
                        "<td>{$result['Name']}</td>" .
                        "<td>{$result['Price']} ₽</td>" .
                        "<td><a href='?del_id={$result['ID']}'>Удалить</a></td>" .
                        "<td><a href='?edit_id={$result['ID']}'>Изменить</a></td>" .
                        '</tr>';
                }
                ?>
            </table>
        </div>
    </article>
    <article id="deleting">
        <h3>Удаление</h3>
        <h2>Принцип работы:</h2>
        <p>Цикл формирует строку tr в которой содержиться: <br> <b style="color: darkblue"> ID | Name | Price| Удалить(del_id=$result['ID']) | Изменить(edit_id=$result['ID']) </b></p>
        <pre>
            &lt;table&gt;
            &lt;tr&gt;
            &lt;td&gt;Идентификатор&lt;/td&gt;
            &lt;td&gt;Наименование&lt;/td&gt;
            &lt;td&gt;Цена&lt;/td&gt;
            &lt;td&gt;Удаление&lt;/td&gt;
            &lt;td&gt;Редактирование&lt;/td&gt;
            &lt;/tr&gt;
            &lt;?php
            $sql = mysqli_query($conn, 'SELECT `ID`, `Name`, `Price` FROM `products`');
        </pre>
        <pre style="background: lightyellow">
        <b> В каждой сформированной строке мы имеем ссылку на удаление и редактирование. В этой ссылке есть переменные <br> <b style="color: black"> del_id и edit_id</b> куда циклом присваивается число согласно иттерации цикла.</b>
            while ($result = mysqli_fetch_array($sql)) {
            echo '&lt;tr&gt;' .
            "&lt;td&gt;{<b style="color: black">$result['ID']</b>}&lt;/td&gt;" .
            "&lt;td&gt;{$result['Name']}&lt;/td&gt;" .
            "&lt;td&gt;{$result['Price']} ₽&lt;/td&gt;" .
            "&lt;td&gt;&lt;a href='? <b style="color: black">del_id={$result['ID']}</b>'&gt;Удалить&lt;/a&gt;&lt;/td&gt;" .
            "&lt;td&gt;&lt;a href='?<b style="color: black">edit_id={$result['ID']}</b>'&gt;Изменить&lt;/a&gt;&lt;/td&gt;" .
            '&lt;/tr&gt;';
            }
            ?&gt;
            &lt;/table&gt;
        </pre>
        <pre>
                if (isset($_GET['<b style="color: black">del_id</b>'])) { //проверяем, есть ли переменная <b style="color: black">del_id'</b>. Она будет, если мы перейдём по ссылке Удалить 
                //Если эта переменная есть, удаляем строку из таблицы. Формируем запрос на удаление.
                $sql = mysqli_query($conn, "DELETE FROM `products` WHERE `ID` = {$_GET['<b style="color: black">del_id'</b>]}");
                if ($sql) {
                    echo "<p>Товар удален.</p>";
                } else {
                    echo '<p>Произошла ошибка: ' . mysqli_error($conn) . '</p>';
                }
                }
        </pre>
    </article>
    <article id="editing">
        <h3>Редактирование</h3>
        <h2>Принцип работы:</h2>
        <div class="sint">
            <ul>
                <li>мы также как и для удаления передадим переменную <b>edit_id</b></li>
                <li>достанем из БД запись с таким номером, чему равна <b>edit_id</b></li>
                <li>подставим полученные из БД данные в форму редактирования (для этого мы воспользуемся сокразениями php для вывода результатов и для выполнения условий)</li>
                <li>сохраним эти данные в БД</li>
            </ul>
        </div>
        <p>Цикл формирует строку tr в которой содержиться: <br> <b style="color: darkblue"> ID | Name | Price| Удалить(del_id=$result['ID']) | Изменить(edit_id=$result['ID']) </b></p>
        <pre>
            &lt;table&gt;
            &lt;tr&gt;
            &lt;td&gt;Идентификатор&lt;/td&gt;
            &lt;td&gt;Наименование&lt;/td&gt;
            &lt;td&gt;Цена&lt;/td&gt;
            &lt;td&gt;Удаление&lt;/td&gt;
            &lt;td&gt;Редактирование&lt;/td&gt;
            &lt;/tr&gt;
            &lt;?php
            $sql = mysqli_query($conn, 'SELECT `ID`, `Name`, `Price` FROM `products`');
        </pre>
        <pre style="background: lightyellow">
            <b> В каждой сформированной строке мы имеем ссылку на удаление и редактирование. В этой ссылке есть переменные <br> <b style="color: black"> del_id и edit_id</b> куда циклом присваивается число согласно иттерации цикла.</b>
            while ($result = mysqli_fetch_array($sql)) {
            echo '&lt;tr&gt;' .
            "&lt;td&gt;{<b style="color: black">$result['ID']</b>}&lt;/td&gt;" .
            "&lt;td&gt;{$result['Name']}&lt;/td&gt;" .
            "&lt;td&gt;{$result['Price']} ₽&lt;/td&gt;" .
            "&lt;td&gt;&lt;a href='? <b style="color: black">del_id={$result['ID']}</b>'&gt;Удалить&lt;/a&gt;&lt;/td&gt;" .
            "&lt;td&gt;&lt;a href='?<b style="color: black">edit_id={$result['ID']}</b>'&gt;Изменить&lt;/a&gt;&lt;/td&gt;" .
            '&lt;/tr&gt;';
            }
            ?&gt;
            &lt;/table&gt;
        </pre>
        <pre>
                    //Если переменная Name передана
            if (isset($_POST["Name"])) {
            //Если это запрос на редактирование $_POST['<b style="color:black">edit_id</b>'], то редактируем
            if (isset($_POST['<b style="color:black">edit_id</b>'])) {
                Формируем UPDATE sql запрос
            $sql = mysqli_query($conn, "UPDATE `products` SET `Name` = '{$_POST['Name']}',`Price` = '{$_POST['Price']}' WHERE `ID`={$_GET['edit_id']}");
            } else {
            //Иначе отправляем данные, вводя их в форму в ручную
            $sql = mysqli_query($conn, "INSERT INTO `products` (`Name`, `Price`) VALUES ('{$_POST['Name']}', '{$_POST['Price']}')");
            }
            <br />
            //Если вставка прошла успешно
            if ($sql) {
            echo '&lt;p&gt;Успешно!&lt;/p&gt;';
            } else {
            echo '&lt;p&gt;Произошла ошибка: ' . mysqli_error($conn) . '&lt;/p&gt;';
            }
            }
            <br />
            //Если передана переменная edit_id, то надо обновлять данные. Для начала достанем их из БД
            if (isset($_GET['edit_id'])) {
            $sql = mysqli_query($conn, "SELECT `ID`, `Name`, `Price` FROM `products` WHERE `ID`={$_GET['edit_id']}");
            $product = mysqli_fetch_array($sql);
        </pre>
    </article>
    <article id="var_server">
        <h3><a href="https://wm-school.ru/php/php_superglobals_server.php" target="_blank">Суперглобальная переменная $_SERVER</a></h3>
        <pre>
            echo $_SERVER['PHP_SELF'];
            echo $_SERVER['SERVER_NAME'];
            echo $_SERVER['HTTP_HOST'];
            echo $_SERVER['HTTP_REFERER'];
            echo $_SERVER['HTTP_USER_AGENT'];
            echo $_SERVER['SCRIPT_NAME'];

        </pre>
        <?php
        echo $_SERVER['PHP_SELF'];
        echo "<br>";
        echo $_SERVER['SERVER_NAME'];
        echo "<br>";
        echo $_SERVER['HTTP_HOST'];
        echo "<br>";
        echo $_SERVER['HTTP_REFERER'];
        echo "<br>";
        echo $_SERVER['HTTP_USER_AGENT'];
        echo "<br>";
        echo $_SERVER['SCRIPT_NAME'];
        ?>

    </article>

</body>

</html>