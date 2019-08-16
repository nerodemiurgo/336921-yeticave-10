<?php
$is_auth = rand(0, 1);

$user_name = 'Ольга'; // укажите здесь ваше имя

$title = 'YetiCave - заголовок страницы';

//Подключаем функции
require_once('helpers.php');

//Объявляем массив с категориями
$categories = ['Доски и лыжи','Крепления','Ботинки','Одежда','Инструменты','Разное'];

//Объявляем двумерный массив с объявлениями
$lots_list = [
	[
	'lot_name' => '2014 Rossignol District Snowboard',
	'category' => 'Доски и лыжи',
	'price' => '10999',
	'img_url' => 'img/lot-1.jpg'
	],
	
	[
	'lot_name' => 'DC Ply Mens 2016/2017 Snowboard',
	'category' => 'Доски и лыжи',
	'price' => '159999',
	'img_url' => 'img/lot-2.jpg'
	],
	
	[
	'lot_name' => 'Крепления Union Contact Pro 2015 года размер L/XL',
	'category' => 'Крепления',
	'price' => '8000',
	'img_url' => 'img/lot-3.jpg'
	],
	
	[
	'lot_name' => 'Ботинки для сноуборда DC Mutiny Charocal',
	'category' => 'Ботинки',
	'price' => '10999',
	'img_url' => 'img/lot-4.jpg'
	],
	
	[
	'lot_name' => 'Куртка для сноуборда DC Mutiny Charocal',
	'category' => 'Одежда',
	'price' => '7500',
	'img_url' => 'img/lot-5.jpg'
	],
	
	[
	'lot_name' => 'Маска Oakley Canopy',
	'category' => 'Разное',
	'price' => '5400',
	'img_url' => 'img/lot-6.jpg'
	],
];

//Добавляем функцию оформления цены
function decorate_price ($price_num) {
    return number_format(ceil($price_num), 0, ' ', ' ') . '₽';
}

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
?>