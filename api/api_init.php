<?php

require_once __DIR__ . '/../db.php';

/*
    API vrača JSON
*/
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

/*
    Preveri, ali je povezava z bazo na voljo
*/
if (!isset($conn) || !$conn) {
    http_response_code(500);
    echo json_encode(array(
        'success' => false,
        'message' => 'Povezava z bazo ni na voljo.'
    ));
    exit;
}

/*
    Pošlje JSON odgovor in zaključi izvajanje skripte
*/
function jsonResponse($data, $statusCode)
{
    if ($statusCode === null) {
        $statusCode = 200;
    }

    http_response_code((int)$statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

/*
    Prebere JSON body iz zahtevka
*/
function readJsonInput()
{
    $rawData = file_get_contents('php://input');

    if (!$rawData) {
        return array();
    }

    $decoded = json_decode($rawData, true);

    if (!is_array($decoded)) {
        return array();
    }

    return $decoded;
}

/*
    Vrne metodo zahtevka
*/
function getRequestMethod()
{
    return isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
}

/*
    Vrne ID iz query stringa
*/
function getRequestId()
{
    if (!isset($_GET['id']) || $_GET['id'] === '') {
        return null;
    }

    return (int)$_GET['id'];
}

/*
    Vrne iskalni niz iz query stringa
*/
function getSearchQuery()
{
    return isset($_GET['q']) ? trim($_GET['q']) : '';
}

/*
    405 Method Not Allowed
*/
function methodNotAllowed($allowedMethods)
{
    if (!empty($allowedMethods) && is_array($allowedMethods)) {
        header('Allow: ' . implode(', ', $allowedMethods));
    }

    jsonResponse(array(
        'success' => false,
        'message' => 'Method not allowed'
    ), 405);
}

/*
    400 Bad Request
*/
function badRequest($message)
{
    if ($message === null || $message === '') {
        $message = 'Bad request';
    }

    jsonResponse(array(
        'success' => false,
        'message' => $message
    ), 400);
}

/*
    404 Not Found
*/
function notFound($message)
{
    if ($message === null || $message === '') {
        $message = 'Not found';
    }

    jsonResponse(array(
        'success' => false,
        'message' => $message
    ), 404);
}

/*
    403 Forbidden
*/
function accessDenied($message)
{
    if ($message === null || $message === '') {
        $message = 'Access denied';
    }

    jsonResponse(array(
        'success' => false,
        'message' => $message
    ), 403);
}

/*
    500 Internal Server Error
*/
function serverError($message)
{
    if ($message === null || $message === '') {
        $message = 'Internal server error';
    }

    jsonResponse(array(
        'success' => false,
        'message' => $message
    ), 500);
}
?>