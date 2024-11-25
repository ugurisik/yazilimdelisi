<?php

class Controller
{



    public function view($theme, $file, $parameters = [], $isIncFiles = true)
    {
        if (file_exists(VIEW_PATH . $theme . '/' . $file . '.php')) {
            if ($isIncFiles) {
                require VIEW_PATH . $theme . '/inc/header.php';
            }

            require VIEW_PATH . $theme . '/' . $file . '.php';

            if ($isIncFiles) {
                require VIEW_PATH . $theme . '/inc/footer.php';
            }
        } else {
            echo 'View not found';
        }
    }
}
