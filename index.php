<?php
require_once("functions.php");

$connection = mysqli_connect("localhost", "root", "1718","done");
mysqli_set_charset($connection, "utf8");
$current_user = 1;
$show_complete_tasks = rand(0, 1);

if (!$connection) {
    error_template ($connection);
}

$projects = projects_sql($current_user, $connection);
$tasks = tasks_sql($current_user, $connection);

$page_content = include_template("index.php", [
    "tasks" => $tasks,
    "show_complete_tasks" => $show_complete_tasks,]);
$layout_content = include_template("layout.php", [
    "page_content" => $page_content,
    "projects" => $projects,
    "title" => "Дела в порядке",
    "tasks" => $tasks,
]);
print($layout_content);




