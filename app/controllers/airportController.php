<?php
    namespace app\controllers;
    use app\core\Controller;
    use \App;

    class airportController extends Controller{
        function __construct()
        {
            parent::__construct();
        }
        public function index(){
           $this->render('view_admin/airport');
            //echo App::getController();
            // echo App::getAction();
        }

    }
?>