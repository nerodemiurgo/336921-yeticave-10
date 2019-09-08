<?php
//Подключаем функции
require_once('init.php');

if (empty($_SESSION['user'])) {
	header("HTTP/1.0 403 (Forbidden, доступ запрещен");
	exit;
}

if (!empty($_SESSION['user'])) { 

//Объявляем массив с категориями
$categories = getCategories($link);

$user_id = $_SESSION['user']['id'];

//Получаем информацию по лотам
$lot_info = getMyLots($link, $user_id);

$bets_page = include_template('my-bets.php', [
	'categories' => $categories,
	'lot_info' => $lot_info
	]);
	
	
print ($bets_page);	
}