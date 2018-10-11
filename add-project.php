<?php

require_once("functions.php");
require_once("init.php");
require_once("db_data.php");

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $project = $_POST["project"];



    $project_name = $project["project_name"];


    if (empty($project_name)) {
        $errors["project_name"] = "«Заполните это поле»";
    } else {
        $dt_project_name = get_project_name($current_user, $connection, $project_name);
            if (current($dt_project_name) == $project_name) {
                $errors["name_duplicate"] = "Такой проект уже есть";
            }
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