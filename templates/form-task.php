
                <h2 class="content__main-heading">Добавление задачи</h2>

                <form class="form"  action="add.php" method="post" enctype="multipart/form-data">
                    <div class="form__row">
                        <?php if (isset($errors["task_name"])): ?>
                        <p class="form__message">
                            <span class="error-message">«Заполните это поле»</span>
                        </p>
                        <?php endif; ?>
                        <label class="form__label" for="name">Название <sup>*</sup></label>

                        <input class="form__input
                        <?php if (isset($errors["task_name"])): ?>
                            form__input--error
                        <?php endif; ?>
                        " type="text" name="tasks[task_name]" id="name" value="" placeholder="Введите название">
                    </div>

                    <div class="form__row">
                        <label class="form__label" for="project">Проект <sup>*</sup></label>

                        <select class="form__input form__input--select" name="tasks[project_id]" id="project">
                            <?php foreach ($projects as $key => $item): ?>
                            <option value="<?= $item["id"] ?>"><?= htmlspecialchars($item["project_name"]); ?></option>
                            <?php endforeach; ?>
                        </select>

                    </div>

                    <div class="form__row">
                        <label class="form__label" for="date">Дата выполнения</label>

                        <input class="form__input form__input--date" type="date" name="tasks[task_deadline]" id="date" value="" placeholder="Введите дату в формате ДД.ММ.ГГГГ">
                    </div>

                    <div class="form__row">
                        <label class="form__label" for="preview">Файл</label>

                        <div class="form__input-file">
                            <input class="visually-hidden" type="file" name="file" id="preview" value="">

                            <label class="button button--transparent" for="preview">
                                <span>Выберите файл</span>
                            </label>
                        </div>
                    </div>

                    <div class="form__row form__row--controls">
                        <input class="button" type="submit" name="" value="Добавить">
                    </div>
                </form>