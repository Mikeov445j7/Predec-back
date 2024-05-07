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

$tabla = 'actividades';


if(isset($_GET["pxua"])){
    $data = json_decode(file_get_contents("php://input"));
    $pxuModulo = Array();
    $puXact = new stdClass();
    $id_modulo = $data->idModulo;
    $id_proyec = $data->id_proyec;
    $Ben_Soc = 0;
    $iva = 0;
    $he_men = 0;
    $g_grales = 0;
    $utilidad = 0;
    $IT = 0;
    $nom_proy = "";
    $cadena ='';
    $sql2 = mysqli_set_charset($conexionBD, "utf8"); 
    $sql2 = mysqli_query($conexionBD,"SELECT * FROM proyectos WHERE id_proyec = '".$id_proyec."'");
        if(mysqli_num_rows($sql2) > 0){
            
               while($row2 = mysqli_fetch_array($sql2)){
                        $nom_proy = $row2[2];
                        $Ben_Soc = $row2[5];
                        $iva = $row2[6];
                        $he_men = $row2[7];
                        $g_grales = $row2[8];
                        $utilidad = $row2[9];
                        $IT = $row2[10];
                        $cliente = $row2[11];
                        $tip_cambio = $row2[12];
                        $fecha = $row2[13];
                    }
          }
    $sqlPredec = mysqli_set_charset($conexionBD, "utf8"); 
    $sqlPredec = mysqli_query($conexionBD,
    "SELECT rel_actv_modulo.id_rel_am, actividades.id_actividad, actividades.descripcion, actividades.unidad, 
            rel_actv_modulo.catidad, rel_actv_modulo.unitario, modulos.nombre, rel_actv_modulo.fecha_ini_actv, rel_actv_modulo.fecha_fin_actv, rel_actv_modulo.orden 
     FROM actividades, rel_actv_modulo, modulos 
     WHERE rel_actv_modulo.id_modulo = '".$id_modulo."' 
     AND actividades.id_actividad = rel_actv_modulo.id_actividad 
     AND modulos.id_modulo = rel_actv_modulo.id_modulo 
     ORDER BY rel_actv_modulo.orden");

    if(mysqli_num_rows($sqlPredec) > 0){
       // $actividades = mysqli_fetch_all($sqlPredec,MYSQLI_ASSOC);
        while($row2 = mysqli_fetch_array($sqlPredec)){
            $id_a = $row2[1];
            $puXact = [
                'id_proyec' => $id_proyec,
                'nom_proy' => $nom_proy,
                'cliente' => $cliente,
                'tip_cambio'=> $tip_cambio,
                'id_modulo' => $id_modulo,
                'modulo' => $row2[6],
                'fechaIni'=> $row2[7],
                'fechaFin'=> $row2[8],
                'orden'=> $row2[9],
                'id_actividad'=> $row2[1],
                'actividad' => $row2[2],
                'unidad' => $row2[3],
                'cantidad' => $row2[4],
                'materiales' => A($conexionBD, $id_proyec, $id_a),
                'manoObra' => B($conexionBD,  $id_proyec, $id_a, $Ben_Soc, $iva),
                'equipo' => F($conexionBD, $id_proyec, $id_a, $he_men),
                'g_grales' => $g_grales,
                'utilidad' => $utilidad,
                'it' => $IT,
                'he_men' => $he_men,
                'id_rel_am' => $row2[0]
            ];
           
            array_push($pxuModulo, $puXact);
        }
        echo json_encode($pxuModulo);
        exit();
    }
    else{  echo json_encode(["success"=>0]); }
}

function A($conexionBD, $id_proyec, $id){
    $A = new stdClass();
    $m = new stdClass();
    $mats = array();
    $sqlPredec = mysqli_set_charset($conexionBD, "utf8"); 
    $sqlPredec = mysqli_query($conexionBD,"SELECT actividades.descripcion, materiales.descripcion, rel_actv_mat.id_rel, rel_actv_mat.cant_por_acti, materiales.unidad, materiales.PU, materiales.id_mat
                FROM actividades, materiales, rel_actv_mat
                WHERE rel_actv_mat.id_actividad = '$id' AND rel_actv_mat.id_mat = materiales.id_mat AND actividades.id_actividad = rel_actv_mat.id_actividad ");
        if(mysqli_num_rows($sqlPredec) > 0){
                //$materiales = mysqli_fetch_all($sqlPredec,MYSQLI_ASSOC);
                while($row2 = mysqli_fetch_array($sqlPredec)){
                    $nombre = $row2[1];
                    $unidad = $row2[4];
                    $cant = $row2[3];
                    $pu = intersecc($conexionBD, 'pu_us_mat', $id_proyec, $row2[6], $row2[5]);
                    $parcial = $row2[3] * $pu;
                    $m = ["nombre"=>$nombre, "unidad"=>$unidad, "cant"=>$cant, "pu"=>$pu, "parcial"=> $parcial];
                    array_push($mats, $m);
                }
                $sum = 0;
                foreach($mats as $mts=>$value){
                    $sum = $sum + $value['parcial'];
                }
                $A -> data= true;
        } else { 
            $A -> data = false;
            $A -> msj = 'SIN MATERIALES';
        }
        $sum = 0;
        foreach($mats as $mts=>$value){
            $sum = $sum + $value['parcial'];
        }
        $A -> listaMateriales = $mats;
        $A -> A = round($sum,2);
 
        return $A; 
}
function B($conexionBD, $id_proyec, $id, $Ben_Soc, $iva){
    $B = new stdClass();
    $mObra = new stdClass();
    $mo = array();
    $sqlPredec = mysqli_set_charset($conexionBD, "utf8"); 
    $sqlPredec = mysqli_query($conexionBD,
        "SELECT actividades.descripcion, mano_obra.descripcion,  rel_actv_mo.id_rel_mat_mo, rel_actv_mo.cant, mano_obra.unidad, mano_obra.PU, mano_obra.id_mo
         FROM actividades, mano_obra, rel_actv_mo
         WHERE rel_actv_mo.id_actividad = '$id' AND rel_actv_mo.id_mo = mano_obra.id_mo AND actividades.id_actividad = rel_actv_mo.id_actividad ");
        if(mysqli_num_rows($sqlPredec) > 0){
            //$manoObra = mysqli_fetch_all($sqlPredec,MYSQLI_ASSOC);
            while($row2 = mysqli_fetch_array($sqlPredec)){
                $nombre = $row2[1];
                $unidad = $row2[4];
                $cant = $row2[3];
                $pu = intersecc($conexionBD, 'pu_us_mo', $id_proyec, $row2[6], $row2[5]);
                $parcial = $row2[3] * $pu;
                $mObra = ["nombre"=>$nombre, "unidad"=>$unidad, "cant"=>$cant, "pu"=>$pu, "parcial"=> $parcial];
                array_push($mo, $mObra);
            }
            $B -> data = true;

        } else { 
            $B -> data = false;
            $B -> msj = "SIN MANO DE OBRA";
           
        }
        $sum = 0;
        $B-> manoObra = $mo;
        foreach($mo as $obra=>$value){
            $sum = $sum + $value['parcial'];
        }
        $B-> B = round($sum,2);
        $B-> Ben_Soc = $Ben_Soc;
        $c = round(($sum*($Ben_Soc/100)),2);
        $B-> C = $c;
        $B-> iva = $iva;
        $d = round(($sum+$c)*($iva/100),2);
        $B-> D = $d;
        $B-> E = round($sum+$c+$d,2);
        return $B;
}
function F($conexionBD, $id_proyec, $id, $he_men){
    $F = new stdClass();
    $equipo = new stdClass();
    $eq = array();
    $sqlPredec = mysqli_set_charset($conexionBD, "utf8"); 
    $sqlPredec = mysqli_query($conexionBD,
    "SELECT actividades.descripcion, equipo.descripcion,  rel_actv_equip.id_rel_mat_equip, rel_actv_equip.cant, equipo.unidad, equipo.PU, equipo.id_equip
    FROM actividades, equipo, rel_actv_equip
    WHERE rel_actv_equip.id_actividad = '$id' AND rel_actv_equip.id_equip = equipo.id_equip AND actividades.id_actividad = rel_actv_equip.id_actividad");
    if(mysqli_num_rows($sqlPredec) > 0){
        //$equipo = mysqli_fetch_all($sqlPredec,MYSQLI_ASSOC);
        while($row2 = mysqli_fetch_array($sqlPredec)){
            $nombre = $row2[1];
            $unidad = $row2[4];
            $cant = $row2[3];
            $pu = intersecc($conexionBD, 'pu_us_eq', $id_proyec, $row2[6], $row2[5]);
            $parcial = $row2[3] * $pu;
            $parcial = round($parcial,2);
            $equipo = ["nombre"=>$nombre, "unidad"=>$unidad, "cant"=>$cant, "pu"=>$pu, "parcial"=> $parcial];
            array_push($eq, $equipo);
        }
         $F -> data = true; 
  
    } else { 
        $F -> data = false; 
        $F -> msj = "SIN EQUIPO"; 
    }
    $sum = 0;
    $F-> equipo = $eq;
        foreach($eq as $e=>$value){
            $sum = $sum + $value['parcial'];
        }
    $F -> F = round($sum); 
    $g = round(($sum*($he_men/100)),2);
    $F -> G = round($g);
    $F -> H = round($g+$sum,2);

    return $F;

}

function intersecc($conexionBD, $tabla, $id_proyecto, $id_insumo, $p_insumo){
    $sqlPredec = mysqli_set_charset($conexionBD, "utf8"); 
    $sqlPredec = mysqli_query($conexionBD,
                 "SELECT id, pu_us
                  FROM $tabla
                  WHERE id_proyecto = '$id_proyecto'
                  AND id_insumo = '$id_insumo'");
        if(mysqli_num_rows($sqlPredec) > 0){
            while($row2 = mysqli_fetch_array($sqlPredec)){
                return $row2[1];
            }

        }else { 
            return $p_insumo;
        }

}



?>