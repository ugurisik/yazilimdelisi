<?php




class System
{

    protected $controller = 'main';
    protected $method = 'index';
    protected $params = [];
    protected $adminController = ADMIN_CONTROLLER;
    protected $controllerPath = CONTROLLER;


    public function run()
    {
        $url = self::parseUrl();
        $url = self::checkFile($url);
        $url = self::checkClass($url);
        $url = self::checkMethod($url);
        self::clearUrl($url);
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    public function clearUrl($url = [])
    {
        if (isset($url[0]) && isset($url[1])) {
            if ($url[0] == $this->controller && $url[1] == $this->method) {
                array_shift($url);
                array_shift($url);
            }
        }
        $this->params = $url;
    }


    public function parseUrl()
    {
        if (isset($_GET['action'])) {
            $url = explode('/', filter_var(rtrim(
                $_GET['action'],
                '/'
            ), FILTER_SANITIZE_URL));
        } else {
            $url[0] = $this->controller;
            $url[1] = $this->method;
        }
        return $url;
    }

    public function checkFile($url = [])
    {
        if ($url[0] == $this->adminController) {
            $controllerPath = $this->adminController;
        } else {
            $controllerPath = $this->controllerPath;
        }
        if (file_exists($controllerPath . $url[0] . '.php')) {
            $this->controller = $url[0];
            array_shift($url);
            require_once $controllerPath . $this->controller . '.php';
        } else {
            require_once $controllerPath . $this->controller . '.php';
        }
        return $url;
    }

    public function checkClass($url = [])
    {
        if (class_exists($this->controller)) {
            $this->controller = new $this->controller;
        } else {
            echo 'sınıf bulunamadı';
        }
        return $url;
    }

    public function checkMethod($url = [])
    {
        if (isset($url[0])) {
            if (method_exists($this->controller, $url[0])) {
                $this->method = $url[0];
                array_shift($url);
            }
        }
        return $url;
    }
}
