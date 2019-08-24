<?php
$is_auth = rand(0, 1);

$user_name = 'Ольга'; // укажите здесь ваше имя

$title = 'YetiCave - заголовок страницы';

//Подключаем функции
require_once('functions.php');
require_once('helpers.php');
require_once('init.php');

//Проверка ошибки при подключении БД
if ($link === false) {
	exit('Ошибка подключения:' . mysqli_connect_error());
}

//Объявляем массив с категориями
$categories = getCategories($link);

//Объявляем двумерный массив с объявлениями
$lots_list = getLots($link);

//Включаем шаблон страницы
$page_content = include_template('main.php', [
	'categories' => $categories,
	'lots_list' => $lots_list
]);

//Включаем шаблон layout
$layout_content = include_template('layout.php', [
	'title' => $title,
	'categories' => $categories,
	'user_name' => $user_name,
	'content' => $page_content,
	'is_auth' => $is_auth
]);
print($layout_content);