<form class="form form--add-lot container <?= empty($errors) ?: 'form--invalid' ?>" action="/add.php" method="post"
      enctype="multipart/form-data">
    <h2>Добавление лота</h2>
    <div class="form__container-two">
        <div class="form__item <?= empty($errors['name']) ?: 'form__item--invalid' ?>">
            <label for="name">Наименование <sup>*</sup></label>
            <input id="name" type="text" name="name" value="<?= getPostVal('name'); ?>"
                   placeholder="Введите наименование лота">
            <?php if (isset($errors['name'])) : ?>
                <span class="form__error"><?= $errors['name'] ?></span>
            <?php endif; ?>
        </div>
        <div class="form__item <?= empty($errors['categories']) ?: 'form__item--invalid' ?>">
            <label for="category">Категория <sup>*</sup></label>
            <select id="category" name="category">
                <?php foreach ($categories as $item): ?>
                    <option
                        value="<?= $item['id'] ?>" <?= $item['id'] === getPostVal('category') ? 'checked' : '' ?>><?= htmlspecialchars($item['name']); ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['categories'])) : ?>
                <span class="form__error"><?= $errors['categories'] ?></span>
            <?php endif; ?>
        </div>
    </div>
    <div class="form__item form__item--wide <?= empty($errors['description']) ?: 'form__item--invalid' ?>">
        <label for="description">Описание <sup>*</sup></label>
        <textarea id="description" name="description"
                  placeholder="Напишите описание лота"><?= getPostVal('description'); ?></textarea>
        <?php if (isset($errors['description'])) : ?>
            <span class="form__error"><?= $errors['description'] ?></span>
        <?php endif; ?>
    </div>
    <div class="form__item form__item--file <?= empty($errors['lot-img']) ?: 'form__item--invalid' ?>"
    ">
    <label>Изображение <sup>*</sup></label>
    <div class="form__input-file">
        <input class="visually-hidden" type="file" id="lot-img" name="lot-img" value="">
        <label for="lot-img">
            Добавить
        </label>
    </div>
    <?php if (isset($errors['lot-img'])) : ?>
        <span class="form__error"><?= $errors['lot-img'] ?></span>
    <?php endif; ?>
    </div>
    <div class="form__container-three">
        <div class="form__item form__item--small <?= empty($errors['start_price']) ?: 'form__item--invalid' ?>">
            <label for="start_price">Начальная цена <sup>*</sup></label>
            <input id="start_price" type="text" name="start_price" value="<?= getPostVal('start_price'); ?>"
                   placeholder="0">
            <?php if (isset($errors['start_price'])) : ?>
                <span class="form__error"><?= $errors['start_price'] ?></span>
            <?php endif; ?>
        </div>
        <div class="form__item form__item--small <?= empty($errors['rate_step']) ?: 'form__item--invalid' ?>">
            <label for="rate_step">Шаг ставки <sup>*</sup></label>
            <input id="rate_step" type="text" name="rate_step" value="<?= getPostVal('rate_step'); ?>" placeholder="0">
            <?php if (isset($errors['rate_step'])) : ?>
                <span class="form__error"><?= $errors['rate_step'] ?></span>
            <?php endif; ?>
        </div>
        <div class="form__item <?= empty($errors['dt_finish']) ?: 'form__item--invalid' ?>">
            <label for="dt_finish">Дата окончания торгов <sup>*</sup></label>
            <input class="form__input-date" id="dt_finish" type="text" name="dt_finish"
                   value="<?= getPostVal('dt_finish'); ?>" placeholder="Введите дату в формате ГГГГ-ММ-ДД">
            <?php if (isset($errors['dt_finish'])) : ?>
                <span class="form__error"><?= $errors['dt_finish'] ?></span>
            <?php endif; ?>
        </div>
    </div>
    <?php if (!empty($errors)) : ?>
        <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
    <?php endif; ?>
    <button type="submit" class="button">Добавить лот</button>
</form>
