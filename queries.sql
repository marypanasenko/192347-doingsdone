INSERT INTO users (date, email, user_name, user_pass, contacts)
VALUES
    ("2017.12.12", "sasha@gmail.com", "Александр", "12345", "г.Симферополь"),
    ("2018.01.02", "masha@gmail.com", "Мария", "12345", "г.Симферополь");

INSERT INTO projects (project_name, user_id)
VALUES
    ("Входящие", 2),
    ("Учеба", 1),
    ("Работа", 1),
    ("Домашние дела", 2),
    ("Авто", 1);

INSERT INTO tasks (date_start, date_done, task_status, task_name, file, task_deadline, user_id, project_id)
VALUES
    ("2018.02.22", NULL, 0, "Собеседование в IT компании", NULL, "01.12.2018", 1, 3),
    ("2018.03.03", NULL, 0, "Выполнить тестовое задание", NULL, "25.12.2018", 1, 3),
    ("2018.04.04", "2018.09.24", 1, "Сделать задание первого раздела", NULL, "21.12.2018", 1, 2),
    ("2018.05.15", NULL, 0, "Встреча с другом", NULL, "22.12.2018", 2, 1),
    ("2018.09.26", NULL, 0, "Купить корм для кота", NULL, NULL, 2, 4),
    ("2018.09.26", NULL, 0, "Заказать пиццу", NULL, NULL, 2, 4);

-- получить список из всех проектов для одного пользователя;

SELECT * FROM projects WHERE user_id = 1;

-- получить список из всех задач для одного проекта;

SELECT * FROM tasks WHERE project_id = 4;

-- пометить задачу как выполненную;

UPDATE tasks SET task_status = 1, date_done = NOW() WHERE id = 1;

-- получить все задачи для завтрашнего дня;

SELECT * FROM tasks WHERE date_start = "2018.09.26";

-- обновить название задачи по её идентификатору.

UPDATE tasks SET task_name = "Заказать две больших пиццы Маргариты" WHERE id = 6;