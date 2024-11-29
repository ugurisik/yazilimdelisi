<?php

// DATABASE CONFIGURATION
define('DB_HOST', 'localhost');
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
define('ENCRYPTION_KEY', base64_encode(md5('123456789AAZZSSDD')));
define('DEBUG', true);