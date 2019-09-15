<?php
//Подключаем функции
require_once('init.php');

//Объявляем массив с категориями
$categories = getCategories($link);

//Шаблон 404
    $error404 = include_template('404.php',[
	'categories' => $categories
]);

if (empty($_SESSION['user'] ?? null)) {
	header("HTTP/1.0 403 (Forbidden, доступ запрещен");
	exit;
}

if (!empty($_SESSION['user'] ?? null)) { 

$user_id = $_SESSION['user']['id'] ?? null;

//Получаем информацию по лотам

$lot_info = getMyLots($link, $user_id);

if (isset($lot_info)) {
//Формируем контент страницы
$page_content = include_template('my-bets.php', [
	'categories' => $categories,
	'lot_info' => $lot_info
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
} else {
	print ($error404);
    exit;
} 
}