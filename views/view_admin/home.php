<?php
session_start()
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/fonts/bootstrap/css/bootstrap-grid.css">
    <link rel="stylesheet" href="../../public/fonts/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="../../public/fonts/themify-icons/themify-icons.css">
    <title>Home</title>

</head>

<body>
     <!--category-left -->
     <?php include("../TTCS/views/view_admin/category_left.php"); ?>
    <!--category-right -->
    <div class="category-right">
        <?php include("../TTCS/views/view_admin/header.php"); ?>
            <div class="slider">
            <img class="mySlides w3-animate-fading" src="/public/img/slider0.png" style="width:100%">
            <img class="mySlides w3-animate-fading" src="/public/img/slider1.png" style="width:100%">
            <img class="mySlides w3-animate-fading" src="/public/img/slider2.png" style="width:100%">
            <img class="mySlides w3-animate-fading" src="/public/img/slider3.png" style="width:100%">
            <img class="mySlides w3-animate-fading" src="/public/img/slider4.png" style="width:100%">
            </div>
       
    </style> 
    </div>

   

             

      
<script src = "../../public/js/main.js"></script>
</body>
</html>