<?php
    require_once(dirname(__FILE__).'../app/core/app.php');
    $config = require_once(dirname(__FILE__).'/db/config.php');
    
    $app = new App($config);
    $app->run();
?>