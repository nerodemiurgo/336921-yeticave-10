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

//Функция для отправки массива в БД
function db_insert_data($link, $sql, $data = [])
{
    $stmt = db_get_prepare_stmt($link, $sql, $data);
    $result = mysqli_stmt_execute($stmt);
    if ($result) {
        $result = mysqli_insert_id($link);
    }

    return $result;
}

//Функция для создания лота
function createLot(
    $connection,
    $name,
    $description,
    $startPrice,
    $price,
    $finishingDate,
    $rateStep,
    $categoryId,
    $imageUrl,
    $authorId
) {
    $sql = <<<SQL
INSERT INTO lot
    (name, description, start_price, price, dt_finish, rate_step, category_id, img, author_id)
VALUES
    (?, ?, ?, ?, ?, ?, ?, ?, ?)
SQL;

    return db_insert_data(
        $connection,
        $sql,
        [
            $name,
            $description,
            $startPrice,
            $price,
            $finishingDate,
            $rateStep,
            $categoryId,
            $imageUrl,
            $authorId
        ]
    );
}

//Функция сохранения заполненных значений формы
function getPostVal($name) {
	return $_POST[$name] ?? '';
}

//Функция проверки заполненности поля
function validateFilled($name) {
	if (empty($_POST[$name])) {
		return 'Это поле должно быть заполнено';
	}
	return null;
}

//Проверка начальной цены
function validateStartPrice($start_price) {
	if ($start_price <= 0) {
		return "Стартовая цена должна быть больше нуля";
	}
	return null;
}

//Проверка даты завершения
function validateDtFinish($dt_finish) {
	$date = is_date_valid($dt_finish);
	$now = time();
	if ($date == true) {
		$str_date = strtotime($dt_finish);
		$tomorrow = strtotime('tomorrow');
		if ($str_date >= $tomorrow) {
			return null;
		} else {
			return "Дата завершения не должна быть меньше завтрашней";
		}
	} 
	if ($date == false) {
		return "Формат даты должен быть ГГГГ-ММ-ДД";
	}
}

//Проверка шага ставки
function validateRateStep($rate_step) {
	$checkRateStep = is_int($rate_step);
	if ($checkRateStep == true) {
		if ($rate_step > 0) {
		return null;
		}
		if ($rate_step == 0) {
		return "Шаг ставки не может быть равен нулю";
		}
	}
		if ($checkRateStep == false) {
		return "Шаг ставки должен быть целым числом";
		}
	}