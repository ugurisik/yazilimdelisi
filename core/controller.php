<?php


use App\helpers\utils\Security;
use App\helpers\utils\ControllerException;
use App\helpers\utils\DebugBar;
use App\helpers\utils\session;

class Controller
{
    protected $viewData = [];
    protected $theme;

    public function __construct()
    {
        $this->viewData['csrf_token'] = Security::getInstance()->getCSRFToken();
    }

    public function view($theme, $file, $params = [], $isIncFiles = true, $adminAuth = true, $userAuth = true)
    {
        try {

            if($adminAuth && $this->checkAdminAuth() == false){
                header('Location: ' . SITE_URL);
                exit();
            }

            if($userAuth && $this->checkUserAuth() == false){
                header('Location: ' . SITE_URL);
                exit();
            }

            $debugBar = DebugBar::getInstance();
            $debugBar->startMeasure('view_render', 'View Rendering');
            $this->getSessionMessage($debugBar);
            $params = Security::escapeArray($params);
            $this->viewData = array_merge($this->viewData, $params);
            
            extract($this->viewData);
            
            ob_start();
            $params = $this->getData();
            if ($isIncFiles) {
                require $this->includeHeader($theme);
            }
            $debugBar->renderHead();

            require $this->getViewPath($theme, $file);
            
            if ($isIncFiles) {
                require $this->includeFooter($theme);
            }

            echo "<input type='hidden' id='csrf_token' value='" . Security::getInstance()->getCSRFToken() . "' />";
            $debugBar->stopMeasure('view_render');
            $debugBar->render();
            
            echo ob_get_clean();
            
        } catch (ControllerException $e) {
            error_log($e->getMessage());
            throw new ControllerException("View yüklenirken hata oluştu: " . $e->getMessage());
        }
    }

    public function checkAdminAuth(){
        return true;
    }

    public function checkUserAuth(){
        return true;
    }

    public function getSessionMessage($debugBar){
        $message = session::getInstance()->get('debugbar');
        if($message){
            foreach($message['messages'] as $message){
                $debugBar->addMessage($message);
            }
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
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }


    
    protected function includeHeader($theme)
    {
        $headerPath = VIEW_PATH . $theme . '/inc/header.php';
        if (file_exists($headerPath)) {
            return $headerPath;
        } else {
            throw new ControllerException("Header dosyası bulunamadı: {$headerPath}");
        }
    }

    
    protected function includeFooter($theme)
    {
        $footerPath = VIEW_PATH . $theme . '/inc/footer.php';
        if (file_exists($footerPath)) {
            return $footerPath;
        } else {
            throw new ControllerException("Footer dosyası bulunamadı: {$footerPath}");
        }
    }

    
    protected function getViewPath($theme, $file)
    {
        if (file_exists(VIEW_PATH . $theme . '/' . $file . '.php')) {
            return VIEW_PATH . $theme . '/' . $file . '.php';
        }else{
            return VIEW_PATH . 'error_404_inner.php';
        }
        
    }
}
