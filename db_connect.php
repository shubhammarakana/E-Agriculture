<?php
// db_connect.php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "e_agriculture_db";

// Define BASE_URL dynamically if not already defined
if (!defined('BASE_URL')) {
    $docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'])) : '';
    $currentDir = str_replace('\\', '/', realpath(__DIR__));
    if ($docRoot && strpos($currentDir, $docRoot) === 0) {
        $relPath = substr($currentDir, strlen($docRoot));
        $baseUrl = '/' . trim($relPath, '/') . '/';
        if ($baseUrl === '//') $baseUrl = '/';
        define('BASE_URL', $baseUrl);
    } else {
        define('BASE_URL', '/');
    }
}

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

