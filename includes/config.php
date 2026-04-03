<?php
declare(strict_types=1);

$DB_HOST = 'localhost';
$DB_NAME = 'bullbrothers';
$DB_USER = 'root';
$DB_PASS = '';
$DB_CHARSET = 'utf8mb4';

$local = __DIR__ . '/config.local.php';
if (is_readable($local)) {
    require $local;
}
