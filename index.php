<?php
require_once("functions.php");
require_once("init.php");
require_once("db_data.php");

$page_content = include_template("index.php", [
    "tasks" => $tasks,
    "show_complete_tasks" => $show_complete_tasks,]);

$layout_content = include_template("layout.php", [
    "page_content" => $page_content,
    "projects" => $projects,
    "title" => "Дела в порядке",
]);

print($layout_content);




