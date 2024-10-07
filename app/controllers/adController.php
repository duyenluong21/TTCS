<?php
    namespace app\controllers;
    use app\core\Controller;
    use \App;

    class adController extends Controller{
        function __construct()
        {
            parent::__construct();
        }
        public function index(){
           $this->render('view_admin/advertisement');
            //echo App::getController();
            // echo App::getAction();
        }

    }
?>