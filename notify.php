<?php
require_once 'vendor/autoload.php';
require_once 'init.php';
require_once 'functions.php';


$transport = new Swift_SmtpTransport("phpdemo.ru", 25);
$transport->setUsername("keks@phpdemo.ru");
$transport->setPassword("htmlacademy");

$mailer = new Swift_Mailer($transport);


$logger = new Swift_Plugins_Loggers_ArrayLogger();
$mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($logger));

$sql = "SELECT u.id
    FROM users AS u";

$result = mysqli_query($connection, $sql);

if ($result && mysqli_num_rows($result)) {
    $users_ids = mysqli_fetch_all($result, MYSQLI_ASSOC);
}


foreach ($users_ids as $user_id) {

    $sql = "SELECT *
  FROM tasks AS t
  LEFT JOIN users AS u ON u.id = t.user_id 
  WHERE task_status = 0
  AND t.user_id = {$user_id["id"]}
  AND task_deadline >= CURRENT_TIMESTAMP
  AND task_deadline <= DATE_ADD(CURRENT_TIMESTAMP, INTERVAL +1 HOUR)";

    $result = mysqli_query($connection, $sql);

    if ($result && mysqli_num_rows($result)) {
        $future_tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);

        $recipients = [];

        foreach ($future_tasks as $user) {
            $recipients[$user['email']] = $user['user_name'];
        }

        $user_name = $user['user_name'];

        $message = new Swift_Message();
        $message->setSubject("Уведомление от сервиса «Дела в порядке»");
        $message->setFrom(['keks@phpdemo.ru' => 'DoingsDone']);
        $message->setBcc($recipients);

        $msg_content = include_template('email.php', ['future_tasks' => $future_tasks, "user_name" => $user_name]);
        $message->setBody($msg_content, 'text/html');

        $mailer_result = $mailer->send($message);

    }
}


if ($mailer_result) {
    print("Рассылка успешно отправлена");
} else {
    print("Не удалось отправить рассылку: " . $logger->dump());
}

