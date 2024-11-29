<?php

namespace App\helpers\utils;

if (!class_exists('\App\helpers\utils\SecurityException')) {
    class SecurityException extends \Exception {
        public function __construct($message, $code = 403) {
            parent::__construct($message, $code);
        }
    }
}

if (!class_exists('\App\helpers\utils\ControllerException')) {
    class ControllerException extends \Exception {
        public function __construct($message, $code = 404) {
            parent::__construct($message, $code);
        }
    }
}

if (!class_exists('\App\helpers\utils\SessionException')) {
    class SessionException extends \Exception {
        public function __construct($message, $code = 500) {
            parent::__construct($message, $code);
        }
    }
}

if (!class_exists('\App\helpers\utils\HashException')) {
    class HashException extends \Exception {
        public function __construct($message, $code = 500) {
            parent::__construct($message, $code);
        }
    }
}

if (!class_exists('\App\helpers\utils\SystemException')) {
    class SystemException extends \Exception {
        public function __construct($message, $code = 500) {
            parent::__construct($message, $code);
        }
    }
}