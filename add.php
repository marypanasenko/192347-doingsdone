<?php
require_once("functions.php");
require_once("init.php");
require_once("db_data.php");

$errors = [];
$value = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tasks = $_POST["tasks"];

    $required = ["task_name", "project_id"];

    foreach ($required as $field) {
        if (empty($tasks[$field])) {
            $errors[$field] = "«Заполните это поле»";
        }
    }

    $project_id = $tasks["project_id"];
    $dt_project_id = get_project_id($current_user, $connection, $project_id);

    if ($dt_project_id == null) {
        $errors["project_id"] = "Заполнить";
    }

    if ($_FILES['file']['name'] !== "") {
        $tmp_name = $_FILES['file']['tmp_name'];
        $file = $_FILES['file']['name'];

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $file_type = finfo_file($finfo, $tmp_name);

        $file_ext = pathinfo($file, PATHINFO_EXTENSION);

        $filename = uniqid();
        $uploded_file = $filename . "." . $file_ext;

        move_uploaded_file($tmp_name, 'uploads/' . $filename . "." . $file_ext);
    } else {
        $uploded_file = "";
    }

    if ($tasks["task_deadline"] !== "") {
        $uploded_date = $tasks["task_deadline"];
    } else {
        $uploded_date = "1970.01.01";
    }

    if (count($errors)) {
        $value["task_name"] = $tasks["task_name"];
    } else {
        $result_post_task = post_task($tasks, $connection, $uploded_date, $uploded_file, $current_user);

        if ($result_post_task) {
            header("Location: index.php?project_id=" . $tasks["project_id"]);
        } else {
            $error = mysqli_error($connection);
            $content = include_template("error.php", ["error" => $error]);

            print ($content);
            exit();
        }
    }
}

$page_content = include_template("form-task.php", [
    "tasks" => $tasks,
    "errors" => $errors,
    "projects" => $projects,
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