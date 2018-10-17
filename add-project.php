<?php

require_once("functions.php");
require_once("init.php");
require_once("db_data.php");

$errors = [];
$value = [];

if (!isset($_SESSION["user"])) {
    header("HTTP/1.0 404 Not Found");
    die();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $project = $_POST["project"];
    $project_name = $project["project_name"];
    $safe_project_name = mysqli_real_escape_string($connection, $project_name);

    if (empty($project_name)) {
        $errors["project_name"] = "Заполните это поле";
    } else {
        $dt_project_name = get_project_name($current_user, $connection, $safe_project_name);

        if (isset($project_name) && current($dt_project_name) == $project_name) {
            $errors["name_duplicate"] = "Такой проект уже есть";
        }
    }

    if (count($errors)) {
        $value["project_name"] = $project["project_name"];
    }

    if (!count($errors) && isset($_SESSION["user"])) {
        $result_post_project = post_project($project, $connection, $current_user);

        if ($result_post_project) {
            header("Location: index.php");

        } else {
            $error = mysqli_error($connection);
            $content = include_template("error.php", ["error" => $error]);

            print ($content);
            exit();
        }
    }
}

$page_content = include_template("form-project.php", [
    "errors" => $errors,
    "value" => $value,
]);

$content_side = include_template("content-side.php", [
    "projects" => $projects,
]);
$layout_content = include_template("layout.php", [
    "body_background" => "",
    "container_with_sidebar" => $container_with_sidebar,
    "content_side" => $content_side,
    "page_content" => $page_content,
    "projects" => $projects,
    "title" => "Дела в порядке",
    "tasks" => $tasks,
]);

print($layout_content);