<?php

//Подключаем функции
require_once('init.php');

if (!empty($_SESSION['user'])) {
    header('HTTP/1.0 403 (Forbidden, доступ запрещен)');
    exit;
}


//Объявляем массив с категориями
$categories = getCategories($link);

//Объявляем массив с юзерами
$users = getUsers($link);

//Объявляем массив ошибок и обязательных полей
$required = ['email', 'password', 'user_name', 'contact'];
$errors = [];

//Проверка отправленности формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //Копируем все данные из массива POST
    $newuser = [
        'email'     => $_POST['email'] ?? null,
        'password'  => $_POST['password'] ?? null,
        'user_name' => $_POST['user_name'] ?? null,
        'contact'   => $_POST['contact'] ?? null
    ];

    //Объявляем массив проверок
    $rules = [
        'email' => function () {
            return validateEmail('email');
        },
        'user_name' => function () {
            return validateName('user_name');
        },
        'contact' => function () {
            return validateContact('contact');
        }
    ];

    //Проверка поля на заполненность
    foreach ($required as $key) {
        $_POST[$key] = trim($_POST[$key]);
        if (empty($_POST[$key])) {
            $errors[$key] = 'Это поле надо заполнить';
        }
    }

    foreach ($_POST as $key => $value) {
        if (!isset($errors[$key]) && isset($rules[$key])) {
            $rule = $rules[$key];
            $errors[$key] = $rule();
        }
    }

    //Проверка email на наличие в базе
    if (!isset($errors['email'])) {
        $email = $_POST['email'];
        $checkemail = mysqli_real_escape_string($link, $email);
        $sql = "SELECT id FROM user WHERE email = '$checkemail'";
        $res = mysqli_query($link, $sql);

        if (mysqli_num_rows($res) > 0) {
            $errors['email'] = 'Пользователь с этим email уже зарегистрирован';
        }
    }
    $errors = array_filter($errors);

    if (empty($errors)) {
        //Хэширование пароля
        $newpass = password_hash($_POST['password'], PASSWORD_DEFAULT);

        //Проверяем массив данных и отправляем его в БД
        $result = createUser($link, $newuser['email'], $newpass, $newuser['user_name'], $newuser['contact']);

        if ($result) {
            $newuser_id = mysqli_insert_id($link);
            header('Location: /');
            exit;
        } else {
            print (mysqli_error($link));
        }
    }
}

//Формируем контент страницы
$page_content = include_template('signuppage.php', [
    'categories' => $categories,
    'users'      => $users,
    'errors'     => $errors
]);

//Задаем тайтл
$title = 'Регистрация';

//Включаем шаблон layout
$layout_content = include_template('backpage.php', [
    'title'      => $title,
    'categories' => $categories,
    'content'    => $page_content
]);

print($layout_content);
