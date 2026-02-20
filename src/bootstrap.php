<?php
// src/bootstrap.php

// Define project root
define('PROJECT_ROOT', dirname(__DIR__));

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set secure HTTP headers
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');

// Load environment variables from .env
$envFile = PROJECT_ROOT . '/.env';
if (file_exists($envFile)) {
    $envVariables = parse_ini_file($envFile, false, INI_SCANNER_RAW);
    if ($envVariables) {
        foreach ($envVariables as $key => $value) {
            $_ENV[$key] = $value;
        }
    }
}

// Load Config
require_once PROJECT_ROOT . '/src/Config/Database.php';

// Load Utils
require_once PROJECT_ROOT . '/src/Utils/Helpers.php';

// Load Auth
require_once PROJECT_ROOT . '/src/Auth/Auth.php';

// Autoloader for namespaced classes (e.g. Src\Models\User -> src/Models/User.php)
spl_autoload_register(function ($class) {
    // Prefix for our project classes
    $prefix = 'Src\\';
    
    // Base directory for the namespace prefix
    $base_dir = PROJECT_ROOT . '/src/';
    
    // Does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // no, move to the next registered autoloader
        return;
    }
    
    // Get the relative class name
    $relative_class = substr($class, $len);
    
    // Replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    // If the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});
