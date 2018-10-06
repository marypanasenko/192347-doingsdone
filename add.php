<?php
require_once("functions.php");
require_once("init.php");
require_once("db_data.php");

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tasks = $_POST["tasks"];

    $required = ['task_name'];

    foreach ($required as $key) {
        if (empty($_POST[$key])) {
            $errors[$key] = 1;
        }
    }

    if (isset($_FILES['file']['name'])) {
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

    if (isset($_POST["tasks"]["task_deadline"])) {
        $uploded_date = $tasks["task_deadline"];
    } else {
        $uploded_date = "";
    }

    $sql = "INSERT INTO tasks (task_name, task_deadline, file, project_id, user_id) VALUES (?, ?, ?, ?,". $current_user.")";

    $stmt = db_get_prepare_stmt($connection, $sql, [$tasks["task_name"], $uploded_date, $uploded_file, $tasks["project_id"]]);
    $res = mysqli_stmt_execute($stmt);


    if ($res) {
        $tasks__id = mysqli_insert_id($connection);

        header("Location: index.php?id=" . $tasks__id);
    }
    else {
        $content = include_template("error.php", ["error" => mysqli_error($connection)]);
    }

}



$page_content = include_template("form-task.php", [
    "projects" => $projects,
    'errors' => $errors,
]);
$layout_content = include_template("layout.php", [
    "page_content" => $page_content,
    "projects" => $projects,
    "title" => "Дела в порядке",
    "tasks" => $tasks,
]);

print($layout_content);