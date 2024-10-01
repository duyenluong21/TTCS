<?php
    namespace app\controllers;
    use app\core\Controller;
    use \App;

    class securityController extends Controller{
        function __construct()
        {
            parent::__construct();
        }
        public function index(){
           $this->render('view_admin/security');
            //echo App::getController();
            // echo App::getAction();
        }

    }
?>