<?php
error_reporting(E_ALL);
require_once("functions.php");

session_start();
$connection = mysqli_connect("localhost", "root", "1718", "done");
mysqli_set_charset($connection, "utf8");
$current_user = 0;
$show_complete_tasks = null;
$container_with_sidebar = "container--with-sidebar";

if (!$connection) {
    error_template($connection);
}

if (isset($_SESSION["user"])) {
    $current_user = $_SESSION["user"]["id"];
}

