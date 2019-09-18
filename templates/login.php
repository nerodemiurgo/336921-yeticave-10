<form class="form container" action="/enter.php" method="post"> <!-- form--invalid -->
    <h2>Вход</h2>
    <div class="form__item <?= empty($errors['email']) ?: 'form__item--invalid' ?>" <!-- form__item--invalid -->
    <label for="email">E-mail <sup>*</sup></label>
    <input id="email" type="text" name="email" placeholder="Введите e-mail" value="<?= getPostVal('email'); ?>">
    <?php if (isset($errors['email'])) : ?>
        <span class="form__error"><?= $errors['email'] ?></span>
    <?php endif; ?>
    </div>
    <div class="form__item form__item--last <?= empty($errors['password']) ?: 'form__item--invalid' ?>"
    ">
    <label for="password">Пароль <sup>*</sup></label>
    <input id="password" type="password" name="password" placeholder="Введите пароль">
    <?php if (isset($errors['password'])) : ?>
        <span class="form__error"><?= $errors['password'] ?></span>
    <?php endif; ?>
    </div>
    <button type="submit" class="button">Войти</button>
</form>
