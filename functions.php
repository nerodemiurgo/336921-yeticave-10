<?php
date_default_timezone_set('Europe/Moscow');

/**
 * Оформляет цену, разделяя ее на порядки и добавляя ₽.
 *
 * Примеры использования:
 * decorate_price('123456789'); // 123 456 789₽
 *
 * @param int $price_num Цена в виде целого числа
 *
 * @return string цена, разбитая на порядки ₽
 */
function decorate_price($price_num)
{
    return number_format(ceil($price_num), 0, ' ', ' ').'₽';
}

/**
 * Создает таймер времени до закрытия лота.
 * Дни пересчитывает в часы, оставшиеся до целого часа минуты пишет
 * в минуты.
 *
 * Примеры использования:
 * timeuptoend($end_date); // HH : ii
 *
 * @param string $end_date Дата в виде строки
 *
 * @return array [часы до окончания, минуты до окончания]
 */
function timeuptoend($end_date)
{
    $cur_ts = time();
    $end_ts = strtotime($end_date);
    $ts_diff = $end_ts - $cur_ts;
    $min_diff = $ts_diff % 3600;

    $hourse_to_end = str_pad(floor($ts_diff / 3600), 2, '0', STR_PAD_LEFT);
    $minutes_to_end = str_pad(floor($min_diff / 60), 2, '0', STR_PAD_LEFT);

    return [$hourse_to_end, $minutes_to_end];
}

/**
 * Получает список категорий из базы данных.
 *
 * Примеры использования:
 * getCategories($sql_link); // массив со всеми имеющиемся категориями
 *
 * @param $sql_link mysqli Ресурс соединения
 *
 * @return array [id, код и название категорий] при true, ошибка sql при false
 */
function getCategories($sql_link)
{
    $sql = 'SELECT id, name, code FROM category;';
    $result = mysqli_query($sql_link, $sql);

    if ($result === false) {
        die("Ошибка при выполнении запроса '$sql'.<br> Текст ошибки: ".mysqli_error($sql_link));
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Получает список пользователей из базы данных.
 *
 * Примеры использования:
 * getUsers($sql_link); // массив со всеми данными всех имеющихся пользователей
 *
 * @param $sql_link mysqli Ресурс соединения
 *
 * @return array [id,имя, мейл, пароль, контакты всех пользователей] при true, ошибка sql при false
 */
function getUsers($sql_link)
{
    $sql = 'SELECT id, user_name, email, password, contact FROM user;';
    $result = mysqli_query($sql_link, $sql);

    if ($result === false) {
        die("Ошибка при выполнении запроса '$sql'.<br> Текст ошибки: ".mysqli_error($sql_link));
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Получает все активные лоты.
 *
 * Примеры использования:
 * getLots($sql_link); // массив со всеми лотами, дата завершения торгов которых больше текущей минимум на 1 день
 *
 * @param $sql_link mysqli Ресурс соединения
 *
 * @return array [id, название, категория, стартовая цена, цена, изображение,
 *               дата окончания торгов всех активных лотов] при true, ошибка sql при false
 */
function getLots($sql_link)
{
    $sql = 'SELECT
        l.name         AS lot_name,
        c.name         AS category_name,
        start_price,
        price,
        img,
        dt_finish,
        l.id           AS lot_id
            FROM lot l
            JOIN category c
            ON l.category_id = c.id  WHERE dt_finish > NOW()
            ORDER BY created_at DESC;';
    $result = mysqli_query($sql_link, $sql);

    if ($result === false) {
        die("Ошибка при выполнении запроса '$sql'.<br> Текст ошибки: ".mysqli_error($sql_link));
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Получает список пользователей из базы данных.
 *
 * Примеры использования:
 * getUsers($sql_link); // массив со всеми данными всех имеющихся пользователей
 *
 * @param $sql_link mysqli Ресурс соединения
 *
 * @return array [id,имя, мейл, пароль, контакты всех пользователей] при true, ошибка sql при false
 */
function getLot($sql_link, $link_id)
{
    if ($link_id == '') {
        $link_id = 0;
    }

    $sql = 'SELECT
        l.id            AS lot_id,
        l.name          AS lot_name,
        l.description   AS lot_desc,
        l.img           AS img,
        l.start_price   AS start_price,
        l.price         AS price,
        l.dt_finish     AS dt_finish,
        l.rate_step     AS rate_step,
        l.category_id,
        l.author_id     AS author_id,
        c.name          AS category_name
        FROM lot l
        JOIN category c ON l.category_id = c.id
        WHERE l.id = '.$link_id.'';

    $result = mysqli_query($sql_link, $sql);
    if ($result === false) {
        die("Ошибка при выполнении запроса '$sql'.<br> Текст ошибки: ".mysqli_error($sql_link));
    }

    return mysqli_fetch_assoc($result);
}

/**
 * Отправляет массив в БД.
 * @param $link mysqli Ресурс соединения
 * @param $sql mysqli Подготовленное sql выражение
 * @param $data = [] массив с данными, которые нужно отправить
 *
 * @return отправленные а базу данных данные
 */
function db_insert_data($link, $sql, $data = [])
{
    $stmt = db_get_prepare_stmt($link, $sql, $data);
    $result = mysqli_stmt_execute($stmt);
    if ($result) {
        $result = mysqli_insert_id($link);
    }

    return $result;
}

/**
 * Функция создания нового лота.
 * Отправляет данные о новом лоте из формы создания лота в базу данных.
 *
 * @param $connection mysqli    Ресурс соединения
 * @param string $name Ресурс   название лота
 * @param string $description   описание лота
 * @param integer $startPrice   стартовая цена
 * @param integer $price        цена на текущий момент (стартовая - для нового лота)
 * @param string $finishingDate дата окончания торгов в формате ГГГГ - ММ - ДД
 * @param integer $rateStep шаг ставки
 * @param integer $categoryId   Id категории
 * @param string $imageUrl      ссылка на изображение
 * @param integer $authorId     Id автора объявления
 *
 * @return добавленный в базу данных лот
 */
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
    $sql = 'INSERT INTO lot
    (name, description, start_price, price, dt_finish, rate_step, category_id, img, author_id)
    VALUES
    (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ';

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

/**
 * Функция создания нового пользователя.
 * Отправляет данные о новом пользователе из формы регистрации в базу данных
 *
 * @param $connection mysqli Ресурс соединения
 * @param string $email      емейл пользователя
 * @param string $password   хэш пароля
 * @param string $user_name  имя пользователы
 * @param string $contact    контакты пользователя
 *
 * @return добавленный в базу данных пользователь
 */
function createUser(
    $connection,
    $email,
    $password,
    $user_name,
    $contact
) {
    $sql = 'INSERT INTO user
    (email, password, user_name, contact)
    VALUES
    (?, ?, ?, ?)
    ';

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

/**
 * Функция сохранения заполненных значений формы.
 * Восстанавливает заполненное значение при перезагрузке формы
 *
 * @param string $name значение формы
 *
 * @return значение из формы
 */
function getPostVal($name)
{
    return $_POST[$name] ?? '';
}

/**
 * Валидация картинок.
 *
 * @return массив с двумя значениями - индикатором наличия ошибки и ее текстом, если индикатор существует,
 *                                 или адрес загруженного файла, если индикатор не существует.
 */
function validateImg()
{
    if (isset($_FILES['lot-img']['error']) && $_FILES['lot-img']['error'] === UPLOAD_ERR_NO_FILE) {
        return ['error', 'Вы не загрузили изображение'];
    } elseif (isset($_FILES['lot-img']['error']) && $_FILES['lot-img']['error'] !== UPLOAD_ERR_OK) {
        return ['error', 'Не удалось загрузить изображение'];
    } else {
        $tmp_name = $_FILES['lot-img']['tmp_name'];
        $file_type = mime_content_type($tmp_name);

        if ($file_type == "image/jpeg") {
            $filename = uniqid().'.jpg';
            return [null, $filename];
        } elseif ($file_type == "image/png") {
            $filename = uniqid().'.png';
            return [null, $filename];
        } else {
            return ['error', 'Изображение должно быть формата jpg или png'];
        }
    }
}

/**
 * Валидация начальной цены.
 *
 * @param integer $start_price стартовая цена для лота
 *
 * @return null, если цена - число больше нуля, текст ошибки, если нет
 */
function validateStartPrice($start_price)
{
    $start_price = intval($_POST[$start_price]) ?? 0;
    if ($start_price <= 0) {
        return 'Стартовая цена должна быть числом больше нуля';
    }
    return null;
}

/**
 * Валидация даты окончания торгов.
 *
 * Поверяет, что дата окончания минимум на сутки больше текущей и соответствует формату ГГГГ-ММ-ДД
 *
 * @param string $dt_finish дата окончания торгов в формате строки
 *
 * @return null, если условия верны, текст ошибки, если нет
 */
function validateDtFinish($dt_finish)
{
    $dt_finish = $_POST[$dt_finish] ?? 0;
    $date = is_date_valid($dt_finish);
    $now = time();
    if ($date === true) {
        $str_date = strtotime($dt_finish);
        $tomorrow = strtotime('tomorrow');
        if ($str_date >= $tomorrow) {
            return null;
        } else {
            return 'Дата завершения не должна быть меньше завтрашней';
        }
    }
    if ($date === false) {
        return 'Формат даты должен быть ГГГГ-ММ-ДД';
    }
}

/**
 * Валидация шага ставки.
 *
 * Поверяет, шаг ставки больше нуля, является целым числом
 *
 * @param integer $rate_step шаг ставки
 *
 * @return null, если условия верны, текст ошибки, если нет
 */
function validateRateStep($rate_step)
{
    $rate_step = $_POST[$rate_step] ?? 0;
    if ($rate_step < 0) {
        return 'Шаг ставки не может быть отрицательным числом';
    } else {
        $checkRateStep = ctype_digit($rate_step);

        if ($checkRateStep === true) {
            if ($rate_step > 0) {
                return null;
            }
            if ($rate_step === 0) {
                return 'Шаг ставки не может быть равен нулю';
            }
        }
        if ($checkRateStep === false) {
            return 'Шаг ставки должен быть целым числом';
        }
    }
}

/**
 * Валидация ставки.
 *
 * Поверяет, что ставка не меньше текущей цены+шаг ставки, является целым положительным числом
 *
 * @param int $new_rate  размер ставки из формы
 * @param int $price     текущая цена лота
 * @param int $rate_step шаг ставки лота
 *
 * @return null, если условия верны, текст ошибки, если нет
 */
function validateRate($new_rate, $price, $rate_step)
{
    $new_rate = $_POST['bid'] ?? 0;
    if ($new_rate < 0) {
        return 'Ставка не может быть отрицательным числом';
    } else {
        $checkNewRate = ctype_digit($new_rate);

        if ($checkNewRate === true) {
            if ($new_rate > 0) {
                $new_price = $price + $rate_step;
                if ($new_rate < $new_price) {
                    return 'Ставка не должна быть меньше $new_price';
                }
                if ($new_rate >= $new_price) {
                    return null;
                }
            }
            if ($new_rate === 0) {
                return 'Ставка не может быть равна нулю';
            }
        }
        if ($checkNewRate === false) {
            return 'Ставка должна быть целым числом';
        }
    }
}

/**
 * Валидация емейла.
 *
 * Поверяет, что емейл корректного вида и его длина не превышает длину отведенного в БД поля
 *
 * @param string $email емейл пользователя
 *
 * @return null, если условия верны, текст ошибки, если нет
 */
function validateEmail($email)
{
    $email = $_POST[$email] ?? 0;
    $checkemail = strlen($email);
    if ($checkemail >= 255) {
        return 'Слишком длинный email';
    } else {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return null;
        } else {
            return 'Формат email должен соответствовать example@email.com';
        }
    }
}

/**
 * Валидация имени.
 *
 * Поверяет, что длина имени не превышает длину отведенного в БД поля
 *
 * @param string $item имя пользователя или название лота
 *
 * @return null, если условия верны, текст ошибки, если нет
 */
function validateName($item)
{
    $item = $_POST[$item] ?? 0;
    $checkitem = strlen($item);
    if ($checkitem > 128) {
        return 'Нужно что-то более короткое';
    } else {
        return null;
    }
}

/**
 * Валидация описания.
 *
 * Поверяет, что длина описания не превышает длину отведенного в БД поля
 *
 * @param string $item описание лота
 *
 * @return null, если условия верны, текст ошибки, если нет
 */
function validateDesc($item)
{
    $item = $_POST[$item] ?? 0;
    $checkitem = strlen($item);
    if ($checkitem > 2048) {
        return 'Описание слишком длинное';
    } else {
        return null;
    }
}


/**
 * Валидация имени.
 *
 * Поверяет, что длина содержимого поля контактов не превышает длину отведенного в БД поля
 *
 * @param string $item содержимое поля контактов
 *
 * @return null, если условия верны, текст ошибки, если нет
 */
function validateContact($item)
{
    $item = $_POST[$item] ?? 0;
    $checkitem = strlen($item);
    if ($checkitem > 255) {
        return 'Попробуйте описать свои контакты немного короче :)';
    } else {
        return null;
    }
}

/**
 * Получает 10 последних ставок для каждого лота.
 *
 * @param $sql_link mysqli Ресурс соединения
 * @param int $lot_id      id лота
 *
 * @return массив с историей ставок, если true, ошибку sql, если false
 */
function getHistoryRates($sql_link, $lot_id)
{
    $lot_id = intval($_GET['id']) ?? 0;
    $sql = 'SELECT
            r.created_at   AS time,
            r.bid AS bid,
            r.user_id      AS user_id,
            r.lot_id,
            u.user_name
            FROM rate r
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

/**
 * Ограничивает доступ к форме ставки.
 *
 * Ставку можно делать, если пользователь залогинен, не является автором лота, не делал последнюю ставку,
 * время проведения трогов не истекло
 *
 * @param $sql_link mysqli Ресурс соединения
 * @param array $lot массив с информацией об открытом лоте
 *
 * @return true, если все условия выполнены, иначе false
 */
function isUserCanMakeBet($sql_link, $lot): bool
{
    if (!isset($_SESSION['user']['id'], $_GET['id'], $lot['dt_finish'], $lot['author_id'])) {
        return false;
    }

    $user_id = $_SESSION['user']['id'];
    $lot_id = mysqli_real_escape_string($sql_link, intval($_GET['id']));

    if ($lot['author_id'] === $user_id) {
        return false;
    }

    $cur_ts = time();
    $end_ts = strtotime($lot['dt_finish']);
    $ts_diff = $end_ts - $cur_ts;

    if ($ts_diff <= 0) {
        return false;
    }

    $sql = 'SELECT
            r.created_at   AS time,
            r.user_id      AS user_id,
            r.lot_id
            FROM rate r
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

/**
 * Возвращает класс для оформления строки в списке ставок.
 *
 * @param string $dt_finish дата окончания торгов
 * @param int $winner_id    id победителя
 *
 * @return rates__item--win, если ставка победила, rates__item--end, если торги завершены, но ставка не победила,
 *                     null, если торги еще не завершены
 */
function classToString($dt_finish, $winner_id)
{
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

/**
 * Оформление периода времени, прошедшего со ставки.
 *
 * Примеры использования:
 * timeFromBet($rate_time); // 5 дней 6 часов 15 минут назад
 *
 * @param date $rate_time время создания ставки
 *
 * @return строку с описанием количества прошедшего времени
 */
function timeFromBet($rate_time)
{
    $time_bet = '';
    $date_now = date_create('now');
    $date_rate = date_create($rate_time);
    $date_diff = date_diff($date_rate, $date_now);
    $hour = date_interval_format($date_diff, '%d %h %i');
    $time_bet = explode(' ', $hour);

    $correctTime = '';
    if ($time_bet[0] > 0) {
        $correctTime = $time_bet[0].' '.get_noun_plural_form($time_bet[0], 'день', 'дня', 'дней').' ';
        $correctTime = $correctTime.$time_bet[1].' '.get_noun_plural_form($time_bet[1], 'час', 'часа',
                'часов').' ';
        $correctTime = $correctTime.$time_bet[2].' '.get_noun_plural_form($time_bet[2], 'минута', 'минуты',
                'минут');
    }
    if ($time_bet[0] == 0) {
        if ($time_bet[1] > 0) {
            $correctTime = $time_bet[1].' '.get_noun_plural_form($time_bet[1], 'час', 'часа', 'часов').' ';
            $correctTime = $correctTime.$time_bet[2].' '.get_noun_plural_form($time_bet[2], 'минута', 'минуты',
                    'минут');
        } else {
            if ($time_bet[1] == 0) {
                $correctTime = $time_bet[2].' '.get_noun_plural_form($time_bet[2], 'минута', 'минуты', 'минут');
            }
        }
    }

    return $correctTime." назад";
}

/**
 * Получение информации о своих ставках.
 *
 * @param $sql_link mysqli Ресурс соединения
 * @param int $user_id     Идентификатор пользователя, для которого получаем ставки
 *
 * @return массив со ставками юзера, если true, ошибку sql, если false
 */
function getMyLots($sql_link, $user_id)
{
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


/**
 * Отправляет емейл о победе ставки.
 *
 * @param string $user_name Имя победителя
 * @param string $content   содержимое шаблона письма
 * @param string $email     Адрес победителя
 *
 * @return отправлен емейл
 */
function send_message($user_name, $content, $email)
{
    $transport = new Swift_SmtpTransport('smtp.mailtrap.io', 2525);
    $transport->setPassword('e3394c63fe8087');
    $transport->setUsername('5104b333c47712');

    $message = new Swift_Message('Ваша ставка победила');
    $message->setTo([$email, $email => $user_name]);
    $message->setFrom(['keks@phpdemo.ru' => 'YetiCave']);
    $message->setBody($content, 'text/html');
    $mailer = new Swift_Mailer($transport);
    $result = $mailer->send($message);
}
