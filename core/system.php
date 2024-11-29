<?php

use App\helpers\utils\SystemException;
use App\helpers\utils\session;
use App\middlewares\RateLimitMiddleware;

class System
{

    protected $controller = 'main';
    protected $method = 'index';
    protected $params = [];
    protected $adminController = ADMIN_CONTROLLER;
    protected $controllerPath = CONTROLLER;
    protected $allowedCharacters = '/^[a-zA-Z0-9_]+$/';

    public function run()
    {
        try {
            session::getInstance()->start();
            $rateLimiter = new RateLimitMiddleware();
            $rateLimiter->handle();

            $url = $this->parseUrl();
            $url = $this->validateUrl($url);
            $url = $this->checkFile($url);
            $url = $this->checkClass($url);
            $url = $this->checkMethod($url);
            $this->clearUrl($url);
            call_user_func_array([$this->getController(), $this->getMethod()], $this->getParams());
        } catch (SystemException $e) {
            error_log($e->getMessage());
            header("HTTP/1.0 404 Not Found");
            echo "Sayfa bulunamadı.";
        }
    }

    protected function validateUrl($url)
    {
        if (!is_array($url)) {
            throw new SystemException("Geçersiz URL formatı");
        }

        foreach ($url as $segment) {
            if (!preg_match($this->allowedCharacters, $segment)) {
                throw new SystemException("URL'de geçersiz karakterler bulundu");
            }
        }

        return $url;
    }

    public function clearUrl($url = [])
    {
        if (isset($url[0]) && isset($url[1])) {
            if ($url[0] == $this->getController() && $url[1] == $this->getMethod()) {
                array_shift($url);
                array_shift($url);
            }
        }
        $this->setParams($url);
    }

    public function parseUrl()
    {
        if (isset($_GET['action'])) {
            $url = explode('/', filter_var(rtrim(
                $_GET['action'],
                '/'
            ), FILTER_SANITIZE_URL));
        } else {
            $url[0] = $this->getController();
            $url[1] = $this->getMethod();
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
            $this->setController($url[0]);
            array_shift($url);
            require_once $controllerPath . $this->getController() . '.php';
        } else {
            require_once $controllerPath . $this->getController() . '.php';
        }
        return $url;
    }

    public function checkClass($url = [])
    {
        if (class_exists($this->getController())) {
            $this->setController(new $this->controller);
        } else {
            throw new SystemException("Controller sınıfı bulunamadı: " . $this->getController());
        }
        return $url;
    }

    public function checkMethod($url = [])
    {
        if (isset($url[0])) {
            if (method_exists($this->getController(), $url[0])) {
                if ($this->isMethodCallable($url[0])) {
                    $this->setMethod($url[0]);
                    array_shift($url);
                } else {
                    throw new SystemException("Method çağrılamaz: " . $url[0]);
                }
            }
        }
        return $url;
    }

    protected function isMethodCallable($method)
    {
        $reflection = new ReflectionMethod($this->getController(), $method);
        return $reflection->isPublic();
    }

    public function getController()
    {
        return $this->controller;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function setParams($params)
    {
        $this->params = $params;
    }

    public function setController($controller)
    {
        $this->controller = $controller;
    }

    public function setMethod($method)
    {
        $this->method = $method;
    }

}
