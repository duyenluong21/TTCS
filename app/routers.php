<?php
 use app\core\Controller;
 use app\core\QueryBuilder;
 use app\api;
 
   Router::get('/login','loginController@login');
   Router::get('/home','homeController@index');
  // Router::post('/api/airline',function(){

  // });
    Router::get('/detail/{id}','homeController@detail');
    Router::any('/airline','airlineController@index');
    Router::any('/airport','airportController@index');
    Router::get('/flights','flightsController@index');
    Router::any('/user','userController@index');
    Router::any('/booked','bookedController@index');
    Router::any('/passenger','passengerController@index');
    Router::any('/voucher','voucherController@index');
    Router::any('/security','securityController@index');
    Router::any('/statistical','statisticalController@index');
    Router::any('/ad','adController@index');
    Router::any('/shop','shopController@index');
   //  Router::get('/login',function(){
   //     $builder = QueryBuilder::table('my duyen')->select('cot1','cot2')->distinct()->join('bang1','abc.id','=','bang1.acbID')
   //     ->leftJoin('bang2','q','=','bang2.acid')->get();
   //     echo '<pre>';
   //     print_r($builder);
   // });
?>