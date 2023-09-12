<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET,POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Conecta a la base de datos  con usuario, contraseña y nombre de la BD
//$servidor = "localhost:3306"; $usuario = "boliviad_bduser1"; $contrasenia = "Prede02082016"; $nombreBaseDatos = "boliviad_predeconst";
//$servidor = "localhost"; $usuario = "root"; $contrasenia = ""; $nombreBaseDatos = "predeconst";
$servidor = "localhost:3306"; $usuario = "www_root"; $contrasenia = "RcomiC150980"; $nombreBaseDatos = "www_predeconst";
$conexionBD = new mysqli($servidor, $usuario, $contrasenia, $nombreBaseDatos);

if(isset($_GET["insertar"])){
    $data = json_decode(file_get_contents("php://input"));
        $id_insumo =$data->id_insumo;
        $id_usuario =$data->id_usuario;
        $id_proyecto =$data->id_proyecto;
        $pu_us =$data->pu_us;   
        $sqlPredec = mysqli_query($conexionBD,
        "INSERT INTO $tabla (`id_insumo`, `id_usuario`, `id_proyecto`, `pu_us`) 
        VALUES ('$id_insumo','$id_usuario','$id_proyecto','$pu_us')");
        if($sqlPredec){
            echo json_encode(["success"=>1]);
            exit();
        }
        else{  echo json_encode(["success"=>0]); }
}

if(isset($_GET["actualizar"])){
    $data = json_decode(file_get_contents("php://input"));
    $id =$data->id;
        $id_insumo =$data->id_insumo;
        $id_usuario =$data->id_usuario;
        $id_proyecto =$data->id_proyecto;
        $pu_us =$data->pu_us;
        $sqlPredec = mysqli_query($conexionBD,
        "UPDATE $tabla SET 
        `id_insumo`='$id_insumo',
        `id_usuario`='$id_usuario',
        `id_proyecto`='$id_proyecto',
        `pu_us`='$pu_us' 
        WHERE id = '$id'");
        if($sqlPredec){
            echo json_encode(["success"=>1]);
            exit();
        }
        else{  echo json_encode(["success"=>0]); }
}


if (isset($_GET["borrar"])){
    $sqlPredec = mysqli_query($conexionBD,"DELETE FROM $tabla` WHERE id =".$_GET["borrar"]);
    if($sqlPredec){
        echo json_encode(["success"=>1]);
        exit();
    }
    else{  echo json_encode(["success"=>0]); }
}


?>