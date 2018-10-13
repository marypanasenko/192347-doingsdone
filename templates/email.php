<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
</head>
<body>
<h1>Вам предстоит решить новые задачи :)</h1>

<table>

    <p>Уважаемый, <?=$user_name;?>. У вас запланирована</p>
    <thead>
    <tr>
        <th>Номер</th>
        <th style="width: 200px">Название задачи</th>
        <th>Время</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($future_tasks as $value => $task): ?>
        <tr>
            <td><?=$value+1;?></td>
            <td><?=htmlspecialchars($task['task_name']);?></td>
            <td><?=$task['task_deadline'];?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</body>
</html>