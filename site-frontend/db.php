<?php

$databaseConfig = [
    'host' => '127.0.0.1',
    'database' => 'loja_db',
    'username' => 'loja_app',
    'password' => '',
];

$localConfigPath = __DIR__ . '/config/database.local.php';
if (is_file($localConfigPath)) {
    $localConfig = require $localConfigPath;
    if (!is_array($localConfig)) {
        error_log('LojaInfoWeb: database.local.php must return an array.');
        http_response_code(503);
        exit('Serviço temporariamente indisponível.');
    }
    $databaseConfig = array_merge($databaseConfig, $localConfig);
}

foreach ([
    'host' => 'DB_HOST',
    'database' => 'DB_NAME',
    'username' => 'DB_USER',
    'password' => 'DB_PASSWORD',
] as $key => $environmentVariable) {
    $value = getenv($environmentVariable);
    if ($value !== false) {
        $databaseConfig[$key] = $value;
    }
}

foreach (['host', 'database', 'username', 'password'] as $requiredKey) {
    if (!isset($databaseConfig[$requiredKey]) || !is_string($databaseConfig[$requiredKey])) {
        error_log('LojaInfoWeb: database configuration is missing or invalid.');
        http_response_code(503);
        exit('Serviço temporariamente indisponível.');
    }
}

if ($databaseConfig['host'] === '' || $databaseConfig['database'] === '' || $databaseConfig['username'] === '') {
    error_log('LojaInfoWeb: database configuration is incomplete.');
    http_response_code(503);
    exit('Serviço temporariamente indisponível.');
}

mysqli_report(MYSQLI_REPORT_OFF);

try {
    $connect = new mysqli(
        $databaseConfig['host'],
        $databaseConfig['username'],
        $databaseConfig['password'],
        $databaseConfig['database']
    );
    if ($connect->connect_errno !== 0) {
        error_log('LojaInfoWeb database connection failed: ' . $connect->connect_error);
        http_response_code(503);
        exit('Serviço temporariamente indisponível.');
    }
    if (!$connect->set_charset('utf8mb4')) {
        error_log('LojaInfoWeb: could not set the connection charset.');
        http_response_code(503);
        exit('Serviço temporariamente indisponível.');
    }
} catch (mysqli_sql_exception $exception) {
    error_log('LojaInfoWeb database connection failed: ' . $exception->getMessage());
    http_response_code(503);
    exit('Serviço temporariamente indisponível.');
}
