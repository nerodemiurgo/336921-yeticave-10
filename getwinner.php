<?php
//Подключаем функции
require_once('init.php');
require_once('vendor/autoload.php');
$last_rate = [];

$sql_lot = 'SELECT
            id,
            dt_finish,
            winner_id
            FROM lot 
            WHERE dt_finish <= NOW() AND winner_id IS NULL
            ';

$stmt = db_get_prepare_stmt($link, $sql_lot);
mysqli_stmt_execute($stmt);
$result_lot = mysqli_stmt_get_result($stmt);
$lots = mysqli_fetch_all($result_lot, MYSQLI_ASSOC);

if (!empty($lots)) {
    $lot_id = array_column($lots, 'id');

    $sql_rate = "SELECT
            r.created_at,
            r.user_id,
            r.lot_id AS lot_id,
            u.user_name AS user_name,
            l.name AS lot_name,
            u.email AS email

                FROM rate r 
                JOIN user u ON u.id = r.user_id
                JOIN lot l ON l.id = r.lot_id
                WHERE r.lot_id IN (".implode(', ', $lot_id).")
                ORDER BY r.created_at DESC LIMIT 1
                ";
    $stmt = db_get_prepare_stmt($link, $sql_rate);
    mysqli_stmt_execute($stmt);
    $result_rate = mysqli_stmt_get_result($stmt);
    $last_rate = mysqli_fetch_all($result_rate, MYSQLI_ASSOC);
}

foreach ($last_rate as $item) {
    $result_winner = mysqli_query($link,
        "UPDATE lot SET winner_id = " . $item['user_id'] . " WHERE id = " . $item['lot_id']);

    $user_name = $item['user_name'];
    $mail = $item['email'];
    $lot_id = $item['lot_id'];
    $lot_name = $item['lot_name'];

    if ($result_winner) {
        $email = include_template('email.php', [
            'user_name' => $user_name,
            'lot_id'    => $lot_id,
            'lot_name'  => $lot_name
        ]);
        send_message($user_name, $email, $mail);
    }
}
