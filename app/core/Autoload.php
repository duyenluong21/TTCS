<?php
use app\core\AppException;
    class Autoload{
        private $rootDir;
        function __construct($rootDir)
        {
            $this->rootDir = $rootDir;
            spl_autoload_register([$this,'autoload']);
            $this->autoLoadFile();
        }
        private function autoload($class){
            $tmp = explode('\\',$class);
            $fileName = end($tmp);
            // $fileName = end(explode('\\',$class));
            $filePath = str_replace($fileName,'',$class);
            $filePath = $this->rootDir.'\\'.strtolower(str_replace($fileName,'',$class)).$fileName.'.php';
            
            if(file_exists($filePath)){
                require_once($filePath);
            }else{
                throw new AppException("$class does not exists");
                //die("$class not exists");
            }
        }

        private function autoLoadFile(){
            foreach($this->defaultFileLoad() as $file){
                require_once($this->rootDir.'/'.$file);
            }
        }

        private function defaultFileLoad(){
            return[
                'app/core/Router.php',
                'app/routers.php'
            ];
        }
    }
?>