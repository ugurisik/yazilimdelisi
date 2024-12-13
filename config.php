<?php
date_default_timezone_set('Europe/Istanbul');

// DATABASE CONFIGURATION
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'yazilimdelisi');
define('DB_USER', 'yazilimdelisi');
define('DB_PASS', 'yazilimdelisi');
define('DB_CHARSET', 'utf8');
define('DB_PORT', 3306);



// SYSTEM CONFIGURATION
define('CONTROLLER', 'app/controllers/');
define('ADMIN_CONTROLLER', 'app/controllers/admin/');
define('MODEL', 'app/models/');
define('VIEW_PATH', 'templates/');
define('PUBLIC_PATH', 'public/');
define('DEFAULT_THEME', 'user');
define("ADMIN_URI", "admin555");
define('ENCRYPTION_KEY', base64_encode(md5('123456789AAZZSSDD')));
define("SITE_URL", (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]/yazilimdelisi");
define('DEBUG', true);

define("FILE_UPLOAD_MAX_SIZE_MB",  3);
define("TABLE_DATA_COUNT", 10);