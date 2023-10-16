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


if(isset($_GET["listar"])){
    $data = json_decode(file_get_contents("php://input"));
    $id_proyecto =$data->id_proyecto;
    $misPrecios = new stdClass();
    $sqlPredec = mysqli_set_charset($conexionBD, "utf8"); 
    $sqlPredec = mysqli_query($conexionBD,
                 "SELECT pu_us_mat.id, materiales.id_mat, materiales.descripcion, materiales.unidad, pu_us_mat.pu_us
                  FROM materiales, pu_us_mat
                  WHERE pu_us_mat.id_proyecto = '$id_proyecto'
                  AND materiales.id_mat = pu_us_mat.id_insumo");
        if(mysqli_num_rows($sqlPredec) > 0){
                $materiales = mysqli_fetch_all($sqlPredec,MYSQLI_ASSOC);
        }else { $materiales = 0;}
    $sqlPredec = mysqli_query($conexionBD,
                 "SELECT pu_us_mo.id, mano_obra.id_mo, mano_obra.descripcion, mano_obra.unidad, pu_us_mo.pu_us
                  FROM mano_obra, pu_us_mo
                  WHERE pu_us_mo.id_proyecto = '$id_proyecto'
                  AND mano_obra.id_mo = pu_us_mo.id_insumo");
        if(mysqli_num_rows($sqlPredec) > 0){
                $manoObra = mysqli_fetch_all($sqlPredec,MYSQLI_ASSOC);
        } else { $manoObra = 0;}
    $sqlPredec = mysqli_query($conexionBD,
                 "SELECT pu_us_eq.id, equipo.id_equip, equipo.descripcion, equipo.unidad, pu_us_eq.pu_us
                  FROM equipo, pu_us_eq
                  WHERE pu_us_eq.id_proyecto = '$id_proyecto'
                  AND equipo.id_equip = pu_us_eq.id_insumo");
        if(mysqli_num_rows($sqlPredec) > 0){
                $equipo = mysqli_fetch_all($sqlPredec,MYSQLI_ASSOC);
        } else { $equipo = 0; }
    $misPrecios -> materiales= $materiales;
    $misPrecios -> manoObra = $manoObra;
    $misPrecios -> equipo = $equipo;

    echo json_encode($misPrecios);
}
if(isset($_GET["verificar"])){
    $data = json_decode(file_get_contents("php://input"));
    $id_proyecto =$data->id_proyecto;
    $id_insumo =$data->id_insumo;
    $tabla = $data ->tabla;
    $sqlPredec = mysqli_set_charset($conexionBD, "utf8"); 
    $sqlPredec = mysqli_query($conexionBD,
                 "SELECT id, pu_us, consolidado 
                  FROM $tabla
                  WHERE id_proyecto = '$id_proyecto'
                  AND id_insumo = '$id_insumo'");
        if(mysqli_num_rows($sqlPredec) > 0){
            $insumo = mysqli_fetch_all($sqlPredec,MYSQLI_ASSOC);
            echo json_encode(["success"=>1, "id"=>$insumo]);
            exit();
        }else { 
            echo json_encode(["success"=>0]);
        }

}
if(isset($_GET["insertar"])){
    $data = json_decode(file_get_contents("php://input"));
        $id_insumo =$data->id_insumo;
        $id_usuario =$data->id_usuario;
        $id_proyecto =$data->id_proyecto;
        $tabla = $data->tabla;
        $pu_us =$data->pu_us;   
        $sqlPredec = mysqli_query($conexionBD,
        "INSERT INTO $tabla (`id_insumo`, `id_usuario`, `id_proyecto`, `pu_us`) 
        VALUES ('$id_insumo','$id_usuario','$id_proyecto','$pu_us')");

       /* if ($conexionBD->query($sqlPredec) === TRUE) {
                echo json_encode(["success"=>"New record created successfully"]);
        } else {
                echo "Error: " . $sqlPredec . "<br>" . $conexionBD->error;
        }*/
        if($sqlPredec){
            echo json_encode(["success"=>1]);
            exit();
        }
        else{  echo json_encode(["success"=>0]); }
}

if(isset($_GET["actualizar"])){
    $data = json_decode(file_get_contents("php://input"));
        $tabla = $data ->tabla;
        $id =$data->id;
        $pu_us =$data->pu_us;
        $sqlPredec = mysqli_query($conexionBD, "UPDATE $tabla SET `pu_us`='$pu_us' WHERE id = '$id'");
        if($sqlPredec){
            echo json_encode(["success"=>1]);
            exit();
        }
        else{  echo json_encode(["success"=>0]); }
}


if (isset($_GET["borrar"])){
    $data = json_decode(file_get_contents("php://input"));
    $id =$data->id;
    $tabla = $data ->tabla;
    $sqlPredec = mysqli_query($conexionBD,"DELETE FROM $tabla WHERE id =".$id);
    if($sqlPredec){
        echo json_encode(["success"=>1]);
        exit();
    }
    else{  echo json_encode(["success"=>0]); }
}


?>