<?php


use App\helpers\utils\Security;
use App\helpers\utils\ControllerException;

class Controller
{
    protected $viewData = [];
    protected $theme;

    public function __construct()
    {
        $this->viewData['csrf_token'] = Security::getInstance()->getCSRFToken();
    }

    public function view($theme, $file, $params = [], $isIncFiles = true)
    {
        try {
            $this->validateViewFile($theme, $file);
            $params = Security::escapeArray($params);
            $this->viewData = array_merge($this->viewData, $params);
            
            extract($this->viewData);
            
            ob_start();
            $params = $this->getData();
            if ($isIncFiles) {
                $this->includeHeader($theme);
            }

            require $this->getViewPath($theme, $file);

            if ($isIncFiles) {
                $this->includeFooter($theme);
            }
            
            echo ob_get_clean();
            
        } catch (ControllerException $e) {
            error_log($e->getMessage());
            throw new ControllerException("View yüklenirken hata oluştu: " . $e->getMessage());
        }
    }

  
    public function setDataBatch(array $data)
    {
        $this->viewData = array_merge($this->viewData, Security::escapeArray($data));
        return $this;
    }

    public function setData($key, $value)
    {
        $this->viewData[$key] = Security::escapeArray($value);
        return $this;
    }

    public function getData($key = null)
    {
        if ($key === null) {
            return $this->viewData;
        }
        return isset($this->viewData[$key]) ? $this->viewData[$key] : null;
    }

   
    public function jsonResponse($data, $statusCode = 200)
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }

   
    protected function validateViewFile($theme, $file)
    {
        $viewPath = $this->getViewPath($theme, $file);
        if (!file_exists($viewPath)) {
            throw new ControllerException("View dosyası bulunamadı: {$viewPath}");
        }
    }

    
    protected function includeHeader($theme)
    {
        $headerPath = VIEW_PATH . $theme . '/inc/header.php';
        if (file_exists($headerPath)) {
            require $headerPath;
        } else {
            throw new ControllerException("Header dosyası bulunamadı: {$headerPath}");
        }
    }

    
    protected function includeFooter($theme)
    {
        $footerPath = VIEW_PATH . $theme . '/inc/footer.php';
        if (file_exists($footerPath)) {
            require $footerPath;
        } else {
            throw new ControllerException("Footer dosyası bulunamadı: {$footerPath}");
        }
    }

    
    protected function getViewPath($theme, $file)
    {
        return VIEW_PATH . $theme . '/' . $file . '.php';
    }
}
