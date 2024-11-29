<?php 

namespace App\helpers\utils;

class TimeChecker
{
    public $start_time = 0;
    private $last_time = 0;

    public function __construct()
    {
        $this->start_time = microtime(true);
        $this->last_time = $this->start_time;
    }

    public function checkTime()
    {
        $current_time = microtime(true);
        $total_time = $current_time - $this->start_time;
        $step_time = $current_time - $this->last_time;
        
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $caller = isset($trace[1]) ? $trace[1] : $trace[0];
        
        $class = isset($caller['class']) ? $caller['class'] : 'Global Scope';
        $type = isset($caller['type']) ? $caller['type'] : '';
        $function = isset($caller['function']) ? $caller['function'] : '';
        $file = isset($caller['file']) ? basename($caller['file']) : '';
        $line = isset($caller['line']) ? $caller['line'] : '';
        
        echo sprintf(
            "[%s::%s%s] at %s:%d | Step: %.6fs | Total: %.6fs\n<br>",
            $class,
            $type,
            $function,
            $file,
            $line,
            $step_time,
            $total_time
        );
        
        $this->last_time = $current_time;
    }

    public function restart()
    {
        $this->start_time = microtime(true);
        $this->last_time = $this->start_time;
    }
    
    public function getDetailedTrace()
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $output = "";
        foreach ($trace as $index => $t) {
            if ($index == 0) continue;
            
            $class = isset($t['class']) ? $t['class'] : 'Global Scope';
            $type = isset($t['type']) ? $t['type'] : '';
            $function = isset($t['function']) ? $t['function'] : '';
            $file = isset($t['file']) ? basename($t['file']) : '';
            $line = isset($t['line']) ? $t['line'] : '';
            
            $output .= sprintf(
                "#%d %s::%s%s called at [%s:%d]\n<br>",
                $index,
                $class,
                $type,
                $function,
                $file,
                $line
            );
        }
        return $output;
    }
}