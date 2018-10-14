<?php
require_once 'vendor/autoload.php';
require_once 'init.php';
require_once 'functions.php';

$mailer_result = "";

$transport = new Swift_SmtpTransport("phpdemo.ru", 25);
$transport->setUsername("keks@phpdemo.ru");
$transport->setPassword("htmlacademy");

$mailer = new Swift_Mailer($transport);


$logger = new Swift_Plugins_Loggers_ArrayLogger();
$mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($logger));

$sql = "SELECT *, date_format(task_deadline, '%k:%i %d.%m') AS task_deadline
  FROM tasks AS t
  LEFT JOIN users AS u ON u.id = t.user_id
  WHERE task_status = 0
  AND task_deadline >= CURRENT_TIMESTAMP
  AND task_deadline <= DATE_ADD(CURRENT_TIMESTAMP, INTERVAL +1 HOUR);";

$result = mysqli_query($connection, $sql);

if ($result && mysqli_num_rows($result)) {
    $undone_tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);

    $recipients = [];
    $data = [];

    foreach ($undone_tasks as $user) {
        $data[$user['user_id']][] = ["user_name" => $user['user_name'], "email" => $user['email'], "task_name" => $user['task_name'], "task_deadline" => $user['task_deadline']];
        $recipients[$user['email']] = $user['user_name'];
    }

    var_dump($recipients);

    $user_tasks = [];

    foreach ($data as $key => $value) {
        $user_id = $key;
        $user_tasks = $value;

        var_dump($user_tasks);
    }

    foreach ($user_tasks as $key => $value) {

        $message = new Swift_Message();
        $message->setSubject("Уведомление от сервиса «Дела в порядке»");
        $message->setFrom(['keks@phpdemo.ru' => 'DoingsDone']);
        $message->setBcc($recipients);

        $msg_content = include_template('email.php', ["user_id" => $user_id, "user_tasks" => $user_tasks]);
        $message->setBody($msg_content, 'text/html');

        $mailer_result = $mailer->send($message);
    }
}
    if ($mailer_result) {
        print("Рассылка успешно отправлена");
    } else {
        print("Не удалось отправить рассылку: " . $logger->dump());
    }


