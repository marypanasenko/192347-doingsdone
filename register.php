<?php
require_once("functions.php");
require_once("init.php");
require_once("db_data.php");


$errors = [];

$tpl_data = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $register = $_POST["signup"];

    $required = ["email", "password", "name"];

    foreach ($required as $field) {
        if (empty($register[$field])) {
            $errors[$field] = 1;
        }
    }

    $result_check = email_check($connection, $register);

    if (mysqli_num_rows($result_check) > 0) {
        $errors["email_duplicate"] = 1;
    }

    $filter_email = filter_var($register["email"], FILTER_VALIDATE_EMAIL);

    if (!$filter_email) {
        $errors["filter-email"] = 1;
    }

    if (count($errors)) {
        $tpl_data['values'] = $register;
        $tpl_data['errors'] = $errors;
        $page_content = include_template("form-register.php", $tpl_data);
    } else {

        $new_user = registration($register, $connection);

        if ($new_user) {
            header("Location: authorization.php");
        } else {
            $error = mysqli_error($connection);
            $content = include_template("error.php", ["error" => $error]);

            print ($content);
            exit();
        }

    }

}

$page_content = include_template("form-register.php",[]);

$layout_content = include_template("layout.php", [
    "page_content" => $page_content,
    "projects" => $projects,
    "title" => "Дела в порядке",

]);

print($layout_content);
