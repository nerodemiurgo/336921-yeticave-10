<?php
session_start();

require_once('helpers.php');
require_once('functions.php');

$link = mysqli_connect('localhost', 'root', '', 'yeticave');
mysqli_set_charset($link, 'utf8');