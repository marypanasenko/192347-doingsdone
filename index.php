<?php
require_once("functions.php");
require_once("init.php");
require_once("db_data.php");


$body_background = "body-background";

if (!isset($_SESSION['user']))  {
    $page_content = include_template("guest.php", []);
    $layout_content = include_template("layout.php", [
        "body_background" => $body_background,
        "container_with_sidebar" => "",
        "content_side" => "",
        "page_content" => $page_content,
        "title" => "Дела в порядке",
    ]);
} else {
    require_once ("tasks.php");
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
}


print($layout_content);




