<?php
//Подключаем функции
require_once('init.php');

if (empty($_SESSION['user'])) {
	header("HTTP/1.0 403 (Forbidden, доступ запрещен");
	exit;
}

//Объявляем массив с категориями
$categories = getCategories($link);

$bets_page = include_template('my-bets.php', [
'categories' => $categories
	]);
	
print ($bets_page);	