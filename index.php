<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$page = $_GET['page'] ?? 'login';

if ($page === 'register') {
    include "views/register.php";
} else {
    include "views/login.php";
}
    