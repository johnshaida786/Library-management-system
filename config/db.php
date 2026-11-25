<?php
require_once __DIR__ . '/config.php';

$dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// TiDB Cloud requires TLS. Use the given CA file, or find the system CA bundle.
$ca = DB_SSL_CA;
if ($ca === '') {
    foreach (['/etc/pki/tls/certs/ca-bundle.crt',        // Amazon Linux / RHEL
              '/etc/ssl/certs/ca-certificates.crt',      // Ubuntu / Debian
              '/etc/ssl/cert.pem'] as $candidate) {
        if (is_readable($candidate)) { $ca = $candidate; break; }
    }
}
if ($ca !== '') {
    $options[PDO::MYSQL_ATTR_SSL_CA] = $ca;
    $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = (getenv('DB_SSL_VERIFY') !== '0');
}

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    http_response_code(500);
    error_log('DB connection failed: ' . $e->getMessage());
    exit('Database connection failed. Check config/config.php and your TiDB IP access list.');
}
