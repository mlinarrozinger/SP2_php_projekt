<?php

session_start();

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/auth_functions.php';
require_once __DIR__ . '/../includes/book_functions.php';
require_once __DIR__ . '/../includes/category_functions.php';

$modul = isset($_GET['modul']) ? $_GET['modul'] : 'dashboard';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

$allowedModules = array('dashboard', 'auth', 'book', 'category');

if (!in_array($modul, $allowedModules, true)) {
    http_response_code(404);
    die('Modul ne obstaja.');
}

$moduleFile = __DIR__ . '/modules/' . $modul . '.php';

if (!file_exists($moduleFile)) {
    http_response_code(404);
    die('Datoteka modula ne obstaja.');
}

require $moduleFile;

