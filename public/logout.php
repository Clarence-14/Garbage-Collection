<?php
require_once __DIR__ . '/../src/bootstrap.php';
use Src\Auth\Auth;

$auth = new Auth();
$auth->logout();
redirect('/');
