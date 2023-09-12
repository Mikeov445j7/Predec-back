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

//MODULOS Inserta un nuevo registro y recepciona en método post los datos de nombre y correo
if(isset($_GET["insertar"])){
    $data = json_decode(file_get_contents("php://input"));
    $id_proyec=$data->id_proyec;
    $orden=$data->orden;
    $nombre=$data->nombre;
    $codigo=$data->codigo;
    $id_modOr=$data->id_modOr;
    $fecha_inicio=$data-> fecha_inicio;
    $ordenado=$data->ordenado;
    $sqlPredec = mysqli_query($conexionBD,"INSERT INTO modulos ( `id_proyec`, `orden`, `nombre`, `codigo`, `id_modOr`, `fecha_inicio`, `ordenado`) VALUES ('$id_proyec', '$orden', '$nombre', '$codigo', '$id_modOr', '$fecha_inicio', '$ordenado')");
    if($sqlPredec){
        echo json_encode(["success"=>1, "Mensaje:"=>'insertado']);
        exit();
    } else {
        echo json_encode(["Mensaje:"=>'ERRORRRR']);
    }
}
// MODULOS Actualiza datos pero recepciona datos de nombre, correo y una clave para realizar la actualización
if(isset($_GET["actualizar"])){
    $data = json_decode(file_get_contents("php://input"));
    $id_modulo=$data->id_modulo;
    $orden=$data->orden;
    $nombre=$data->nombre;
    $codigo=$data->codigo;
    $id_modOr=$data->id_modOr;
    $fecha_inicio=$data->fecha_inicio;
    $ordenado=$data->ordenado;
    $sqlPredec = mysqli_query($conexionBD,
        "UPDATE modulos 
         SET orden= '$orden', 
         nombre= '$nombre', 
         codigo= '$codigo', 
         id_modOr= '$id_modOr', 
         fecha_inicio= '$fecha_inicio', 
         ordenado= '$ordenado'
         WHERE id_modulo = $id_modulo");
        if($sqlPredec){
            echo json_encode(["success"=>1, "Mensaje:"=>'datos Actualizados5555555']);
            exit();
        } else {
            echo json_encode(["Mensaje:"=>'ERRORRRR']);
        }
}
//MODULOOOO borrar pero se le debe de enviar una clave ( para borrado )
if (isset($_GET["borrar"])){
    $data = json_decode(file_get_contents("php://input"));
    $id_modulo=$data->id_modulo;
    $sqlPredec = mysqli_query($conexionBD,"DELETE FROM modulos WHERE id_modulo=".$id_modulo);
    if($sqlPredec){
        echo json_encode(["success"=>1]);
        exit();
    }
    else{  echo json_encode(["success"=>0]); }
}
//// FIN MODULOS 
//ACTIVIDADES MODULO
// veractividades modulo
if (isset($_GET["modulo"])){
    $sqlPredec = mysqli_query($conexionBD,
    "SELECT rel_actv_modulo.id_rel_am, actividades.id_actividad, 
            actividades.descripcion, actividades.unidad, 
            rel_actv_modulo.catidad, rel_actv_modulo.unitario,  rel_actv_modulo.orden, rel_actv_modulo.fecha_ini_actv, rel_actv_modulo.fecha_fin_actv
     FROM actividades, rel_actv_modulo 
     WHERE rel_actv_modulo.id_modulo = '".$_GET["modulo"]."' 
     AND actividades.id_actividad = rel_actv_modulo.id_actividad ORDER BY rel_actv_modulo.orden");
    if(mysqli_num_rows($sqlPredec) > 0){
        $actividades = mysqli_fetch_all($sqlPredec,MYSQLI_ASSOC);
        echo json_encode($actividades);
        exit();
    }
    else{  echo json_encode(["success"=>0]); }
}
if(isset($_GET["agregarAct"])){
    $data = json_decode(file_get_contents("php://input"));
    $id_modulo=$data->id_modulo;
    $id_actividad=$data->id_actividad;
    $catidad=$data->catidad;
    $unitario=$data->unitario;
    $orden=$data->orden;
    $fecha_ini_actv=$data->fecha_ini_actv;
    $fecha_fin_actv=$data->fecha_fin_actv;
    $sqlPredec = mysqli_query($conexionBD,
                "INSERT INTO `rel_actv_modulo`(`id_modulo`, `id_actividad`, `catidad`, `unitario`, `orden`, `fecha_ini_actv`, `fecha_fin_actv`)
                 VALUES ($id_modulo, $id_actividad, $catidad, $unitario, $orden, '$fecha_ini_actv', '$fecha_fin_actv')");
    if($sqlPredec){
        echo json_encode(["success"=>1]);
        exit();
    } else {
        echo json_encode(["success"=>"error"]);
        exit();
    }
}
//actualizar data relacion actividad modulo
if (isset($_GET["UpdateRlActModulo"])){
    $data = json_decode(file_get_contents("php://input"));
    $id_rel_am=$data->id_rel_am;
    $orden=$data->orden;
    $catidad=$data->catidad;
    $fecha_ini_actv=$data->fecha_ini_actv;
    $fecha_fin_actv=$data->fecha_fin_actv;
    $sqlPredec = mysqli_query($conexionBD,
        "UPDATE `rel_actv_modulo` 
         SET orden='$orden', catidad='$catidad',
             fecha_ini_actv='$fecha_ini_actv', fecha_fin_actv='$fecha_fin_actv'
         WHERE id_rel_am='$id_rel_am'");
    echo json_encode(["success"=>1, "mensaje:"=>'datos Actualizados']);
    exit();
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



// Consulta todos los registros de la tabla $tabla

?>
