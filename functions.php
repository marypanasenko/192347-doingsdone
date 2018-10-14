<?php
/**
 * Подключает шаблоны
 * @param $name string -- имя файла с шаблоном
 * @param $data array -- массив с переменными
 * @return string $result -- подключаемый шаблон
 */
function include_template($name, $data)
{
    $name = "templates/" . $name;
    $result = "";

    if (!file_exists($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    require $name;

    $result = ob_get_clean();

    return $result;
}

/**
 * Высчитывает сколько времени осталось до окончания зададачи,
 * если меньше 24 часов делает его важным, передает класс "task--important"
 * @param $date -- дата дедлайна
 * @return string -- добавляет или нет класс "task--important"
 */

function time_left($date)
{
    date_default_timezone_set("Europe/Moscow");

    if ($date !== null) {

        $task_date = strtotime($date);
        $secs_to_date = $task_date - strtotime("now");
        $hours_left = floor($secs_to_date / 3600);

        if ($hours_left <= 24 && $date !== null && $date !== "01.01.1970") {
            return "task--important";
        } else {
            return "";
        }
    }
}

/**
 * Подключает шаблон с ошибкой
 * @param $connection -- установка соединения
 */

function error_template($connection)
{
    $error = mysqli_error($connection);
    $content = include_template("error.php", ["error" => $error]);
    print ($content);
    exit();
}

/**
 * Делает запрос в БД, выбирает ID проекта у текущего пользователя
 * @param $current_user -- текущий пользователь
 * @param $connection -- установка соединения
 * @param $project_id -- ID проекта
 * @return array|null -- возвращает результат с ID проекта текущего пользователя
 */

function get_project_id($current_user, $connection, $project_id)
{
    $sql = "SELECT p.*
        FROM projects AS p
        WHERE user_id = $current_user
        AND id = $project_id";

    $result = mysqli_query($connection, $sql);
    $project_id_result = mysqli_fetch_row($result);

    return $project_id_result;
}

/**
 * Делает запрос в БД, выбирает имя проекта у текущего юзера
 * @param $current_user -- текущий пользователь
 * @param $connection -- установка соединения
 * @param $project_name -- имя проекта
 * @return array|null -- возвращает имя проекта текущего пользователя
 */

function get_project_name($current_user, $connection, $project_name)
{
    $sql = "SELECT p.project_name
        FROM projects AS p
        WHERE user_id = $current_user
        AND project_name = '$project_name'";
    $result = mysqli_query($connection, $sql);
    $project_name_result = mysqli_fetch_row($result);

    return $project_name_result;
}

/**
 * Делает запрос в БД, выбирает задачи из таблицы задач текущего пользователя
 * для нужного проекта, если установлен ID проекта
 * @param $current_user -- текущий пользователь
 * @param $connection -- установка соединения
 * @param $project_id -- ID проекта
 * @return array|null -- возвращает массив задач
 */

function tasks_sql($current_user, $connection, $project_id)
{
    $add_and = isset($project_id) ? "AND project_id = $project_id" : null;
    $sql = "SELECT t.*, date_format(task_deadline, '%d.%m.%Y') AS task_deadline
            FROM tasks AS t 
            WHERE user_id = $current_user $add_and";

    $result = mysqli_query($connection, $sql);

    if (!$result) {
        error_template($connection);
    } else {
        $tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
    return $tasks;
}

/**
 * Делает запрос в БД, выбирает проекты из таблицы проектов текущего пользователя
 * @param $current_user -- текущий пользователь
 * @param $connection -- установка соединения
 * @return array|null -- возвращает массив проектов
 */
function projects_sql($current_user, $connection)
{
    $sql = "SELECT p.*, COUNT(t.project_id) AS cnt 
            FROM projects AS p 
            LEFT JOIN tasks AS t ON t.project_id = p.id 
            WHERE p.user_id = $current_user
            GROUP BY p.id";

    $result = mysqli_query($connection, $sql);

    if (!$result) {
        $error = mysqli_error($connection);
        $content = include_template("error.php", ["error" => $error]);

        print ($content);
        exit();

    } else {
        $projects = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
    return $projects;
}

/**
 * Записывает в БД новую задачу
 * @param $tasks -- массив с полученными данными о задаче
 * @param $connection -- установка соединения
 * @param $uploded_date -- дата загрузки
 * @param $uploded_file -- загружаемый файл
 * @param $current_user -- текущий пользователь
 * @return bool -- возвращает ответ об успехе или неудаче
 */

function post_task($tasks, $connection, $uploded_date, $uploded_file, $current_user)
{
    $sql = "INSERT INTO tasks (task_name, task_deadline, file, project_id, user_id) VALUES (?, ?, ?, ?, ?)";

    $stmt = db_get_prepare_stmt($connection, $sql,
        [$tasks["task_name"], $uploded_date, $uploded_file, $tasks["project_id"], $current_user]);
    $result = mysqli_stmt_execute($stmt);

    return $result;
}

/**
 * Записывает в БД новый проект
 * @param $projects -- массив с полученными данными о проекте
 * @param $connection -- установка соединения
 * @param $current_user -- текущий пользователь
 * @return bool -- возвращает ответ об успехе или неудаче
 */

function post_project($projects, $connection, $current_user)
{
    $sql = "INSERT INTO projects (project_name, user_id) VALUES (?, ?)";

    $stmt = db_get_prepare_stmt($connection, $sql, [$projects["project_name"], $current_user]);
    $result = mysqli_stmt_execute($stmt);

    return $result;
}

/**
 * Записывает в БД нового пользователя
 * @param $register -- массив с полученными данными о пользователе
 * @param $connection -- установка соединения
 * @return bool -- возвращает ответ об успехе или неудаче
 */
function registration($register, $connection)
{
    $password_hash = password_hash($register["password"], PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (reg_date, email, user_name, user_pass, token) VALUES (NOW(), ?, ?, ?, '')";

    $stmt = db_get_prepare_stmt($connection, $sql, [$register['email'], $register["name"], $password_hash]);
    $result = mysqli_stmt_execute($stmt);

    return $result;
}

/**
 * Проверяет есть ли в БД введенный email
 * @param $connection -- установка соединения
 * @param $register -- массив с полученными данными о пользователе
 * @return bool|mysqli_result -- возвращает 0 или ID пользователя с веденным
 * email адресом
 */
function email_check($connection, $register)
{
    $email = mysqli_real_escape_string($connection, $register['email']);
    $sql = "SELECT id FROM users WHERE email = '$email'";

    $result = mysqli_query($connection, $sql);

    return $result;
}

/**
 * Проверяет существования пользователя по введенному email
 * @param $connection -- установка соединения
 * @param $authorization -- массив с полученными данными о пользователе при авторизации
 * @return array|null -- возвращает null или массив о пальзователе с email
 */
function session($connection, $authorization)
{
    $email = mysqli_real_escape_string($connection, $authorization['email']);
    $sql = "SELECT * FROM users WHERE email = '$email'";

    $res = mysqli_query($connection, $sql);
    $session_array = $res ? mysqli_fetch_array($res, MYSQLI_ASSOC) : null;

    return $session_array;
}

/**
 * Формирует ссылку для для выбранного проекта и фильтра задач
 * @param $project_id -- ID проекта
 * @param $filter_item -- выбранный фильтр задач
 * @return string -- возвращает сформированную ссылку
 */
function link_filter($project_id, $filter_item)
{
    $get_data["tasks-switch"] = $filter_item;
    $get_data["project_id"] = $project_id;

    $scriptname = pathinfo("index.php", PATHINFO_BASENAME);
    $query = http_build_query($get_data);
    $url = "/" . $scriptname . "?" . $query;

    return $url;
}

/**
 * Делает запрос в БД о задачах за выбранное из фильтра время
 * @param $date -- выбранное время
 * @param $add_and -- добавляет выборку по ID проекта
 * @param $current_user -- текущий пользователь
 * @return string -- возвращет sql запрос
 */
function sql_filter($date, $add_and, $current_user)
{
    $sql = "SELECT t.*, date_format(task_deadline, '%d.%m.%Y') AS task_deadline
            FROM tasks AS t 
            WHERE user_id = $current_user
            $date $add_and";

    return $sql;
}

/**
 * Меняет статус у задачи Выполнено <=> Невыволнено
 * @param $connection -- установка соединения
 * @param $task_id -- ID задачи
 * @param $current_user -- текущий пользователь
 * @return bool|mysqli_result -- возвращает задачу с измененным статусом
 */
function sql_task_id($connection, $task_id, $current_user)
{
    $sql = "UPDATE tasks SET task_status = NOT task_status
        WHERE user_id = $current_user
        AND id = $task_id";

    $result = mysqli_query($connection, $sql);

    return $result;
}

/**
 * Осуществляет полнотекстовый поиск по БД
 * @param $connection -- установка соединения
 * @param $search_trim -- искомое слово
 * @param $current_user -- текущий пользователь
 * @return bool|mysqli_result -- возраащет результаты
 */
function search_sql($connection, $search_trim, $current_user)
{
    $sql = "SELECT t.*, date_format(task_deadline, '%d.%m.%Y') AS task_deadline
            FROM tasks AS t 
            WHERE MATCH(task_name) AGAINST((?) IN BOOLEAN MODE)
            AND user_id = $current_user";

    $stmt = db_get_prepare_stmt($connection, $sql, [("*$search_trim*")]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return $result;
}

/**
 * Делает запрос в БД о невыполненных задачах, до окончания срока выполнения которых осталось меньше часа.
 * @param $connection -- установка соединения
 * @return bool|mysqli_result - возвращает результат
 */
function undone_tasks($connection)
{
    $sql = "SELECT *, date_format(task_deadline, '%k:%i %d.%m') AS task_deadline
            FROM tasks AS t
            LEFT JOIN users AS u ON u.id = t.user_id
            WHERE task_status = 0
            AND task_deadline >= CURRENT_TIMESTAMP
            AND task_deadline <= DATE_ADD(CURRENT_TIMESTAMP, INTERVAL +1 HOUR);";

    $result = mysqli_query($connection, $sql);

    return $result;
}

/**
 * Подгатавливает запрос на отправку в БД,
 * делая его безопасными для внедрения
 * @param $connection -- установка соединения
 * @param $sql -- запрос
 * @param array $data -- данные для передачи в БД
 * @return bool|mysqli_stmt -- возвращает подготовленное выражение
 */
function db_get_prepare_stmt($connection, $sql, $data = [])
{
    $stmt = mysqli_prepare($connection, $sql);

    if ($data) {
        $types = "";
        $stmt_data = [];

        foreach ($data as $value) {
            $type = null;

            if (is_int($value)) {
                $type = "i";
            } else {
                if (is_string($value)) {
                    $type = "s";
                } else {
                    if (is_double($value)) {
                        $type = "d";
                    }
                }
            }

            if ($type) {
                $types .= $type;
                $stmt_data[] = $value;
            }
        }

        $values = array_merge([$stmt, $types], $stmt_data);

        $func = "mysqli_stmt_bind_param";
        $func(...$values);
    }

    return $stmt;
}