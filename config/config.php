<?php
// Configuration file
session_start();

// Enable error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define base path
define('BASE_PATH', dirname(dirname(__FILE__)));
define('ADMIN_PATH', BASE_PATH . '/admin');

// Database file paths
define('DB_PATH', BASE_PATH . '/database');
define('JOBS_DB', DB_PATH . '/jobs.json');
define('USERS_DB', DB_PATH . '/users.json');
define('COMPANIES_DB', DB_PATH . '/companies.json');

// Default admin credentials
define('DEFAULT_ADMIN_EMAIL', 'admin@zewedjobs.com');
define('DEFAULT_ADMIN_PASSWORD', 'admin123'); // Change in production

// Initialize JSON files if they don't exist
function initDatabase() {
    $files = [
        JOBS_DB => ['jobs' => []],
        USERS_DB => [
            'users' => [
                [
                    'id' => 1,
                    'name' => 'Administrator',
                    'email' => DEFAULT_ADMIN_EMAIL,
                    'password' => password_hash(DEFAULT_ADMIN_PASSWORD, PASSWORD_DEFAULT),
                    'role' => 'admin',
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ]
        ],
        COMPANIES_DB => ['companies' => []]
    ];
    
    foreach ($files as $file => $defaultData) {
        if (!file_exists($file)) {
            file_put_contents($file, json_encode($defaultData, JSON_PRETTY_PRINT));
        }
    }
}

// Initialize database on first run
initDatabase();

// Helper functions
function isLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function getJsonData($file) {
    if (!file_exists($file)) {
        return [];
    }
    $json = file_get_contents($file);
    return json_decode($json, true);
}

function saveJsonData($file, $data) {
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}

function generateId() {
    return uniqid() . '_' . time();
}

function logError($message) {
    $logFile = BASE_PATH . '/logs/errors.log';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}
?>
