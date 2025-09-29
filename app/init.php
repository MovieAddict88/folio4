<?php

session_start();

if (!file_exists(__DIR__ . '/config/config.php')) {
    header('Location: ../setup.php');
    exit;
}

require_once 'config/config.php';
require_once '../vendor/autoload.php';

require_once 'core/App.php';
require_once 'core/Controller.php';
require_once 'core/Database.php';
require_once 'core/Flasher.php';