<?php

/*
    Fiksni prijavni podatki za administracijo.
    Po želji jih kasneje prestaviš v config datoteko.
*/
if (!defined('ADMIN_USERNAME')) {
    define('ADMIN_USERNAME', 'admin');
}

if (!defined('ADMIN_PASSWORD')) {
    define('ADMIN_PASSWORD', 'geslo123');
}

/*
    Preveri, ali je admin prijavljen
*/
function isAdminLoggedIn()
{
    return isset($_SESSION['admin']) && $_SESSION['admin'] === true;
}

/*
    Zaščiti admin module
*/
function requireAdmin()
{
    if (!isAdminLoggedIn()) {
        header('Location: index.php?modul=auth&action=login');
        exit;
    }
}

/*
    Prijava admina
*/
function loginAdmin($username, $password)
{
    $username = trim($username);
    $password = trim($password);

    if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
        $_SESSION['admin'] = true;
        $_SESSION['admin_username'] = $username;
        return true;
    }

    return false;
}

/*
    Odjava admina
*/
function logoutAdmin()
{
    $_SESSION = array();

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();

        setcookie(
            session_name(),
            '',
            time() - 42000,
            isset($params['path']) ? $params['path'] : '/',
            isset($params['domain']) ? $params['domain'] : '',
            isset($params['secure']) ? $params['secure'] : false,
            isset($params['httponly']) ? $params['httponly'] : false
        );
    }

    session_destroy();
}
?>
