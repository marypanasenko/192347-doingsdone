<?php
require_once("functions.php");
require_once("init.php");
require_once("db_data.php");

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tasks = $_POST["tasks"];

    $required = ["task_name"];

    foreach ($tasks as $field) {
        if (empty($tasks[$field])) {
            $errors[$field] = 1;
        }
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

        $page_content = include_template("form-task.php", [ "errors" => $errors,]);

    } else {
        $result_post_task = post_task($tasks, $connection, $uploded_date, $uploded_file, $current_user);
    }

    var_dump($errors);

    if ($result_post_task) {
        header("Location: index.php?project_id=" . $tasks["project_id"]);
    } else {
        $content = include_template("error.php", ["error" => mysqli_error($connection)]);
        print ($content);
        exit();
    }

}


$page_content = include_template("form-task.php", ["projects" => $projects]);
$layout_content = include_template("layout.php", [
    "page_content" => $page_content,
    "projects" => $projects,
    "title" => "Дела в порядке",
    "tasks" => $tasks,
]);

print($layout_content);