<?php
session_start();
require('./inc/pdo.php');
require('./inc/fonctions.php');
include('inc/header.php');
$errors = array();

if (isset($_COOKIE['member_email'])) {
    unset($_COOKIE['member_email']);
    setcookie('member_email', '');
}

unset($_SESSION['user']);
session_destroy();
header("location: index.php");