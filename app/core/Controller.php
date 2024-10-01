<?php
    namespace app\core;
    use \App;
    use app\core\Registry;
    class Controller{
        private $layout = null;
        private $config;
        function view($view,$data=[])
    {
        require_once "./app/views/".$view.".php";
    }
    function models($models){
        require_once "./app/models/".$models.".php";
        return new $models;
    }
    function viewFrontEnd($view,$data=[])
    {
        foreach($data as $key => $value){
            $$key = $value;
        }
        //require_once "./app/views/".$view.".php";
        require_once "./views/".$view.".php";
    }
        public function __construct()
        {
            $this->config = Registry::getInstance()->config;
            $this->layout = $this->config['layout'];
        }

        public function setLayout($layout){
            $this->layout = $layout;
        }

        public function redirect($url,$isEnd=true,$resPonseCode=302){
            header('Location:'.$url,true,$resPonseCode);
            if($isEnd)
                die();
        }

        public function render($view,$data=null){
            //$controller = App::getController();
           // $folderView = strtolower(str_replace('Controller','',$controller));
            $rootDir = $this->config['rootDir'];
            $content = $this->getViewContent($view,$data);
            if($this->layout !== null){
                $layoutPath = $rootDir.'/views'.'/'.$this->layout.'.php';
                if(file_exists($layoutPath)){
                require($layoutPath);
            }
            }
            // echo $rootDir;
            // echo $view;
            // $viewPath = $rootDir.'/views'.'/'.$view.'.php';
            // echo $viewPath;
            // if(file_exists($viewPath)){
            //     require($viewPath);
            // }
        }

        public function getViewContent($view,$data){
            $controller = Registry::getInstance()->controllers;
            // $folderView = strtolower(str_replace('Controller','',$controller));
             $rootDir = $this->config['rootDir'];
            if(is_array($data))
                extract($data, EXTR_PREFIX_SAME, "data");
            else
                $data = $data;
             $viewPath = $rootDir.'/views'.'/'.$view.'.php';
             if(file_exists($viewPath)){
                ob_start();
                require($viewPath);
                return ob_get_clean();
            }
        }

        public function renderPartial($view,$data){
            $rootDir = $this->config['rootDir'];
            if(is_array($data))
                extract($data, EXTR_PREFIX_SAME, "data");
            else
                $data = $data;
             $viewPath = $rootDir.'/views'.'/'.$view.'.php';
             if(file_exists($viewPath)){
                ob_start();
                require($viewPath);
                return ob_get_clean();
            }
        }

    }
    
?>