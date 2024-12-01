<?php
namespace App\helpers\utils;

use DebugBar\StandardDebugBar;
use Exception;
// Her zaman view çağrılmadan önce kullanılmalı!
class DebugBar
{
    private $debugBar;
    private $renderer;
    private static $instance = null;
    private $isRendered = false; 

    private function __construct()
    {
        $this->debugBar = new StandardDebugBar();
        $this->renderer = $this->debugBar->getJavascriptRenderer()
            ->setBaseUrl(SITE_URL . '/vendor/maximebf/debugbar/src/DebugBar/Resources/');
        
        if (!$this->debugBar->hasCollector('memory')) {
            $this->debugBar->addCollector(new \DebugBar\DataCollector\MemoryCollector());
        }
        if (!$this->debugBar->hasCollector('request')) {
            $this->debugBar->addCollector(new \DebugBar\DataCollector\RequestDataCollector());
        }
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function renderHead(): void
    {
        $this->isRendered = true;
        echo $this->renderer->renderHead();
    }

    public function render(): void
    {
        $this->isRendered = true;
        echo $this->renderer->render();
    }

    public function hasRendered()
    {
        return $this->isRendered;
    }

    public function startMeasure(string $name, ?string $label = null): void
    {
        $this->debugBar['time']->startMeasure($name, $label);
    }

    public function stopMeasure(string $name): void
    {
        $this->debugBar['time']->stopMeasure($name);
    }

    public function addMessage($message, string $label = 'info'): void
    {
        $this->debugBar['messages']->addMessage($message, $label);
    }

    public function addException(Exception $exception): void
    {
        $this->debugBar['exceptions']->addException($exception);
    }

    public function addQuery(string $query, array $params = [], float $duration = null): void
    {
        $queryData = [
            'sql' => $query,
            'params' => $params,
            'duration' => $duration,
            'duration_str' => $duration ? sprintf('%.2f ms', $duration * 1000) : null
        ];
        $this->debugBar['queries']->addQuery($queryData);
    }

    public function addRoute(array $routeData): void
    {
        $this->debugBar['routes']->addRoute($routeData);
    }

    public function addCustomData(string $key, $value): void
    {
        if (!isset($this->debugBar['custom_data'])) {
            $this->debugBar->addCollector(new \DebugBar\DataCollector\MessagesCollector('custom_data'));
        }
        $this->debugBar['custom_data']->addMessage([$key => $value]);
    }

    public function measure(string $label, callable $callback)
    {
        $this->startMeasure($label);
        $result = $callback();
        $this->stopMeasure($label);
        return $result;
    }

    public function getDebugBar()
    {
        return $this->debugBar;
    }

    
}