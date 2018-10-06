<?php
$project_id = isset($_GET["project_id"]) ? intval($_GET["project_id"]) : null;

$projects = projects_sql($current_user, $connection);
$tasks = tasks_sql($current_user, $connection, $project_id);

if (isset($_GET["project_id"])) {
    $array_result = get_project_id($current_user, $connection, $project_id);

    if ($array_result == null || !$array_result) {
        header("HTTP/1.0 404 Not Found");
        die();
    }
}