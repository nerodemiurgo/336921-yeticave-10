<?php
require_once('functions.php');
require_once('helpers.php');

$link = mysqli_connect('localhost', 'root', '', 'yeticave');
mysqli_set_charset($link, "utf8");