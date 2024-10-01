<?php
     require_once "../models/db.php";
     require_once "../models/User.php";
     
     $tipo_consulta = $_POST['form_name'];
     switch ($tipo_consulta) {
         case 'send_value':
             $maNV = $_POST['maNV'];
             $hoNV = $_POST['hoNV'];
             $tenNV = $_POST['tenNV'];            
             $kinhNghiem =$_POST['kinhNghiem'];
             $trinhDoHocVan =$_POST['trinhDoHocVan'];
             $chucVu =$_POST['chucVu'];
             $username =$_POST['username'];
             $passw =$_POST['passw'];
             $consultas = new User();
             $ejecutar = $consultas->insert($maNV,$hoNV,$tenNV,$kinhNghiem,$trinhDoHocVan,
             $chucVu,$username,$passw);
             echo json_encode($ejecutar);
            break;   
         case 'edit':
            $maNV = $_POST['maNV'];
            $consultas = new User();
            $ejecutar = $consultas->edit($maNV);
            echo json_encode($ejecutar);
            break;
        case 'update_user':
            $maNV = $_POST['maNV'];
            $hoNV = $_POST['hoNV'];
            $tenNV = $_POST['tenNV'];      
            $chucVu= $_POST['chucVu'];
            $kinhNghiem= $_POST['kinhNghiem'];
            $username= $_POST['username'];
            $passw= $_POST['passw'];
            $consultas = new User();
            $ejecutar = $consultas->update_user($maNV,$hoNV, $tenNV,$chucVu,$kinhNghiem,$username,$passw);
            echo json_encode($ejecutar);
            break;
        case 'delete_user' :
            $maNV = $_POST['maNV'];
            $consultas = new User();
            $ejecutar = $consultas->delete_user($maNV);
            echo json_encode($ejecutar);
            break;
         default:
             # code...
             break;
     }
?>
