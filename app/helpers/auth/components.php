<?php

namespace App\helpers\auth;

use App\helpers\utils\session;
use Core\Mysql;
use App\helpers\utils\Logger;
use DOMDocument;

class Components
{
    private static $instance = null;
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function check($compName)
    {
        $user = session::getInstance()->get('user_data');
        $user_id = $user['role_guid'];
        $user_company = $user['company_guid'];
        $comp = Mysql::getInstance()->get('auth_components', ['compName' => $compName, 'roleGuid' => $user_id, 'companyGuid' => $user_company]);
        if ($comp) {
            return true;
        }
        return false;
    }

    public function addComponents()
    {

        $themePath = dirname(__DIR__, 3) . '\\templates\\' . DEFAULT_THEME . '\\';

        $fileList = glob($themePath . '*.php');

        foreach ($fileList as $file) {
            $content = file_get_contents($file);

            // compName ve compTitle attributelarını bul
            preg_match_all('/<([a-zA-Z0-9]+)[^>]*compName=["\'](.*?)["\'][^>]*compTitle=["\'](.*?)["\']/i', $content, $matches);

            if (!empty($matches[0])) {
                $fileName = basename($file);
                echo "File: " . $fileName . "\n";

                for ($i = 0; $i < count($matches[0]); $i++) {
                    $tagName = $matches[1][$i];    // Element tipi (button, div vs.)
                    $compName = $matches[2][$i];   // compName değeri
                    $compTitle = $matches[3][$i];  // compTitle değeri

                    echo "Tag: <" . $tagName . ">, ";
                    echo "Component Name: " . $compName . ", ";
                    echo "Title: " . $compTitle . "<br>";
                }
            }
        }
    }
}
