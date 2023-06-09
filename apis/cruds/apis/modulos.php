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
$tabla = 'modulos';

// Consulta datos y recepciona una clave para consultar dichos datos con dicha clave
if (isset($_GET["consultar"])){
    $sqlPredec = mysqli_query($conexionBD,"SELECT * FROM $tabla WHERE id_modulo=".$_GET["consultar"]);
    if(mysqli_num_rows($sqlPredec) > 0){
        $empleaados = mysqli_fetch_all($sqlPredec,MYSQLI_ASSOC);
        echo json_encode($empleaados);
        exit();
    }
    else{  echo json_encode(["success"=>0]); }
}
if (isset($_GET["proyecto"])){
    $sqlPredec = mysqli_query($conexionBD,"SELECT * FROM $tabla WHERE id_proyec=".$_GET["proyecto"]." ORDER by orden ASC");
    if(mysqli_num_rows($sqlPredec) > 0){
        $empleaados = mysqli_fetch_all($sqlPredec,MYSQLI_ASSOC);
        echo json_encode($empleaados);
        exit();
    }
    else{  echo json_encode(["success"=>0]); }
}
if (isset($_GET["buscar"])){
    $sqlPredec = mysqli_query($conexionBD,"SELECT * FROM $tabla WHERE nombre LIKE '%".$_GET["buscar"]."%'");
    if(mysqli_num_rows($sqlPredec) > 0){
        $empleaados = mysqli_fetch_all($sqlPredec,MYSQLI_ASSOC);
        echo json_encode($empleaados);
        exit();
    }
    else{  echo json_encode(["success"=>0]); }
}
///quitar Actividad
if (isset($_GET["quitarAct"])){
    $sqlPredec = mysqli_query($conexionBD,"DELETE FROM rel_actv_modulo WHERE id_rel_am=".$_GET["quitarAct"]);
    if($sqlPredec){
        echo json_encode(["success"=>1]);
        exit();
    }
    else{  echo json_encode(["success"=>0]); }
}
//borrar pero se le debe de enviar una clave ( para borrado )
if (isset($_GET["borrar"])){
    $sqlPredec = mysqli_query($conexionBD,"DELETE FROM $tabla WHERE id_modulo=".$_GET["borrar"]);
    if($sqlPredec){
        echo json_encode(["success"=>1]);
        exit();
    }
    else{  echo json_encode(["success"=>0]); }
}
//Inserta un nuevo registro y recepciona en método post los datos de nombre y correo
if(isset($_GET["insertar"])){
    $data = json_decode(file_get_contents("php://input"));
    $id_proyec=$data->id_proyec;
    $orden=$data->orden;
    $nombre=$data->nombre;
    $codigo=$data->codigo;
    $id_modOr=$data->id_modOr;
    $fecha_inicio=$data-> fecha_inicio;
    $ordenado=$data->ordenado;
    $sqlPredec = mysqli_query($conexionBD,"INSERT INTO $tabla ( `id_proyec`, `orden`, `nombre`, `codigo`, `id_modOr`, `fecha_inicio`, `ordenado`) VALUES ('$id_proyec', '$orden', '$nombre', '$codigo', '$id_modOr', '$fecha_inicio', '$ordenado')");
    echo json_encode(["success"=>1]);
    exit();
}
if(isset($_GET["agregarAct"])){
    $data = json_decode(file_get_contents("php://input"));
    $id_modulo=$data->id_modulo;
    $id_actividad=$data->id_actividad;
    $catidad=$data->catidad;
    $unitario=$data->unitario;
    $orden=$data->orden;
    $sqlPredec = mysqli_query($conexionBD,"INSERT INTO `rel_actv_modulo`(`id_modulo`, `id_actividad`, `catidad`, `unitario`, `orden`)
                                            VALUES ($id_modulo, $id_actividad, $catidad, $unitario, $orden)");
    echo json_encode(["success"=>1]);
    exit();
}
// Actualiza datos pero recepciona datos de nombre, correo y una clave para realizar la actualización
if(isset($_GET["actualizar"])){
    
    $data = json_decode(file_get_contents("php://input"));

    $id_modulo=(isset($data->id))?$data->id:$_GET["actualizar"];
    $orden=$data->orden;
    $nombre=$data->nombre;
    $codigo=$data->codigo;
    $id_modOr=$data->id_modOr;
    $fecha_inicio=$data->fecha_inicio;
    $ordenado=$data->ordenado;
    $sqlPredec = mysqli_query($conexionBD,"UPDATE $tabla SET nombre='$nombre', codigo='$codigo', fecha_creacion='$fecha_creacion', Ben_Soc='$Ben_Soc', iva='$iva', he_men='$he_men', g_grales='$g_grales', utilidad='$utilidad', IT='$IT', cliente='$cliente', tip_cambio='$tip_cambio', fecha='$fecha', ubicacion='$ubicacion' WHERE id_modulo='$id_modulo'");
    echo json_encode(["success"=>1, "mensaje:"=>2]);
    exit();
}
if (isset($_GET["modulo"])){
    $sqlPredec = mysqli_query($conexionBD,"SELECT rel_actv_modulo.id_rel_am, actividades.id_actividad, actividades.descripcion, actividades.unidad, rel_actv_modulo.catidad, rel_actv_modulo.unitario  FROM actividades, rel_actv_modulo WHERE rel_actv_modulo.id_modulo = '".$_GET["modulo"]."' AND actividades.id_actividad = rel_actv_modulo.id_actividad ORDER BY orden");
    if(mysqli_num_rows($sqlPredec) > 0){
        $actividades = mysqli_fetch_all($sqlPredec,MYSQLI_ASSOC);
        echo json_encode($actividades);
        exit();
    }
    else{  echo json_encode(["success"=>0]); }
}

// Consulta todos los registros de la tabla $tabla
$sqlPredec = mysqli_query($conexionBD,"SELECT * FROM $tabla ");
if(mysqli_num_rows($sqlPredec) > 0){
    $empleaados = mysqli_fetch_all($sqlPredec,MYSQLI_ASSOC);
    echo json_encode($empleaados);
}
else{ echo json_encode([["success"=>0]]); }
?>
