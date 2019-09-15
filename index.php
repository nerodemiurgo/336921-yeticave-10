<?php
$title = 'YetiCave - заголовок страницы';

//Подключаем функции
require_once('init.php');
require_once('getwinner.php');

//Проверка ошибки при подключении БД
if ($link === false) {
	exit('Ошибка подключения:' . mysqli_connect_error());
}

//Объявляем массив с категориями
$categories = getCategories($link);

//Объявляем двумерный массив с объявлениями
$lots_list = getLots($link);

//Включаем шаблон главной страницы
$page_content = include_template('main.php', [
	'categories' => $categories,
	'lots_list' => $lots_list
]);

//Включаем шаблон layout
$layout_content = include_template('layout.php', [
	'title' => $title,
	'categories' => $categories,
	'content' => $page_content
]);

print($layout_content);