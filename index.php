<?php
require_once("functions.php");
require_once("init.php");
require_once("db_data.php");

$page_content = include_template("guest.php", [
    "title" => "Дела в порядке"
]);


print($page_content);




