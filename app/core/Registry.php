<?php
    namespace app\core;
    class Registry{
        private static $instance;
        private $storage;

        private function __construct()
        {
            
        }

        public static function getInstance(){
            if(!isset(self::$instance))
                self::$instance = new self;
            return self::$instance;
        }

        public function __set($name,$value){
            if(!isset($this->storage[$name]))
                $this->storage[$name] = $value;
            else{
                die("can't not set \"$value\" to \"$name\", $name already exists");
            }
        }

        public function __get($name){
            if(isset($this->storage[$name]))
                return $this->storage[$name];
            return null;
        }
    }
?>