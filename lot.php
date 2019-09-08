<?php
//Подключаем функции
require_once('init.php');

//Подключаем шаблон ошибки 404
$error404 = include_template('404.php');

//Проверяем наличие запроса id для формирования страницы лота
if (isset($_GET['id'])) {
	$checkID = $_GET['id'];
} else {
	print ($error404);
	die;
}

//Объявляем массив с категориями
$categories = getCategories($link);

//Объявляем массив с информацией для объявлений
$lot_info = getLot($link, $_GET['id']);

//Вывод ошибки, если пришел пустой массив (id объявления не существует)
$checkLotInfo = count($lot_info);
if ($checkLotInfo == 0) {
	print ($error404);
}
	
//Объявляем массив ошибок и обязательных полей
	$required = ['bid'];
	$errors = [];
	
//Проверка, что форма была отправлена
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

	//Копируем все данные из массива POST
	$newrate = $_POST;
	
	//Проверка поля на заполненность
	foreach ($required as $key) {
		if (empty($_POST[$key])) {
			$errors[$key] = 'Это поле надо заполнить';
		}
	}

	//Проверка валидности ставки
	if (empty($errors)) {
	$errors['bid'] = validateRate($newrate['bid'], $lot_info['price'], $lot_info['rate_step']);
	}

	$errors = array_filter($errors); 

	//Добавление новой ставки
	if (empty($errors)) {
		$user_id = $_SESSION['user']['id'];
		$lot_id = $_GET['id'];
		$bid = $newrate['bid'];
		mysqli_query($link, 'START TRANSACTION');
		$newRate = mysqli_query($link, "INSERT INTO rate (bid, user_id, lot_id) VALUES ($bid, $user_id, $lot_id)");
		$updatePrice = mysqli_query($link, "UPDATE lot SET price = $bid WHERE id = $lot_id");
		
			if ($newRate && $updatePrice) {
				mysqli_query($link, "COMMIT");
			} else {
				mysqli_query($link, "ROLLBACK");
			}
			
			header ('Location: /lot.php?id='.$lot_id);
	} 
}

	$rates = getHistoryRates($link, $_GET['id']);
	
//Условие отображения формы добавления ставки	
	if (!empty($_SESSION)) {
	$CanSeeBets = isUserCanSeeBets($link, $lot_info['dt_finish'], $_SESSION['user'], $lot_info['author_id'], $_GET['id']);
	} else {
		$CanSeeBets = false;
	}
	
//Формирование массива и подключение шаблона лота
$lot_page = include_template('lotpage.php', [
	'categories' => $categories,
	'lot_info' => $lot_info,
	'errors' => $errors,
	'rates' => $rates,
	'canseebets' => $CanSeeBets
]);
print ($lot_page);
