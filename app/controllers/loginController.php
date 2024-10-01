<?php
 namespace app\controllers;
 use app\core\Controller;
 use \App;
	class loginController extends Controller {
		public function __construct(){
			parent::__construct();	        

                }

                public function login(){
                    $this->render('view_admin/login');
                     //echo App::getController();
                     // echo App::getAction();
                 }
            }
?>