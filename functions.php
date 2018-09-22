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
        if ($value_project["category"] === $name_project) {
            $counter++;
        }
    }
    return $counter;
}

function time_left($date) {
    date_default_timezone_set("Europe/Moscow");

    if ($date !== "Нет") {

        $task_date = strtotime($date);
        $secs_to_date = $task_date - strtotime("now");
        $hours_left = floor ($secs_to_date / 3600);

        if ($hours_left <= 24) {
            return "task--important";
        }
    }
}


