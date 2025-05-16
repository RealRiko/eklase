<?php
session_start();
require_once "controllers/AuthController.php";

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

AuthController::login($username, $password);
