<?php

function getConnection(){
    $host = "localhost:3306";
    $db_name = "gamestats";
    $username = "root";
    $password = "";
    $conn = null;
    try {
        $conn = new PDO("mysql:host=" . $host . ";dbname=" . $db_name, $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $conn->exec("set names utf8");
    } catch(PDOException $exception) {
        echo "Connection error: " . $exception->getMessage();
    }
    return $conn;
}

function runSql($conn, $query, $params) {
    try {
        $stmt = $conn->prepare($query);
        foreach ($params as $key => &$value) {
            $value = sanitize($value);
        }
        $stmt->execute($params);
        return $stmt;
    } catch(PDOException $exception) {
        error_log($exception);
        throw $exception;
    }
}

function commit($conn) {
    $conn->exec("COMMIT;");
}

function rollback($conn) {
    $conn->exec("ROLLBACK;");
}

function sanitize($s) {
    return htmlspecialchars(strip_tags($s));
}

?>