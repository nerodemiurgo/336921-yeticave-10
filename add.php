<?php
//Подключаем функции
require_once('functions.php');
require_once('helpers.php');
require_once('init.php');

//Объявляем массив с категориями
$categories = getCategories($link);

	//Объявляем массив ошибок и обязательных полей
	$required = ['name', 'category', 'description', 'lot-img', 'start_price', 'rate_step', 'dt_finish'];
	$errors = [];

//Включаем шаблон страницы добавления лота
$add_page = include_template('add-lot.php', [
	'categories' => $categories
]);

//Проверка, что форма была отправлена
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	//Копируем все данные из массива POST
	$newlot = $_POST;
	
	//Объявляем массив проверок
	$rules = [
		'start_price' => function() {
			return validateStartPrice('start_price');
		},
		'dt_finish' => function() {
			return validateDtFinish('dt_finish');
		},
		'rate_step' => function() {
			return validateRateStep('rate_step');
		}
	];
	
	//Проверка поля на заполненность
 	    foreach ($required as $key) {
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
	$newlot['author_id'] = 1;	
	
	
	//Валидация изображения
	$imgCheck = isset($_FILES['lot-img']['name']);
	if (isset($_FILES['lot-img']['name'])) {
		$tmp_name = $_FILES['lot-img']['tmp_name'];
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$file_type = finfo_file($finfo, $tmp_name);
		
			if ($file_type == "image/jpeg") {
				$filename = uniqid().'.jpg';
				$newlot['lot-img'] = $filename;
				move_uploaded_file($_FILES['lot-img']['tmp_name'], 'uploads/'.$filename);	
			} else if ($file_type == "image/png"){
				$filename = uniqid().'.png';
				$newlot['lot-img'] = $filename;
				move_uploaded_file($_FILES['lot-img']['tmp_name'], 'uploads/'.$filename);	
			} else {
				$newlot['lot-img'] = null;
				$errors['img'] = 'Загрузите картинку в формате jpeg или png';
			}
	} else {
		$newlot['lot-img'] = null;
		$errors['img'] = 'Вы не загрузили изображение';
		}

	//Проверяем массив данных и отправляем его в БД
	$res = createLot($link, $newlot['name'], $newlot['description'], $newlot['start_price'], $newlot['price'], $newlot['dt_finish'], $newlot['rate_step'], $newlot['category'], $newlot['lot-img'], $newlot['author_id']);
	
	if ($res) {
            $newlot_id = mysqli_insert_id($link);
            header("Location: lot.php?id=" . $newlot_id);
			print ($newlot_id = mysqli_insert_id($link));
        } else {
		print (mysqli_error($link));	
		}
		
	
		if (count($errors)) 
	{
	$add_page = include_template('add-lot.php', [
	'categories' => $categories,
	'errors' => $errors
	]);
	}
} 
print ($add_page);