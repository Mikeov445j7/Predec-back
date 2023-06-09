<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET,POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Conecta a la base de datos  con usuario, contraseña y nombre de la BD
$servidor = "localhost:3306"; $usuario = "boliviad_bduser1"; $contrasenia = "Prede02082016"; $nombreBaseDatos = "boliviad_predeconst";
//$servidor = "localhost"; $usuario = "root"; $contrasenia = ""; $nombreBaseDatos = "predeconst";
$conexionBD = new mysqli($servidor, $usuario, $contrasenia, $nombreBaseDatos);



if (isset($_GET["listar"])){
    $sqlPredec = mysqli_set_charset($conexionBD, "utf8"); 
    $sqlPredec = mysqli_query($conexionBD,"SELECT * FROM materiales");
    if(mysqli_num_rows($sqlPredec) > 0){
        $empleaados = mysqli_fetch_all($sqlPredec,MYSQLI_ASSOC);
        echo json_encode($empleaados);
    }
    else{  echo json_encode(["success"=>0]); }
}
// Consulta datos y recepciona una clave para consultar dichos datos con dicha clave
if (isset($_GET["consultar"])){
    $sqlPredec = mysqli_query($conexionBD,"SELECT * FROM materiales WHERE id_mat=".$_GET["consultar"]);
    if(mysqli_num_rows($sqlPredec) > 0){
        $empleaados = mysqli_fetch_all($sqlPredec,MYSQLI_ASSOC);
        echo json_encode($empleaados);
        exit();
    }
    else{  echo json_encode(["success"=>0]); }
}
if (isset($_GET["buscar"])){
    $sqlPredec = mysqli_query($conexionBD,"SELECT * FROM materiales WHERE descripcion LIKE '%".$_GET["buscar"]."%'");
    if(mysqli_num_rows($sqlPredec) > 0){
        $empleaados = mysqli_fetch_all($sqlPredec,MYSQLI_ASSOC);
        echo json_encode($empleaados);
        exit();
    }
    else{  echo json_encode(["success"=>0]); }
}
//borrar pero se le debe de enviar una clave ( para borrado )
if (isset($_GET["borrar"])){
    $sqlPredec = mysqli_query($conexionBD,"DELETE FROM materiales WHERE id_mat=".$_GET["borrar"]);
    if($sqlPredec){
        echo json_encode(["success"=>1]);
        exit();
    }
    else{  echo json_encode(["success"=>0]); }
}
//Inserta un nuevo registro y recepciona en método post los datos de nombre y correo
if(isset($_GET["insertar"])){
    $data = json_decode(file_get_contents("php://input"));
    $descripcion=$data->descripcion;
    $unidad=$data->unidad;
    $PU=$data->PU;
    $grupo_insumo=$data->grupo_insumo;
            $sqlPredec = mysqli_query($conexionBD,"INSERT INTO materiales (descripcion, unidad, PU, grupo_insumo) VALUES('$descripcion', '$unidad', '$PU', '$grupo_insumo') ");
            echo json_encode(["success"=>1]);
        
    exit();
}
// Actualiza datos pero recepciona datos de nombre, correo y una clave para realizar la actualización
if(isset($_GET["actualizar"])){
    
    $data = json_decode(file_get_contents("php://input"));

    $id_mat=(isset($data->id))?$data->id:$_GET["actualizar"];
    $descripcion=$data->descripcion;
    $unidad=$data->unidad;
    $PU=$data->PU;
    $grupo_insumo=$data->grupo_insumo;
    $sqlPredec = mysqli_query($conexionBD,"UPDATE materiales SET descripcion ='$descripcion' , unidad='$unidad' , PU='$PU' , grupo_insumo='$grupo_insumo' WHERE id_mat='$id_mat'");
    echo json_encode(["success"=>1]);
    exit();
}

// Consulta todos los registros de la tabla mano_obra
/*$sqlPredec = mysqli_query($conexionBD,"SELECT * FROM materiales");
//$sqlPredec = mysqli_set_charset($conexionBD, "utf8"); 
if(mysqli_num_rows($sqlPredec) > 0){
    $empleaados = mysqli_fetch_all($sqlPredec,MYSQLI_ASSOC);
    echo json_encode($empleaados);
}
else{ echo json_encode([["success"=>0]]); }*/
?>