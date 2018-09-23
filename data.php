<?php
$projects = ["Входящие", "Учеба", "Работа", "Домашние дела", "Авто"];
$tasks = [ [
    "task_name" => "Собеседование в IT компании",
    "date" => "01.12.2018",
    "category" => $projects[2],
    "done" => false
],
    [   "task_name" => "Выполнить тестовое задание",
        "date" => "25.12.2018",
        "category" => $projects[2],
        "done" => false
    ],
    [   "task_name" => "Сделать задание первого раздела",
        "date" => "21.12.2018",
        "category" => $projects[1],
        "done" => true
    ],
    [   "task_name" => "Встреча с другом",
        "date" => "22.12.2018",
        "category" => $projects[0],
        "done" => false
    ],
    [   "task_name" => "Купить корм для кота",
        "date" => "Нет",
        "category" => $projects[3],
        "done" => false
    ],
    [   "task_name" => "Заказать пиццу",
        "date" => "Нет",
        "category" => $projects[3],
        "done" => false
    ]
];