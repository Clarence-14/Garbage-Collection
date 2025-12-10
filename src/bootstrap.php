<?php
// src/bootstrap.php

// Define project root
define('PROJECT_ROOT', dirname(__DIR__));

// Load Config
require_once PROJECT_ROOT . '/src/Config/Database.php';

// Load Utils
require_once PROJECT_ROOT . '/src/Utils/Helpers.php';

// Load Auth
require_once PROJECT_ROOT . '/src/Auth/Auth.php';

// Load Models (Auto-load later preferred, but manual for now)
// require_once PROJECT_ROOT . '/src/Models/User.php';
