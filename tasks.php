<?php
require_once("functions.php");
require_once("init.php");
require_once("db_data.php");

$get_data = [];
$tasks_search = "";
$search = $_GET["search"] ?? '';

if ($search) {
    $search_trim = trim($search);

    $result = search_sql($connection, $search_trim);
    $tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);
    if (empty($tasks)) {
        $tasks_search = "Ничего не найдено по вашему запросу";
    }
}

$tasks_switch = $_GET["tasks-switch"] ?? "";

if (isset($_GET["task_id"])) {
    $task_id = intval($_GET["task_id"]) ?? "";

    if ($task_id) {
        $res = sql_task_id($connection, $task_id, $current_user);
        header("Location: index.php");
    }
}

if (isset($_GET["show_completed"])) {
    $show_complete_tasks = intval($_GET["show_completed"]) ?? "";
}


if (isset($_GET["project_id"])) {
    $project_id = $_GET["project_id"];

}

if (isset($_GET["tasks-switch"])) {
    $tasks_switch = $_GET["tasks-switch"] ?? "";
    $add_and = isset($_GET["project_id"]) ? "AND project_id = $project_id" : null;

    switch ($tasks_switch) {
        case "today":
            $date = "AND task_deadline = CURDATE()";
            $sql = sql_filter($date, $add_and, $current_user);

            break;
        case "tomorrow":
            $date = "AND task_deadline = CURDATE() + 1";
            $sql = sql_filter($date, $add_and, $current_user);

            break;
        case "delay":
            $date = "AND task_deadline <= CURDATE() + 1";
            $sql = sql_filter($date, $add_and, $current_user);

            break;
        case "all":
            $date = "";
            $sql = sql_filter($date, $add_and, $current_user);

            break;
    }

    $result = mysqli_query($connection, $sql);

    if (!$result) {
        error_template($connection);
    } else {
        $tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}

$page_content = include_template("index.php", [
    "show_complete_tasks" => $show_complete_tasks,
    "tasks" => $tasks,
    "tasks_search" => $tasks_search,
    "project_id" => $project_id,
    "tasks_switch" => $tasks_switch,
]);


