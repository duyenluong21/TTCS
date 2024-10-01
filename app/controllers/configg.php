<?php
    return[
        'basePath' => '',
        'rootDir' => dirname(__DIR__),
        'layout'    => 'layouts/main',
        'db'    => [
            'host' => '127.0.0.1',
            'port'  => 3306,
            'user'  => 'root',
            'password' => ''
        ],
        'vnpay' => [
            'hashSecret' => 'ZFIRJBMBFAINNHJWFUHYJYQXPFIIVOTY'
        ]
    ];
define('HOST', 'localhost');
define('DATABASE', 'quanlymaybay');
define('USERNAME', 'root');
define('PASSWORD', '');

?>