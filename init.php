<?php
require_once("functions.php");

$connection = mysqli_connect("localhost", "root", "1718","done");
mysqli_set_charset($connection, "utf8");
$current_user = 2;
$show_complete_tasks = rand(0, 1);

if (!$connection) {
    error_template ($connection);
}