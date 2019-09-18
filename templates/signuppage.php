<form class="form container form--invalid" action="/sign-up.php" method="post" autocomplete="off">
    <h2>Регистрация нового аккаунта</h2>
    <div class="form__item <?= empty($errors['email']) ?: 'form__item--invalid' ?>">
        <label for="email">E-mail <sup>*</sup></label>
        <input id="email" type="text" name="email" placeholder="Введите e-mail" value="<?= getPostVal('email'); ?>">
        <?php if (isset($errors['email'])) : ?>
            <span class="form__error"><?= $errors['email'] ?></span>
        <?php endif; ?>
    </div>
    <div class="form__item <?= empty($errors['password']) ?: 'form__item--invalid' ?>"
    ">
    <label for="password">Пароль <sup>*</sup></label>
    <input id="password" type="password" name="password" placeholder="Введите пароль">
    <?php if (isset($errors['password'])) : ?>
        <span class="form__error"><?= $errors['password'] ?></span>
    <?php endif; ?>
    </div>
    <div class="form__item <?= empty($errors['user_name']) ?: 'form__item--invalid' ?>">
        <label for="user_name">Имя <sup>*</sup></label>
        <input id="user_name" type="text" name="user_name" placeholder="Введите имя"
               value="<?= getPostVal('user_name'); ?>">
        <?php if (isset($errors['user_name'])) : ?>
            <span class="form__error"><?= $errors['user_name'] ?></span>
        <?php endif; ?>
    </div>
    <div class="form__item <?= empty($errors['contact']) ?: 'form__item--invalid' ?>"
    ">
    <label for="contact">Контактные данные <sup>*</sup></label>
    <textarea id="contact" name="contact"
              placeholder="Напишите как с вами связаться"><?= getPostVal('contact'); ?></textarea>
    <?php if (isset($errors['contact'])) : ?>
        <span class="form__error"><?= $errors['contact'] ?></span>
    <?php endif; ?>
    </div>
    <?php if (!empty($errors)) : ?>
        <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
    <?php endif; ?>
    <button type="submit" class="button">Зарегистрироваться</button>
    <a class="text-link" href="/enter.php">Уже есть аккаунт</a>
</form>
