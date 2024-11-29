<?php
namespace App\helpers\utils;

class CronManager
{
    private static $instance = null;
    private $jobs = [];
    private $logFile;
    private $lastRunFile;

    private function __construct()
    {
        $this->logFile = dirname(__DIR__, 3) . '/logs/cron.log';
        $this->lastRunFile = dirname(__DIR__, 3) . '/logs/cron_last_run.json';
        $this->ensureDirectoryExists();
        $this->loadLastRun();
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function ensureDirectoryExists(): void
    {
        $logDir = dirname($this->logFile);
        if (!file_exists($logDir)) {
            mkdir($logDir, 0777, true);
        }
    }

    private function loadLastRun(): void
    {
        if (file_exists($this->lastRunFile)) {
            $this->jobs = json_decode(file_get_contents($this->lastRunFile), true) ?? [];
        }
    }

    private function saveLastRun(): void
    {
        file_put_contents($this->lastRunFile, json_encode($this->jobs));
    }

    public function addJob(string $name, string $expression, callable $callback): void
    {
        $this->jobs[$name] = [
            'expression' => $expression,
            'callback' => $callback,
            'last_run' => $this->jobs[$name]['last_run'] ?? null
        ];
    }

    public function shouldRunJob(string $name): bool
    {
        if (!isset($this->jobs[$name])) {
            return false;
        }

        $job = $this->jobs[$name];
        $lastRun = $job['last_run'] ? strtotime($job['last_run']) : 0;
        $now = time();

        list($minute, $hour, $day, $month, $weekday) = explode(' ', $job['expression']);
        
        $currentMinute = (int)date('i', $now);
        $currentHour = (int)date('H', $now);
        $currentDay = (int)date('d', $now);
        $currentMonth = (int)date('m', $now);
        $currentWeekday = (int)date('w', $now);

        if ($lastRun && ($now - $lastRun) < 60) {
            return false;
        }

        // Match cron expression
        if ($minute !== '*' && $currentMinute != (int)$minute) return false;
        if ($hour !== '*' && $currentHour != (int)$hour) return false;
        if ($day !== '*' && $currentDay != (int)$day) return false;
        if ($month !== '*' && $currentMonth != (int)$month) return false;
        if ($weekday !== '*' && $currentWeekday != (int)$weekday) return false;

        return true;
    }

    public function runJob(string $name): void
    {
        if (!isset($this->jobs[$name])) {
            $this->log("Job '$name' not found");
            return;
        }

        try {
            $job = $this->jobs[$name];
            $callback = $job['callback'];
            
            $this->log("Starting job '$name'");
            $startTime = microtime(true);
            
            $callback();
            
            $duration = round(microtime(true) - $startTime, 2);
            $this->log("Completed job '$name' in {$duration}s");
            
            $this->jobs[$name]['last_run'] = date('Y-m-d H:i:s');
            $this->saveLastRun();
            
        } catch (\Exception $e) {
            $this->log("Error in job '$name': " . $e->getMessage());
            if (class_exists('DebugBar')) {
                DebugBar::getInstance()->addException($e);
            }
        }
    }

    public function run(): void
    {
        foreach ($this->jobs as $name => $job) {
            if ($this->shouldRunJob($name)) {
                $this->runJob($name);
            }
        }
    }

    private function log(string $message): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] $message\n";
        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
        
        if (class_exists('DebugBar')) {
            DebugBar::getInstance()->addMessage("CRON: $message", 'info');
        }
    }

    public function getJobs(): array
    {
        return $this->jobs;
    }

    public function removeJob(string $name): void
    {
        if (isset($this->jobs[$name])) {
            unset($this->jobs[$name]);
            $this->saveLastRun();
            $this->log("Removed job '$name'");
        }
    }
}
