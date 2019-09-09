<?php
//Подключаем функции
require_once('init.php');

if (!isset($_GET['search']) || $_GET['search'] === '') {
    echo 404;
    exit;
}

//Объявляем массив с категориями
$categories = getCategories($link);

//Получаем содержимое поискового запроса
$search     = mysqli_real_escape_string($link, trim($_GET['search']));
$searchLots = '';

$sql = 'SELECT
		COUNT(l.id) as count
			FROM lot l
			JOIN category c ON l.category_id = c.id 
			WHERE MATCH(l.name, l.description) AGAINST(\''.$search.'\') AND dt_finish > NOW()';


$result      = mysqli_query($link, $sql);
$items_count = mysqli_fetch_assoc($result);
$items_count = $items_count['count'] ?? 0;

//Странички для поиска
$cur_page   = $_GET['page'] ?? 1;
$page_items = 9;

$pages_count = ceil($items_count / $page_items);
$offset      = ($cur_page - 1) * $page_items;

$pages = range(1, $pages_count);

$sql = 'SELECT
		l.name AS lot_name,
		c.name AS category_name,
		l.description,
		start_price,
		price,
		img,
		dt_finish,
		l.id AS lot_id
	
			FROM lot l
			JOIN category c ON l.category_id = c.id 
			WHERE MATCH(l.name, l.description) AGAINST(\''.$search.'\') AND dt_finish > NOW()
			ORDER BY created_at DESC LIMIT ' . $page_items . ' OFFSET ' . $offset;

$result = mysqli_query($link, $sql);
$lots   = mysqli_fetch_all($result, MYSQLI_ASSOC);

//Формирование массива и подключение шаблона лота
$search_page = include_template(
    'search_page.php',
    [
        'categories'  => $categories,
        'search'      => $search,
        'lots'        => $lots,
        'pages'       => $pages,
        'pages_count' => $pages_count,
        'cur_page'    => $cur_page,
    ]
);
print ($search_page);
