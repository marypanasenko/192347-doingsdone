<?php

require_once("functions.php");

//require_once("data.php");

//подключение к БД

$connection = mysqli_connect("localhost", "root", "1718","done");

$current_user = 1;

$show_complete_tasks = rand(0, 1);

if (!$connection) {
    $error = mysqli_connect_error();
    $content = include_template("error.php", ["error" => $error]);

    print ($content);
    exit();
}

else {
    mysqli_set_charset($connection, "utf8");
    $sql = "SELECT id, project_name, user_id FROM projects where user_id = $current_user";
    $result = mysqli_query($connection, $sql);

    if (!$result) {
        $error = mysqli_error($connection);
        $content = include_template("error.php", ["error" => $error]);

        print ($content);
        exit();

    } else {
        $projects = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }


    $sql = "SELECT id, date_start, date_done, task_status, task_name, file, date_format(task_deadline, '%d.%m.%Y')  AS task_deadline, user_id, project_id FROM tasks WHERE user_id = $current_user";
    $result = mysqli_query($connection, $sql);


    if (!$result) {
        $error = mysqli_error($connection);
        $content = include_template("error.php", ["error" => $error]);

        print ($content);
        exit();

    } else {
        $tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

}


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





