<?php
//Подключаем функции
require_once('init.php');

if (empty($_SESSION['user'])) {
    header("HTTP/1.0 403 (Forbidden, доступ запрещен");
    exit;
}


//Объявляем массив с категориями
$categories = getCategories($link);

//Объявляем массив ошибок и обязательных полей
$required = ['name', 'category', 'description', 'start_price', 'rate_step', 'dt_finish'];
$errors = [];

//Проверка, что форма была отправлена
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //Копируем все данные из массива POST
    $newlot = [
        'name' => mysqli_real_escape_string($link, $_POST['name']) ?? null,
        'category' => mysqli_real_escape_string($link, $_POST['category']) ?? null,
        'description' => mysqli_real_escape_string($link, $_POST['description']) ?? null,
        'start_price' => mysqli_real_escape_string($link, $_POST['start_price']) ?? null,
        'rate_step' => mysqli_real_escape_string($link, $_POST['rate_step']) ?? null,
        'dt_finish' => mysqli_real_escape_string($link, $_POST['dt_finish']) ?? null
    ];

    //Объявляем массив проверок
    $rules = [
        'start_price' => function () {
            return validateStartPrice('start_price');
        },
        'dt_finish' => function () {
            return validateDtFinish('dt_finish');
        },
        'rate_step' => function () {
            return validateRateStep('rate_step');
        },
        'name' => function () {
            return validateName('name');
        }
        ,
        'description' => function () {
            return validateDesc('description');
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

    $errors = array_filter($errors);

    //Переменные цены, автора
    $newlot['price'] = $newlot['start_price'];
    $newlot['author_id'] = mysqli_real_escape_string($link, $_SESSION['user']['id']);

    if (empty($errors)) {
        //Валидация изображения
        if (isset($_FILES['lot-img']['error']) && $_FILES['lot-img']['error'] === UPLOAD_ERR_NO_FILE) {
            $errors['lot-img'] = 'Вы не загрузили изображение';
        } elseif (isset($_FILES['lot-img']['error']) && $_FILES['lot-img']['error'] !== UPLOAD_ERR_OK) {
            $errors['lot-img'] = 'Не удалось загрузить изображение';
        } else {
            $tmp_name = $_FILES['lot-img']['tmp_name'];
            $file_type = mime_content_type($tmp_name);

            if ($file_type == "image/jpeg") {
                $filename = uniqid() . '.jpg';
                $newlot['lot-img'] = $filename;
                move_uploaded_file($_FILES['lot-img']['tmp_name'], 'uploads/' . $filename);
            } elseif ($file_type == "image/png") {
                $filename = uniqid() . '.png';
                $newlot['lot-img'] = $filename;
                move_uploaded_file($_FILES['lot-img']['tmp_name'], 'uploads/' . $filename);
            } else {
                $errors['lot-img'] = 'Изображение должно быть формата jpg или png';
            }
        }
    }

    //Проверяем массив данных и отправляем его в БД
    if (empty($errors)) {
        $res = createLot($link, $newlot['name'], $newlot['description'], $newlot['start_price'], $newlot['price'],
            $newlot['dt_finish'], $newlot['rate_step'], $newlot['category'], $newlot['lot-img'], $newlot['author_id']);

        if ($res) {
            $newlot_id = mysqli_insert_id($link);
            header("Location: /lot.php?id=" . $newlot_id);
        } else {
            print (mysqli_error($link));
        }
    }
}

//Формируем контент страницы
$page_content = include_template('add-lot.php', [
    'categories' => $categories,
    'errors' => $errors
]);

//Задаем тайтл
$title = 'Добавление лота';

//Включаем шаблон layout
$layout_content = include_template('backpage.php', [
    'title' => $title,
    'categories' => $categories,
    'content' => $page_content
]);

print($layout_content);
