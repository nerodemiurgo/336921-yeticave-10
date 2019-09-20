<?php
//Подключаем функции
require_once('init.php');

//Объявляем массив с категориями
$categories = getCategories($link);

//Шаблон 404
$error404 = include_template('404.php', [
    'categories' => $categories
]);

if (!isset($_GET['search']) || $_GET['search'] === '') {
    print ($error404);
    exit;
}

//Получаем содержимое поискового запроса
$search = mysqli_real_escape_string($link, trim($_GET['search']));
$searchLots = '';

$sql = 'SELECT
        COUNT(l.id) as count
            FROM lot l
            JOIN category c ON l.category_id = c.id 
            WHERE MATCH(l.name, l.description) AGAINST(\'' . $search . '\') AND dt_finish > NOW()
            ';

$result = mysqli_query($link, $sql);
$items_count = mysqli_fetch_assoc($result);
$items_count = $items_count['count'] ?? 0;

//Странички для поиска

$cur_page = $_GET['page'] ?? 1;
$page_items = 9;

$pages_count = ceil($items_count / $page_items);
$offset = ($cur_page - 1) * $page_items;

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
            WHERE MATCH(l.name, l.description) AGAINST(\'' . $search . '\') AND dt_finish > NOW()
            ORDER BY created_at DESC LIMIT ' . $page_items . ' OFFSET ' . $offset;

$result = mysqli_query($link, $sql);
$lots = mysqli_fetch_all($result, MYSQLI_ASSOC);

//Добавление количества ставок
for ($n = 0; $n <= 8; $n = $n + 1) {
    if (isset($lots[$n]['lot_id'])) {
        $lot_id = $lots[$n]['lot_id'];
        $sql = 'SELECT COUNT(*) as count FROM rate
                WHERE lot_id = ' . $lot_id . '
                ;';
        $result = mysqli_query($link, $sql);
        $rate_count = mysqli_fetch_assoc($result);
        $rate_count = $rate_count['count'] ?? 0;

        if ($result == false) {
            die("Ошибка при выполнении запроса '$sql'.<br> Текст ошибки: " . mysqli_error($link));
        }
        if ($result == true) {
            if ($rate_count == 0) {
                $lots[$n]['rates'] = "Стартовая цена";
            } else {
                $lots[$n]['rates'] = $rate_count . ' ' . get_noun_plural_form($rate_count, 'ставка', 'ставки',
                        'ставок');
            }
        }
    }
}

//Формируем контент страницы
$pagination = include_template(
    'pagination.php',
    [
        'search' => $search,
        'pages' => $pages,
        'pages_count' => $pages_count,
        'cur_page' => $cur_page
    ]);

$page_content = include_template(
    'search_page.php',
    [
        'categories' => $categories,
        'search' => $search,
        'lots' => $lots,
        'pagination' => $pagination
    ]);

//Задаем тайтл
$title = 'Поиск ' . $search;

//Включаем шаблон layout
$layout_content = include_template('backpage.php', [
    'title' => $title,
    'categories' => $categories,
    'content' => $page_content
]);

print($layout_content);
