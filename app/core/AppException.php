<?php
    namespace app\core;

use \Exception;

    class AppException extends Exception{
        public function __construct($message,$code = null)
        {;

            set_exception_handler([$this,'error_handle']);
            parent::__construct($message,$code);
        }

        public function error_handle($e){
            echo "<h1>{$e->getCode()} => {$e->getMessage()}</h1>";
            echo "<h2>{$e->getFile()} => {$e->getLine()}</h2>";
            echo "<p>{$e->getTraceAsString()}</p>";

            foreach($e -> getTrace() as $trace){
                $file = isset($trace['file']) ? $trace['file'] : '';
                $line = isset($trace['line']) ? $trace['line'] : '';
                $class = isset($trace['class']) ? $trace['class'] : '';
                $function = isset($trace['function']) ? $trace['function'] : '';

                echo "<h4>File: {$file}</h4>";
                echo "<h4>File: {$line}</h4>";
                echo "<h4>File: {$class}</h4>";
                echo "<h4>File: {$function}</h4>";
                echo "<hr/>";

            }
        }
    }
?>