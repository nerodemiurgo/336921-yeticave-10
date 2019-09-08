<?php
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

//Функция для получения списка юзеров
function getUsers ($sql_link) {
	$sql = 'SELECT id, user_name, email, password, avatar, contact FROM user;';
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
	
	$sql = 'SELECT l.id AS lot_id, l.name AS lot_name, l.description AS lot_desc, l.img AS img, l.start_price AS start_price, l.price AS price, l.dt_finish AS dt_finish, l.rate_step AS rate_step, l.category_id, l.author_id AS author_id, c.name AS category_name FROM lot l JOIN category c ON l.category_id = c.id WHERE l.id = '.$link_id.'';
	
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

//Функция для создания юзера
function createUser(
	$connection,
	$email,
	$password,
	$user_name,
	$contact
) {
    $sql = <<<SQL
INSERT INTO user
    (email, password, user_name, contact)
VALUES
    (?, ?, ?, ?)
SQL;

    return db_insert_data(
        $connection,
        $sql,
        [
            $email,
            $password,
            $user_name,
            $contact
        ]
    );
}

//Функция сохранения заполненных значений формы
function getPostVal($name) {
	return $_POST[$name] ?? '';
}

//Проверка начальной цены
function validateStartPrice($start_price) {
	$start_price = $_POST[$start_price] ?? 0;
	if ($start_price <= 0) {
		return "Стартовая цена должна быть больше нуля";
	}
	return null;
}

//Проверка даты завершения
function validateDtFinish($dt_finish) {
	$dt_finish = $_POST[$dt_finish] ?? 0;
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
	$rate_step = $_POST[$rate_step] ?? 0;
	if ($rate_step < 0 ) {
		return "Шаг ставки не может быть отрицательным числом";
	} else {	
		$checkRateStep = ctype_digit($rate_step);
		
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
}

	//Проверка ставки
function validateRate($new_rate, $price, $rate_step) {
	$new_rate = $_POST['bid'] ?? 0;
	if ($new_rate < 0 ) {
		return "Ставка не может быть отрицательным числом";
	} else {	
		$checkNewRate = ctype_digit($new_rate);
		
			if ($checkNewRate == true) {
				if ($new_rate > 0) {
						$new_price = $price+$rate_step;
					if ($new_rate < $new_price) {
						return "Ставка не должна быть меньше $new_price";
					}
					if ($new_rate >= $new_price) {
						return null;
					}
				}
				if ($new_rate == 0) {
				return "Ставка не может быть равна нулю";
				}
			}
			if ($checkNewRate == false) {
			return "Ставка должна быть целым числом";
			}
	}
}

//Проверка корректности email
function validateEmail($email) {
	$email = $_POST[$email] ?? 0;
	if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
		return null;
	} 
	else {
		return "Формат email должен соответствовать example@email.com";
	}
}

//Функция для получения истории ставок
function getHistoryRates ($sql_link, $lot_id) {
	$lot_id = $_GET['id'] ?? 0;
	$sql = 'SELECT r.created_at AS time, r.bid AS bid, r.user_id AS user_id, r.lot_id, u.user_name FROM rate r
			JOIN user u
			ON r.user_id = u.id
			WHERE r.lot_id = '.$lot_id.'
			ORDER BY time DESC LIMIT 10
			
			;';
	$result = mysqli_query($sql_link, $sql);
	
		if ($result === false) {
		die("Ошибка при выполнении запроса '$sql'.<br> Текст ошибки: ".mysqli_error($sql_link));
		}
	
	return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

//Функция проверки доступа к форме создания ставки
function isUserCanMakeBet($sql_link, $lot): bool
{
    if (!isset($_SESSION['user']['id'], $_GET['id'], $lot['dt_finish'], $lot['author_id'])) {
        return false;
    }

    $user_id = $_SESSION['user']['id'];
    $lot_id = mysqli_real_escape_string($sql_link, $_GET['id']);

    if ($lot['author_id'] === $user_id) {
        return false;
    }

    $cur_ts = time();
    $end_ts = strtotime($lot['dt_finish']);
    $ts_diff = $end_ts - $cur_ts;

    if ($ts_diff <= 0) {
        return false;
    }

    $sql = 'SELECT r.created_at AS time, r.user_id AS user_id, r.lot_id FROM rate r
		WHERE r.lot_id = '.$lot_id.'
		ORDER BY time DESC LIMIT 1
		;';
    $result = mysqli_query($sql_link, $sql);
    $last_rate = mysqli_fetch_assoc($result);

    if (!isset($last_rate['user_id'])) {
        return true;
    }

    return $last_rate['user_id'] !== $user_id;
}

//Функция возврата класса в строку
function classToString($dt_finish, $winner_id) {
	if (isset($winner_id)) {
		return "rates__item--win";
	} 
	
	$date_now = date_create('now');
	$date_finish = date_create($dt_finish);
	if ($date_finish > $date_now) {
		return null;
	}
	if ($date_finish <= $date_now) {
		return 'rates__item--end';
	}
}

//Функция красивого вывода времени, прошедшего от ставки
function timeFromBet($rate_time) {
		$time_bet = '';
		$date_now = date_create('now');
		$date_rate = date_create($rate_time);
		$date_diff = date_diff($date_rate, $date_now);
		$hour = date_interval_format($date_diff, '%d %h %i');
		$time_bet = explode(' ', $hour);	

	$correctTime = '';
	if ($time_bet[0] > 0) {
		$correctTime = $time_bet[0].' '.get_noun_plural_form($time_bet[0], 'день', 'дня', 'дней').' ';
		$correctTime .= ' '.get_noun_plural_form($time_bet[1], 'час', 'часа', 'часов').' ';
		$correctTime .= ' '.get_noun_plural_form($time_bet[2], 'минута', 'минуты', 'минут');
	} 
	if ($time_bet[0] == 0){
		if ($time_bet[1] > 0) {
			$correctTime = $time_bet[1].' '.get_noun_plural_form($time_bet[1], 'час', 'часа', 'часов').' ';
			$correctTime .= ' '.get_noun_plural_form($time_bet[2], 'минута', 'минуты', 'минут');
		} else if ($time_bet[1] == 0) {
			$correctTime = $time_bet[2].' '.get_noun_plural_form($time_bet[2], 'минута', 'минуты', 'минут');
		}
	}

    return return $correctTime." назад";
}

//Функция для получения информации о своих ставках
function getMyLots ($sql_link, $user_id) {
	$user_id = intval($user_id);
	$sql = 'SELECT MAX(r.id),
       MAX(r.created_at) AS time,
       r.user_id,
       r.lot_id,
       l.id              AS lot_id,
       l.name            AS lot_name,
       l.img             AS img,
       MAX(r.bid)        AS price,
       l.dt_finish       AS dt_finish,
       l.category_id,
       l.winner_id       AS winner_id,
       l.author_id,
       c.name            AS category_name,
       u.id,
       u.contact         AS contact
	FROM rate r
			 JOIN lot l ON r.lot_id = l.id
			 JOIN category c ON l.category_id = c.id
			 JOIN user u ON l.author_id = u.id
	WHERE r.user_id = '.$user_id.'
	GROUP BY l.id
	ORDER BY time DESC';
		
	$result = mysqli_query($sql_link, $sql);
	
		if ($result === false) {
		die("Ошибка при выполнении запроса '$sql'.<br> Текст ошибки: ".mysqli_error($sql_link));
		}
	
	return mysqli_fetch_all($result, MYSQLI_ASSOC);
}