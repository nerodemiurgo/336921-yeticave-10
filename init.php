<?php
session_start();

define('CACHE_DIR', basename(__DIR__ . DIRECTORY_SEPARATOR . 'cache'));
define('UPLOAD_PATH', basename(__DIR__ . DIRECTORY_SEPARATOR . 'uploads'));

require_once('functions.php');
require_once('helpers.php');

$link = mysqli_connect('localhost', 'root', '', 'yeticave');
mysqli_set_charset($link, "utf8");