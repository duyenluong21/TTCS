<?php
    namespace app\controllers;
    use app\core\Controller;
    use \App;

    class homeController extends Controller{
        function __construct()
        {
            parent::__construct();
        }
        public function index(){
           $this->render('view_admin/home');
            //echo App::getController();
            // echo App::getAction();
        }
        public function passenger(){
            $this->render('models/passengerModel');
        }

        public function khachhang(){
            $this->render('view_admin/detailPassenger');
        }

        public function detail($maKH = ''){
            $passenger = $this->models('Passenger');
            $passenger->maKH = $maKH;
            $this->viewFrontEnd('view_admin/detailPassenger',['maKH' => $passenger->maKH]);
        }

    }
?>