<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET,POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Conecta a la base de datos  con usuario, contraseña y nombre de la BD
//$servidor = "localhost:3306"; $usuario = "boliviad_bduser1"; $contrasenia = "Prede02082016"; $nombreBaseDatos = "boliviad_predeconst";
//$servidor = "localhost"; $usuario = "root"; $contrasenia = ""; $nombreBaseDatos = "predeconst";
$servidor = "localhost"; $usuario = "c1402643_predec"; $contrasenia = "22poWEzodu"; $nombreBaseDatos = "c1402643_predec";
$conexionBD = new mysqli($servidor, $usuario, $contrasenia, $nombreBaseDatos);
$tabla = 'proyectos';

// Consulta datos y recepciona una clave para consultar dichos datos con dicha clave
if (isset($_GET["listar"])){
    $sqlPredec = mysqli_set_charset($conexionBD, "utf8"); 
    $sqlPredec = mysqli_query($conexionBD,"SELECT * FROM $tabla");
    if(mysqli_num_rows($sqlPredec) > 0){
        $empleaados = mysqli_fetch_all($sqlPredec,MYSQLI_ASSOC);
        echo json_encode($empleaados);
        exit();
    }
    else{  echo json_encode(["success"=>0]); }
}
if (isset($_GET["consultar"])){
    $sqlPredec = mysqli_set_charset($conexionBD, "utf8"); 
    $sqlPredec = mysqli_query($conexionBD,"SELECT * FROM $tabla WHERE id_proyec=".$_GET["consultar"]);
    if(mysqli_num_rows($sqlPredec) > 0){
        $empleaados = mysqli_fetch_all($sqlPredec,MYSQLI_ASSOC);
        echo json_encode($empleaados);
        exit();
    }
    else{  echo json_encode(["success"=>0]); }
}
if (isset($_GET["id_us"])){
    $sqlPredec = mysqli_set_charset($conexionBD, "utf8"); 
    $sqlPredec = mysqli_query($conexionBD,"SELECT * FROM $tabla WHERE id_us=".$_GET["id_us"]);
    if(mysqli_num_rows($sqlPredec) > 0){
        $empleaados = mysqli_fetch_all($sqlPredec,MYSQLI_ASSOC);
        echo json_encode($empleaados);
        exit();
    }
    else{  
        echo json_encode(["success"=>0]); 
    }
}
if (isset($_GET["buscarPUS"])){
    $sqlPredec = mysqli_set_charset($conexionBD, "utf8"); 
    $data = json_decode(file_get_contents("php://input"));
    $id_us=$data->id_us;
    $param=$data->param;
    $sqlPredec = mysqli_query($conexionBD,"SELECT * FROM $tabla WHERE nombre LIKE '$param%' AND id_us='$id_us'");
    if(mysqli_num_rows($sqlPredec) > 0){
        $empleaados = mysqli_fetch_all($sqlPredec,MYSQLI_ASSOC);
        echo json_encode($empleaados);
        exit();
    }
    else{  echo json_encode(["success"=>0]); }
}
if (isset($_GET["buscar"])){
    $sqlPredec = mysqli_set_charset($conexionBD, "utf8"); 
    $sqlPredec = mysqli_query($conexionBD,"SELECT * FROM $tabla WHERE nombre LIKE '%".$_GET["buscar"]."%'");
    if(mysqli_num_rows($sqlPredec) > 0){
        $empleaados = mysqli_fetch_all($sqlPredec,MYSQLI_ASSOC);
        echo json_encode($empleaados);
        exit();
    }
    else{  echo json_encode(["success"=>0]); }
}
//borrar pero se le debe de enviar una clave ( para borrado )
if (isset($_GET["borrar"])){
    $sqlPredec = mysqli_set_charset($conexionBD, "utf8"); 
    $sqlPredec = mysqli_query($conexionBD,"DELETE FROM $tabla WHERE id_proyec=".$_GET["borrar"]);
    if($sqlPredec){
        echo json_encode(["success"=>1]);
        exit();
    }
    else{  echo json_encode(["success"=>0]); }
}
//Inserta un nuevo registro y recepciona en método post los datos de nombre y correo
if(isset($_GET["insertar"])){
    $data = json_decode(file_get_contents("php://input"));
    $id_us=$data->id_us;
    $nombre=$data->nombre;
    $codigo=$data->codigo;
    $fecha_creacion=$data->fecha_creacion;
    $Ben_Soc=$data->Ben_Soc;
    $iva=$data->iva;
    $he_men=$data->he_men;
    $g_grales=$data->g_grales;
    $utilidad=$data->utilidad;
    $IT=$data->IT;
    $cliente=$data->cliente;
    $tip_cambio=$data->tip_cambio;
    $fecha=$data->fecha_creacion;
    $ubicacion=$data->ubicacion;
    $id_proyecOr=$data->id_proyecOr;
    $sqlPredec = mysqli_query($conexionBD,"INSERT INTO $tabla (`id_us`, `nombre`, `codigo`, `fecha_creacion`, `Ben_Soc`, `iva`, `he_men`, `g_grales`, `utilidad`, `IT`, `cliente`, `tip_cambio`, `fecha`, `ubicacion`, `id_proyecOr`) VALUES ('$id_us', '$nombre', '$codigo', '$fecha_creacion', '$Ben_Soc', '$iva', '$he_men', '$g_grales', '$utilidad', '$IT', '$cliente', '$tip_cambio', '$fecha', '$ubicacion', '$id_proyecOr') ");
    echo json_encode(["success"=>1]);
    exit();
}
// Actualiza datos pero recepciona datos de nombre, correo y una clave para realizar la actualización
if(isset($_GET["actualizar"])){
    
    $data = json_decode(file_get_contents("php://input"));

    $id_proyec=(isset($data->id))?$data->id:$_GET["actualizar"];
    $nombre=$data->nombre;
    $codigo=$data->codigo;
    $fecha_creacion=$data->fecha_creacion;
    $Ben_Soc=$data->Ben_Soc;
    $iva=$data->iva;
    $he_men=$data->he_men;
    $g_grales=$data->g_grales;
    $utilidad=$data->utilidad;
    $IT=$data->IT;
    $cliente=$data->cliente;
    $tip_cambio=$data->tip_cambio;
    $fecha=$data->fecha_creacion;
    $ubicacion=$data->ubicacion;
    $sqlPredec = mysqli_query($conexionBD,"UPDATE $tabla SET nombre='$nombre', codigo='$codigo', fecha_creacion='$fecha_creacion', Ben_Soc='$Ben_Soc', iva='$iva', he_men='$he_men', g_grales='$g_grales', utilidad='$utilidad', IT='$IT', cliente='$cliente', tip_cambio='$tip_cambio', fecha='$fecha', ubicacion='$ubicacion' WHERE id_proyec='$id_proyec'");
    echo json_encode(["success"=>1, "mensaje:"=>2]);
    exit();
}

// Consulta todos los registros de la tabla $tabla
/*$sqlPredec = mysqli_query($conexionBD,"SELECT * FROM $tabla ");
if(mysqli_num_rows($sqlPredec) > 0){
    $empleaados = mysqli_fetch_all($sqlPredec,MYSQLI_ASSOC);
    echo json_encode($empleaados);
}
else{ echo json_encode([["success"=>0]]); }*/
?>
