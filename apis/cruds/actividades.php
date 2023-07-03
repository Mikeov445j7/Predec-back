<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET,POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Conecta a la base de datos  con usuario, contraseña y nombre de la BD
global $servidor;
$servidor = "localhost:3306"; $usuario = "boliviad_bduser1"; $contrasenia = "Prede02082016"; $nombreBaseDatos = "boliviad_predeconst";
//$servidor = "localhost"; $usuario = "root"; $contrasenia = ""; $nombreBaseDatos = "predeconst";
global $conexionBD;
$conexionBD = new mysqli($servidor, $usuario, $contrasenia, $nombreBaseDatos);
global $tabla;
$tabla = 'actividades';

// Consulta datos y recepciona una clave para consultar dichos datos con dicha clave
if (isset($_GET["consultar"])){
    $sqlPredec = mysqli_query($conexionBD,"SELECT * FROM $tabla WHERE id_actividad=".$_GET["consultar"]);
    if(mysqli_num_rows($sqlPredec) > 0){
        $actividades = mysqli_fetch_all($sqlPredec,MYSQLI_ASSOC);
        echo json_encode($actividades);
        exit();
    }
    else{  echo json_encode(["success"=>0]); }
}

if (isset($_GET["buscar"])){
    $sqlPredec = mysqli_query($conexionBD,"SELECT * FROM $tabla WHERE descripcion LIKE '%".$_GET["buscar"]."%'");
    if(mysqli_num_rows($sqlPredec) > 0){
        $actividades = mysqli_fetch_all($sqlPredec,MYSQLI_ASSOC);
        echo json_encode($actividades);
        exit();
    }
    else{
        echo json_encode(["success"=>0]);
    }
}
if(isset($_GET["gruposA"])){
    $sqlPredec = mysqli_set_charset($conexionBD, "utf8"); 
    $sqlPredec = mysqli_query($conexionBD,"SELECT * FROM grupos_actividad ");
    if(mysqli_num_rows($sqlPredec) > 0){
        $grupos = mysqli_fetch_all($sqlPredec,MYSQLI_ASSOC);
        echo json_encode($grupos);
    }
}
//----------------------------------MOSTRAR ACTIVIDADES-------------------------------//
if (isset($_GET["mostarAct"])){
    $actividad = new stdClass();
    $sqlPredec = mysqli_set_charset($conexionBD, "utf8"); 
    $sqlPredec = mysqli_query($conexionBD,"SELECT actividades.descripcion, materiales.descripcion, rel_actv_mat.id_rel, rel_actv_mat.cant_por_acti, materiales.unidad, materiales.PU
                FROM actividades, materiales, rel_actv_mat
                WHERE rel_actv_mat.id_actividad = '".$_GET["mostarAct"]."' AND rel_actv_mat.id_mat = materiales.id_mat AND actividades.id_actividad = rel_actv_mat.id_actividad ");
        if(mysqli_num_rows($sqlPredec) > 0){
                $materiales = mysqli_fetch_all($sqlPredec,MYSQLI_ASSOC);
        }else { $materiales = "SIN MATERIALES";}
    $sqlPredec = mysqli_query($conexionBD,"SELECT actividades.descripcion, mano_obra.descripcion,  rel_actv_mo.id_rel_mat_mo, rel_actv_mo.cant, mano_obra.unidad, mano_obra.PU
                FROM actividades, mano_obra, rel_actv_mo
                WHERE rel_actv_mo.id_actividad = '".$_GET["mostarAct"]."' AND rel_actv_mo.id_mo = mano_obra.id_mo AND actividades.id_actividad = rel_actv_mo.id_actividad ");
        if(mysqli_num_rows($sqlPredec) > 0){
                $manoObra = mysqli_fetch_all($sqlPredec,MYSQLI_ASSOC);
        } else { $manoObra = "SIN MANO DE OBRA";}
    $sqlPredec = mysqli_query($conexionBD,"SELECT actividades.descripcion, equipo.descripcion,  rel_actv_equip.id_rel_mat_equip, rel_actv_equip.cant, equipo.unidad, equipo.PU 
                 FROM actividades, equipo, rel_actv_equip
                 WHERE rel_actv_equip.id_actividad = '".$_GET["mostarAct"]."' AND rel_actv_equip.id_equip = equipo.id_equip AND actividades.id_actividad = rel_actv_equip.id_actividad");
        if(mysqli_num_rows($sqlPredec) > 0){
                $equipo = mysqli_fetch_all($sqlPredec,MYSQLI_ASSOC);
        } else { $equipo = "SIN EQUIPO"; }
    $actividad -> materiales= $materiales;
    $actividad -> manoObra = $manoObra;
    $actividad -> equipo = $equipo;

    echo json_encode($actividad);
}
//-----------------------------------------------------------------------------------//
//borrar pero se le debe de enviar una clave ( para borrado )
if (isset($_GET["borrar"])){
    $sqlPredec = mysqli_query($conexionBD,"DELETE FROM $tabla WHERE id_actividad=".$_GET["borrar"]);
    if($sqlPredec){
        echo json_encode(["success"=>1]);
        exit();
    }
    else{  echo json_encode(["success"=>0]); }
}
//Inserta un nuevo registro y recepciona en método post los datos de nombre y correo
if(isset($_GET["insertar"])){
    $data = json_decode(file_get_contents("php://input"));
    $tipo=$data->tipo;
    $descripcion=$data->descripcion;
    $unidad=$data->unidad;
    $duenio=$data->duenio;
    $id_us=$data->id_us;
    $grupos_actividad=$data->grupos_actividad;
    $sub_grupo_actividad=$data->sub_grupo_actividad;

    if($descripcion!=''&&$unidad!=''&&$grupos_actividad!=''){
        $sqlPredec = mysqli_query($conexionBD,"INSERT INTO $tabla (`tipo`, `descripcion`, `unidad`, `duenio`, `id_us`, `grupos_actividad`, `sub_grupo_actividad`)
                                                VALUES ('$tipo', '$descripcion', '$unidad', '$duenio', '$id_us', '$grupos_actividad', '$sub_grupo_actividad') ");
            if($sqlPredec) {
                $last_id = $conexionBD->insert_id;
                echo json_encode(["lastId:"=>$last_id]);
            } else {
                echo json_encode(["success:"=>0]);
            }
                exit();
    }
}

// insrtar relacion actividad-insumo (MAT,MO, EQUIP)
if(isset($_GET["relActvInsumo"])){
    $data = json_decode(file_get_contents("php://input"));
    $insumo=$data->insumo;
    if($insumo==1){
        $id_actividad=$data->id_actividad;
        $id_mat=$data->id;
        $cant=$data->cant;
        if($id_actividad!=''&&$id_mat!=''&&$cant!=''){
            $sqlPredec = mysqli_query($conexionBD,"INSERT INTO `rel_actv_mat` (`id_actividad`, `id_mat`, `cant_por_acti`) 
                                                   VALUES ('$id_actividad','$id_mat','$cant')");
                    if($sqlPredec) {
                        echo json_encode(["success:"=>1]);
                    } else {
                        echo json_encode(["success:"=>0]);
                    }
                    exit();
        }
    }
    if($insumo==2){
        $id_actividad=$data->id_actividad;
        $id_mo=$data->id;
        $cant=$data->cant;
        if($id_actividad!=''&&$id_mo!=''&&$cant!=''){
            $sqlPredec = mysqli_query($conexionBD,"INSERT INTO `rel_actv_mo`(`id_actividad`, `id_mo`, `cant`) 
                                                   VALUES ('$id_actividad','$id_mo','$cant')");
                    if($sqlPredec) {
                        echo json_encode(["success:"=>1]);
                    } else {
                        echo json_encode(["success:"=>0]);
                    }
                    exit();
        }
    }
    if($insumo==3){
        $id_actividad=$data->id_actividad;
        $id_equip=$data->id;
        $cant=$data->cant;
        if($id_actividad!=''&&$id_equip!=''&&$cant!=''){
            $sqlPredec = mysqli_query($conexionBD,"INSERT INTO `rel_actv_equip`(`id_actividad`, `id_equip`, `cant`) 
                                                   VALUES ('$id_actividad','$id_equip','$cant')");
                    if($sqlPredec) {
                        echo json_encode(["success:"=>1]);
                    } else {
                        echo json_encode(["success:"=>0]);
                    }
                    exit();
        }
    }

}
//QUITAR RELACION ITEM INSUMO
if(isset($_GET["quitarRelActvInsumo"])){
    $data = json_decode(file_get_contents("php://input"));
    $insumo=$data->insumo;
    if($insumo==1){
        $id_rel=$data->id;
        $sqlPredec = mysqli_query($conexionBD,"DELETE FROM rel_actv_mat WHERE id_rel =".$id_rel);
        if($sqlPredec){
            echo json_encode(["success"=>1]);
            exit();
        }
        else{  echo json_encode(["success"=>0]); }
    }
    if($insumo==2){
        $id_rel_mat_mo=$data->id;
        $sqlPredec = mysqli_query($conexionBD,"DELETE FROM rel_actv_mo WHERE id_rel_mat_mo=".$id_rel_mat_mo);
        if($sqlPredec){
            echo json_encode(["success"=>1]);
            exit();
        }
        else{  echo json_encode(["success"=>0]); }
    }
    if($insumo==3){
        $id_rel_mat_equip=$data->id;
        $sqlPredec = mysqli_query($conexionBD,"DELETE FROM rel_actv_equip WHERE id_rel_mat_equip=".$id_rel_mat_equip);
        if($sqlPredec){
            echo json_encode(["success"=>1]);
            exit();
        }
        else{  echo json_encode(["success"=>0]); }

    }

}


// Actualiza datos pero recepciona datos de nombre, correo y una clave para realizar la actualización
if(isset($_GET["actualizar"])){
    
    $data = json_decode(file_get_contents("php://input"));

    $id_actividad=(isset($data->id))?$data->id:$_GET["actualizar"];
    $grupos_actividad=$data->grupos_actividad;
    $descripcion=$data->descripcion;
    $unidad=$data->unidad;
    $sqlPredec = mysqli_query($conexionBD,"UPDATE $tabla SET descripcion='$descripcion',  unidad='$unidad', grupos_actividad='$grupos_actividad'  WHERE id_actividad='$id_actividad'");
    echo json_encode(["success"=>1, "mensaje:"=>2]);
    exit();
}

// Consulta todos los registros de la tabla $tabla

else{ 
    /*$sqlPredec = mysqli_query($conexionBD,"SELECT * FROM $tabla ");
    if(mysqli_num_rows($sqlPredec) > 0){
        $actividades = mysqli_fetch_all($sqlPredec,MYSQLI_ASSOC);
        echo json_encode($actividades);
    }*/
    //echo json_encode([["success"=>0]]); 
}

?>
