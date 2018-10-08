<?php
require_once("functions.php");
require_once("init.php");
require_once("db_data.php");



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tasks = $_POST["tasks"];

    $required = ["task_name", "project_id"];
    $errors = [];
    foreach ($required as $field => $key) {
        if (empty($tasks[$key])) {
            $errors[$key] = "Заполнить";
        }
    }

    $project_id = $tasks["project_id"];
    $dt_project_id = get_project_id($current_user, $connection, $project_id);


    if ($project_id !== current($dt_project_id)) {
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

        $page_content = include_template("form-task.php", [ "tasks" => $tasks, "errors" => $errors, "projects" => $projects]);
        $content = include_template("error.php", ["error" => mysqli_error($connection)]);

    } else {
        $result_post_task = post_task($tasks, $connection, $uploded_date, $uploded_file, $current_user);
        header("Location: index.php?project_id=" . $tasks["project_id"]);
    }

}
else {
    $page_content = include_template("form-task.php", ["projects" => $projects]);
}


$layout_content = include_template("layout.php", [
    "page_content" => $page_content,
    "projects" => $projects,
    "title" => "Дела в порядке",
    "tasks" => $tasks,
]);

print($layout_content);