<?
//Подключаем функции
require_once('functions.php');
require_once('helpers.php');
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
} else {
//Формирование массива и подключение шаблона лота
$lot_page = include_template('lotpage.php', [
	'categories' => $categories,
	'lot_info' => $lot_info[0]
]);
}
//Вывод лота, если его id существует
if ($lot_info == true) {
print ($lot_page);
} 