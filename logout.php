<?php
session_start();
require_once "controllers/AuthController.php";
AuthController::logout();
