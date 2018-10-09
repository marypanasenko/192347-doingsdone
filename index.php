<?php
require_once("functions.php");
require_once("init.php");
require_once("db_data.php");


if (!isset($_SESSION['user']))  {
    $page_content = include_template("guest.php", []);

    $layout_content = include_template("layout.php", [
        "page_content" => $page_content,
        "title" => "Дела в порядке",
    ]);
}


print($layout_content);




