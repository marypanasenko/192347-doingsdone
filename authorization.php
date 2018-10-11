<?php
require_once("functions.php");
require_once("init.php");
require_once("db_data.php");

$errors = [];
$tpl_data = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $authorization = $_POST["auth"];

    $required = ['email', 'password'];

    foreach ($required as $field) {
        if (empty($authorization[$field])) {
            $errors[$field] = "«Заполните это поле»";
        }
    }

    $filter_email = filter_var($authorization["email"], FILTER_VALIDATE_EMAIL);

    if (!$filter_email) {
        $errors["filter-email"] = 1;
    }

    $user = session($connection,  $authorization);

    if (!count($errors) and $user) {
        if (password_verify($authorization["password"], $user["user_pass"])) {
            $_SESSION["user"] = $user;
        } else {
            $errors["password"] = "Неверный пароль";
        }


    } else {
        $errors["email"] = "Такой пользователь не найден";
    }

    if (count($errors)) {
        $tpl_data["values"] = $authorization;
        $tpl_data["errors"] = $errors;

    } else {
        header("Location: ../index.php");
        exit();
    }

} else {
    if (isset($_SESSION["user"])) {
        $page_content = include_template("index.php", [
            "tasks" => $tasks,
            "show_complete_tasks" => $show_complete_tasks,]);
    }
}

$page_content = include_template("form-authorization.php", $tpl_data);
$container_with_sidebar = "container--with-sidebar";

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

]);

print($layout_content);
