<?php
class Database {
    private static $host = "localhost";
    private static $db = "grade_viewer";
    private static $user = "root";
    private static $pass = "";
    private static $conn;

    public static function connect() {
        if (!self::$conn) {
            self::$conn = new PDO("mysql:host=" . self::$host . ";dbname=" . self::$db, self::$user, self::$pass);
            self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return self::$conn;
    }
}
?>
