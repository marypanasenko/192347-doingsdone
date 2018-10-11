<h2 class="content__main-heading">Список задач</h2>

<form class="search-form" action="index.php" method="get">
    <input class="search-form__input" type="text" name="q" value placeholder="Поиск по задачам">

    <input class="search-form__submit" type="submit" name="" value="Искать">
</form>

<div class="tasks-controls">
    <nav class="tasks-switch">
        <a href="<?= link_filter($project_id, "all");?>" class="tasks-switch__item <?php if($tasks_switch == null || $tasks_switch == "all"):?>tasks-switch__item--active<?php endif;?>">Все задачи</a>
        <a href="<?= link_filter($project_id, "today");?>" class="tasks-switch__item <?php if($tasks_switch == "today"):?>tasks-switch__item--active<?php endif;?>">Повестка дня</a>
        <a href="<?= link_filter($project_id, "tomorrow");?>" class="tasks-switch__item <?php if($tasks_switch == "tomorrow"):?>tasks-switch__item--active<?php endif;?>">Завтра</a>
        <a href="<?= link_filter($project_id, "delay");?>" class="tasks-switch__item <?php if($tasks_switch == "delay"):?>tasks-switch__item--active<?php endif;?>">Просроченные</a>
    </nav>

    <label class="checkbox">
        <input class="checkbox__input visually-hidden show_completed" type="checkbox"
               <?php if ($show_complete_tasks): ?>checked<?php endif; ?>>
        <span class="checkbox__text">Показывать выполненные</span>
    </label>
</div>
<table class="tasks">
    <?php foreach ($tasks as $key => $item): ?>
        <?php if (($show_complete_tasks and $item["task_status"]) or !$item["task_status"]): ?>
            <tr class="tasks__item task
                <?php if ($item["task_status"]): ?>
                    task--completed
                <?php endif; ?>
                <?= time_left($item["task_deadline"]) ?>">
                <td class="task__select">
                    <label class="checkbox task__checkbox">
                        <input class="checkbox__input visually-hidden task__checkbox" type="checkbox" value="<?= $item["id"]; ?>"
                            <?php if ($item["task_status"]): ?>
                                checked
                            <?php endif; ?>>
                        <span class="checkbox__text">
                            <?= htmlspecialchars($item["task_name"]); ?>
                        </span>
                    </label>
                </td>
                <td class="task__file">
                    <?php if ($item["file"] !== NULL and $item["file"] !== ""): ?>
                    <a class="download-link" href="../uploads/<?= $item["file"] ?>">
                        <?= $item["file"] ?>
                    </a>
                    <?php endif; ?>
                </td>
                <td class="task__task_deadline">
                    <?php
                        if ($item["task_deadline"] == NULL || $item["task_deadline"] == "01.01.1970"):
                            print "Нет";
                        else:
                            print $item["task_deadline"];
                    endif;?>
                </td>
            </tr>
        <?php endif; ?>
    <?php endforeach; ?>
</table>

