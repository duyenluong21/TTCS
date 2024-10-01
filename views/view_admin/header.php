<?php
require_once "./app/models/db.php";
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
    <title>Header</title>
</head>
<style>
    ::placeholder {
        color: #fff;
        opacity: 1;
        /* Firefox */
    }

    ::-ms-input-placeholder {
        /* Edge 12 -18 */
        color: #fff;
    }
</style>

<body>
    <div id="main">
        <div id="header">
            <nav class="navbar navbar-expand-lg bg-body-tertiary">
                <div class="container-fluid">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNavDropdown">
                        <div class="search-header">
                            <div class="icon-search">
                                <i class="ti-search"></i>
                                <input type="text" class = "form-control1" id = "live_search" autocomplete="off" placeholder="Tìm kiếm"  onkeydown="handleKeyPress(event)">
                            </div>
                        </div>
                    </div>
                </div>
                <button class="register">
                    <i class="ti-bell"></i>
                </button>
                <button class="login">
                    <i class="ti-user"></i>
                </button>

            </nav>
        </div>
        <div id="searchResult">

        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function(){
            $("#live_search").keyup(function(){
                var input = $($this).val();
                alert(input);
                if(input != ""){
                    $.ajax({
                        url: "header.php",
                        method: "POST",
                        data:{input,input},

                        success:function(data){
                            $("#searchResult").html(data);
                        }
                    });
                }else{
                    $('#searchResult').css("display","none");
                }
            });
        });
    </script>
</body>

</html>