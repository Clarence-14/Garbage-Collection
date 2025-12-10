<?php
// src/Utils/Helpers.php

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function jsonResponse($data, $status = 200) {
    header('Content-Type: application/json');
    http_response_code($status);
    echo json_encode($data);
    exit();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function currentUser() {
    return isset($_SESSION['user']) ? $_SESSION['user'] : null;
}

function hasRole($role) {
    return isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === $role;
}

function sendMockEmail($to, $subject, $message) {
    // In a real app, use mail() or PHPMailer.
    // For this prototype, we log to a file.
    $logEntry = "[" . date('Y-m-d H:i:s') . "] To: $to | Subject: $subject | Message: $message" . PHP_EOL;
    file_put_contents(__DIR__ . '/../../logs/email.log', $logEntry, FILE_APPEND);
}
