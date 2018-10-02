<?php

function include_template($name, $data) {
    $name = 'templates/' . $name;
    $result = '';

    if (!file_exists($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    require_once $name;

    $result = ob_get_clean();

    return $result;
}

function count_projects($array_project, $name_project) {
    $counter = 0;
    foreach ($array_project as $key_project => $value_project) {
        if ($value_project["project_id"] === $name_project) {
            $counter++;
        }
    }
    return $counter;
}

function time_left($date) {
    date_default_timezone_set("Europe/Moscow");

    if ($date !== NULL) {

        $task_date = strtotime($date);
        $secs_to_date = $task_date - strtotime("now");
        $hours_left = floor ($secs_to_date / 3600);

        if ($hours_left <= 24) {
            return "task--important";
        }
    }
}

function tasks_sql($current_user) {
    $connection = mysqli_connect("localhost", "root", "1718","done");
    mysqli_set_charset($connection, "utf8");

    $sql = "SELECT t.*, date_format(task_deadline, '%d.%m.%Y') AS task_deadline
            FROM tasks AS t 
            WHERE user_id = $current_user";

    $result = mysqli_query($connection, $sql);

    if (!$result) {
        $error = mysqli_error($connection);
        $content = include_template("error.php", ["error" => $error]);

        print ($content);
        exit();

    } else {
        $tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    return $tasks;
}

function projects_sql($current_user) {
    $connection = mysqli_connect("localhost", "root", "1718","done");
    mysqli_set_charset($connection, "utf8");
    $sql = "SELECT p.*, COUNT(t.project_id) AS cnt 
            FROM projects AS p 
            LEFT JOIN tasks AS t ON t.project_id = p.id 
            WHERE p.user_id = $current_user
            GROUP BY p.id";

    $result = mysqli_query($connection, $sql);

    if (!$result) {
        $error = mysqli_error($connection);
        $content = include_template("error.php", ["error" => $error]);

        print ($content);
        exit();

    } else {
        $projects = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    return $projects;
}
