<h2 class="content__main-heading">Добавление проекта</h2>

<form class="form"  action="../add-project.php" method="post">
    <div class="form__row">
        <label class="form__label" for="project_name">Название <sup>*</sup></label>
        <?php if (isset($errors["project_name"]) || isset($errors["name_duplicate"])): ?>
            <p class="form__message">
                <?=$errors["project_name"] ?? ""; ?>
                <?=$errors["name_duplicate"] ?? ""; ?>
            </p>
        <?php endif; ?>
        <input class="form__input" type="text" name="project[project_name]" id="project_name" value="" placeholder="Введите название проекта">
    </div>

    <div class="form__row form__row--controls">
        <input class="button" type="submit" name="" value="Добавить">
    </div>
</form>