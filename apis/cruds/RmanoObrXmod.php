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

if(isset($_GET["RmanoObrXmod"])){
    $data = json_decode(file_get_contents("php://input"));
    $id_proyec = $data->id_proyec;
    //$id_proyec = 11;
    $modsXproyecto = Array();
    $actiXmods = new stdClass();
    $proy = new stdClass();
    $Ben_Soc = 0;
    $iva = 0;
    $he_men = 0;
    $g_grales = 0;
    $utilidad = 0;
    $IT = 0;
    $nom_proy = "";
    $cadena ='';
        
    $sql2 = mysqli_query($conexionBD,"SELECT * FROM proyectos WHERE id_proyec = '".$id_proyec."'")
    or die(mysqli_error());
    if(mysqli_num_rows($sql2) > 0){
       while($row2 = mysqli_fetch_array($sql2)){
            $nom_proy = $row2[2];
            $Ben_Soc = floatval($row2[5]);
            $iva = floatval($row2[6]);
            $he_men = floatval($row2[7]);
            $g_grales = floatval($row2[8]);
            $utilidad = floatval($row2[9]);
            $IT = floatval($row2[10]);
            $cliente = $row2[11];
            $tip_cambio = floatval($row2[12]);
            $fecha = $row2[13];
            $proy = [
                'id_proyec' => $row2[0],
                'nom_proy' => $row2[2],
                'Ben_Soc' => $row2[5],
                'iva' => $row2[6],
                'he_men' => $row2[7],
                'g_grales' => $row2[8],
                'utilidad' => $row2[9],
                'IT' => $row2[10],
                'cliente' => $row2[11],
                'tip_cambio' => $row2[12],
                'fecha' => $row2[13],
                'lugar' => $row2[14]
            ];
        }
    }
    $sqlPredec = mysqli_query($conexionBD,"SELECT * FROM modulos WHERE id_proyec = $id_proyec ORDER by orden ASC");
    if(mysqli_num_rows($sqlPredec) > 0){
        while($row3 = mysqli_fetch_array($sqlPredec)){
        
            $actiXmods = [
                'modulo'=> ['id_modulo' => $row3[0], 
                            'nombre' => $row3[3], 
                            'orden'=>$row3[2],
                            'codigo'=>$row3[4],
                            'fecha_inicio'=>$row3[6],
                            'listadeinsumos'=>actiXmods($conexionBD,$id_proyec,$row3[0],$Ben_Soc,$iva,$he_men,$g_grales,$utilidad,$IT)
                            ],
                
            ];
            array_push($modsXproyecto, $actiXmods);
        }
        $proyecto -> proyecto = $proy;
        $proyecto -> modulos = $modsXproyecto;
        echo json_encode($proyecto);
        exit();
    }
    else{  echo json_encode(["success"=>0]); }    
}
function actiXmods($conexionBD, $id_proyec, $id_modulo, $Ben_Soc,$iva,$he_men,$g_grales,$utilidad,$IT){
    $listadeActiv = array();
    $actividad = new stdClass();
    $materiales = array();
    
    $sqlPredec = mysqli_query($conexionBD,
    "SELECT rel_actv_modulo.id_rel_am, actividades.id_actividad, actividades.descripcion, actividades.unidad, 
            rel_actv_modulo.catidad, rel_actv_modulo.unitario
     FROM actividades, rel_actv_modulo
     WHERE rel_actv_modulo.id_modulo = '".$id_modulo."' 
     AND actividades.id_actividad = rel_actv_modulo.id_actividad 
     ORDER BY rel_actv_modulo.orden");

    if(mysqli_num_rows($sqlPredec) > 0){
        while($row2 = mysqli_fetch_array($sqlPredec)){
            $cantXmod = $row2[4];
            $dataActv= [
                'id_rel_am' => $row2[0],
                'id_actividad' => $row2[1],
                'descripcion' => $row2[2],
                'unidad' => $row2[3],
                'cantidad' =>  $row2[4],
                'Materiales' => A($conexionBD, $id_proyec, $row2[1], $cantXmod)
            ];
            array_push($listadeActiv, $dataActv);
        }
        return $listadeActiv;
    }
    else{  
        $actividad = [
            'actividad'=> [
                'id_rel_am' => 0,
                'id_actividad' => 0,
                'descripcion' => 'SIN MANO DE OBRA',
                'unidad' => 0,
                'catidad' =>  0
                ],
            'parcial'=> 0,
            'unitario'=> 0,
        ];
        array_push($listadeActiv, $actividad);
        return $listadeActiv;
    }
}
function A($conexionBD, $id_proyec, $id, $cantXmod){
    $actividad = new stdClass();
    $materiales = array();
    $sqlPredec = mysqli_set_charset($conexionBD, "utf8"); 
    $sqlPredec = mysqli_query($conexionBD,
                              "SELECT mano_obra.descripcion,  rel_actv_mo.id_rel_mat_mo, rel_actv_mo.cant, mano_obra.unidad, mano_obra.PU, mano_obra.id_mo
                               FROM actividades, mano_obra, rel_actv_mo
                               WHERE rel_actv_mo.id_actividad = '$id' AND rel_actv_mo.id_mo = mano_obra.id_mo AND actividades.id_actividad = rel_actv_mo.id_actividad ORDER by jerarquia ASC");
        if(mysqli_num_rows($sqlPredec) > 0){
               while($row2 = mysqli_fetch_array($sqlPredec)){
                    $pu = intersecc($conexionBD, 'pu_us_mo', $id_proyec, $row2[5], $row2[4]);
                    $parcial = $row2[2] * $pu;
                    $parcial = $parcial * $cantXmod;
                    $cantXmodT = $row2[2] * $cantXmod;
                    $actividad = [
                        'insumo' => $row2[0],
                        'unidad' => $row2[3],
                        'PU' => $pu,
                        'parcial' => round($parcial),
                        'cantidad' => round($row2[2]),
                        'cantXmod' => round($cantXmodT,2)
                    ];
                    array_push($materiales, $actividad);
                }
                
        } else { 
            $actividad -> insumo = 'SIN MANO DE OBRA';
            array_push($materiales, $actividad);
            
        }
        return $materiales; 
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