<?php

function jsonResponse($success, $data = null, $message = '', $code = 200)
{
    http_response_code($code);

    header('Content-Type: application/json; charset=utf-8');

    echo json_encode([
        'success' => $success,
        'data'    => $data,
        'message' => $message,
    ]);

    exit;
}

function jsonSuccess($data = null, $message = 'Opération réussie', $code = 200)
{
    jsonResponse(true, $data, $message, $code);
}

function jsonError($message = 'Une erreur est survenue', $code = 400)
{
    jsonResponse(false, null, $message, $code);
}
