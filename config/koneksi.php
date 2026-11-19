<?php
$privateConfig = __DIR__ . '/config.php';
if (is_file($privateConfig)) {
    require $privateConfig;
} else {
    define('DB_HOST', 'sql208.infinityfree.com');
    define('DB_USER', 'if0_42247619');
    define('DB_PASS', 'j0NApkvIZTmuK');
    define('DB_NAME', 'if0_42247619_marketplace');
    define('BASE_URL', '/marketplace'); 
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    die('Koneksi database gagal. Periksa pengaturan pada config/config.php.');
}
