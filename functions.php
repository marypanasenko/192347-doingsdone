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

function error_template ($connection) {
    $error = mysqli_error($connection);
    $content = include_template("error.php", ["error" => $error]);
    print ($content);
    exit();
}

function get_project_id($current_user, $connection, $project_id) {


        $sql = "SELECT p.*
        FROM projects AS p
        WHERE user_id = $current_user
        AND id = $project_id";

        $result = mysqli_query($connection, $sql);
        $project_id_result = mysqli_fetch_row($result);

        return $project_id_result;

    }


function tasks_sql($current_user, $connection, $project_id) {
    $add_and = isset($project_id) ? "AND project_id = $project_id" : null;

    $sql = "SELECT t.*, date_format(task_deadline, '%d.%m.%Y') AS task_deadline
            FROM tasks AS t 
            WHERE user_id = $current_user $add_and";

    $result = mysqli_query($connection, $sql);

    if (!$result) {
        error_template($connection);
    } else {
        $tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
    return $tasks;
}


function projects_sql($current_user, $connection) {

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

function post_task($tasks, $connection, $uploded_date, $uploded_file, $current_user) {
    $sql = "INSERT INTO tasks (task_name, task_deadline, file, project_id, user_id) VALUES (?, ?, ?, ?, ?)";

    $stmt = db_get_prepare_stmt($connection, $sql, [$tasks["task_name"], $uploded_date, $uploded_file, $tasks["project_id"], $current_user]);
    $res = mysqli_stmt_execute($stmt);

    return $res;
}
function registration($register, $connection) {
    $password_hash = password_hash($register['password'], PASSWORD_DEFAULT);

    $sql = 'INSERT INTO users (reg_date, email, user_name, user_pass, token) VALUES (NOW(), ?, ?, ?, "")';
    $stmt = db_get_prepare_stmt($connection, $sql, [$register['email'], $register["name"], $password_hash]);
    $result = mysqli_stmt_execute($stmt);

    return $result;

}

function email_check($connection, $register) {
    $email = mysqli_real_escape_string($connection, $register['email']);
    $sql = "SELECT id FROM users WHERE email = '$email'";
    $result = mysqli_query($connection, $sql);

    return $result;

}

function db_get_prepare_stmt($connection, $sql, $data = []) {
    $stmt = mysqli_prepare($connection, $sql);

    if ($data) {
        $types = '';
        $stmt_data = [];

        foreach ($data as $value) {
            $type = null;

            if (is_int($value)) {
                $type = 'i';
            }
            else if (is_string($value)) {
                $type = 's';
            }
            else if (is_double($value)) {
                $type = 'd';
            }

            if ($type) {
                $types .= $type;
                $stmt_data[] = $value;
            }
        }

        $values = array_merge([$stmt, $types], $stmt_data);

        $func = 'mysqli_stmt_bind_param';
        $func(...$values);
    }

    return $stmt;
}