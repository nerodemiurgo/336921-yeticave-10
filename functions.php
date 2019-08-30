<?php
require_once('init.php');
date_default_timezone_set("Europe/Moscow");

//Функция оформления цены
function decorate_price ($price_num) {
    return number_format(ceil($price_num), 0, ' ', ' ') . '₽';
}

//Функция таймера до закрытия лота
function timeuptoend ($end_date) {
		$cur_ts = time();
		$end_ts = strtotime($end_date);
		$ts_diff = $end_ts - $cur_ts;
		$min_diff = $ts_diff%3600;
		
		$hourse_to_end = str_pad(floor($ts_diff/3600), 2, "0", STR_PAD_LEFT);
		$minutes_to_end = str_pad(floor($min_diff/60), 2, "0", STR_PAD_LEFT);

		return [$hourse_to_end, $minutes_to_end];
}

//Функция для получения списка категорий
function getCategories ($sql_link) {
	$sql = 'SELECT id, name, code FROM category;';
	$result = mysqli_query($sql_link, $sql);
	
		if ($result === false) {
		die("Ошибка при выполнении запроса '$sql'.<br> Текст ошибки: ".mysqli_error($sql_link));
		}
	
	return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

//Функция для получения списка лотов
function getLots ($sql_link) {
	$sql = 'SELECT l.name AS lot_name, c.name AS category_name, start_price, price, img, dt_finish, l.id AS lot_id FROM lot l
			JOIN category c
			ON l.category_id = c.id  WHERE dt_finish > NOW()
			ORDER BY created_at DESC;';
	$result = mysqli_query($sql_link, $sql);
	
		if ($result === false) {
		die("Ошибка при выполнении запроса '$sql'.<br> Текст ошибки: ".mysqli_error($sql_link));
		}
	
	return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function getLot ($sql_link, $link_id) {
	if ($link_id == '') {$link_id = 0;}
	
	$sql = 'SELECT l.name AS lot_name, c.name AS category_name, start_price, price, rate_step, img, description AS lot_desc, dt_finish, l.id AS lot_id FROM lot l
			JOIN category c
			ON l.category_id = c.id  WHERE l.id = '.$link_id.'';
	
	$result = mysqli_query($sql_link, $sql);
		if ($result === false) {
		die("Ошибка при выполнении запроса '$sql'.<br> Текст ошибки: ".mysqli_error($sql_link));
		}
	
	return mysqli_fetch_assoc($result);
}