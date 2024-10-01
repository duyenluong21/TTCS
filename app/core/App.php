<?php
// require_once(dirname(__FILE__).'/Router.php');
// require_once(dirname(__FILE__).'/../controllers/homeController.php');

use app\core\Registry;

require_once(dirname(__FILE__).'/Autoload.php');
class App{
    private $router;

    function __construct($config)
    {
        new Autoload($config['rootDir']);
        $this->router = new Router($config['basePath']);
        
        Registry::getInstance()->config = $config;
    }
    public function run(){
        $this->router->run();
        // echo 'App running';
    }
}
?>