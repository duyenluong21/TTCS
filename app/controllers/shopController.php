<?php
    namespace app\controllers;
    use app\core\Controller;
    use \App;

    class shopController extends Controller{
        function __construct()
        {
            parent::__construct();
        }
        public function index(){
           $this->render('view_admin/shop');
        }

    }
?>