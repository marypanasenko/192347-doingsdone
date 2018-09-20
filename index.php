<?php

require_once("functions.php");

require_once("data.php");

// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);

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