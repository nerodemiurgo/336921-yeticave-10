<?php
//Подключаем функции
require_once('init.php');

//Объявляем массив с категориями
	$categories = getCategories($link);

/* //Объявляем массив с юзерами
	$users = getUsers($link); */
	
//Объявляем массив ошибок и обязательных полей
	$required = ['email', 'password'];
	$errors = [];

//Проверка отправленности формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	
	//Копируем все данные из массива POST
	$userenter = $_POST;
	
	//Объявляем массив проверок
	$rules = [
		'email' => function() {
			return validateEmail('email');
		}
	];
	
	//Проверка поля на заполненность
 	foreach ($required as $key) {
		if (empty($_POST[$key])) {
            $errors[$key] = 'Это поле надо заполнить';
		}
	}

    foreach ($_POST as $key => $value) {
        if (!isset($errors[$key]) && isset($rules[$key])) {
            $rule = $rules[$key];
            $errors[$key] = $rule();
        }
    }
	
	//Проверка email на наличие в базе
	if (empty($errors['email'])) {
		$email = mysqli_real_escape_string($link, $userenter['email']);
		$sql = "SELECT * FROM user WHERE email = '$email'";
		$res = mysqli_query($link, $sql);
			
		if (mysqli_num_rows($res) == 0) {
			$errors['email'] = 'Такой пользователь не найден';
		} else {	
			$user = mysqli_fetch_array($res, MYSQLI_ASSOC);
			$errors = array_filter($errors);

			if (empty($errors)) {
				if (password_verify($userenter['password'], $user['password'])) {
					$_SESSION['user'] = $user;
					header('Location: /');
					exit;
				}
				else {
					$errors['password'] = 'Неверный пароль';
				}
			}
		}
	}
}

	//Включаем шаблон
$login_page = include_template('login.php', [
	'categories' => $categories,
	'errors' => $errors
]);

print ($login_page);