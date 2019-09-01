<?php
//Подключаем функции
require_once('functions.php');
require_once('helpers.php');
require_once('init.php');

//Объявляем массив с категориями
$categories = getCategories($link);

//Включаем шаблон страницы добавления лота
$add_page = include_template('add-lot.php', [
	'categories' => $categories
]);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$newlot = $_POST;
	$filename = uniqid().'.jpg';
	$newlot['img'] = $filename;
	$newlot['price'] = $newlot['start_price'];
	move_uploaded_file($_FILES['lot-img']['tmp_name'], 'uploads/'.$filename);
	/* var_dump ($_POST); */
	$sql = 'INSERT INTO lot (name, description, start_price, price, dt_finish, rate_step, category_id, img, autor_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)';
	 
	$stmt = db_get_prepare_stmt($link, $sql, $newlot);
	$res = mysqli_stmt_execute($stmt);
	
	if ($res) {
            $newlot_id = mysqli_insert_id($link);
            header("Location: lot.php?id=" . $newlot_id);
			print ($newlot_id = mysqli_insert_id($link));
        } else {
		print (mysqli_error($link));	
		}
} 
print ($add_page);