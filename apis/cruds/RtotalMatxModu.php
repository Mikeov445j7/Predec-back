<?php
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: access");
    header("Access-Control-Allow-Methods: GET,POST");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    // Conecta a la base de datos  con usuario, contrase�a y nombre de la BD
    //$servidor = "localhost:3306"; $usuario = "boliviad_bduser1"; $contrasenia = "Prede02082016"; $nombreBaseDatos = "boliviad_predeconst";
    //$servidor = "localhost"; $usuario = "root"; $contrasenia = ""; $nombreBaseDatos = "predeconst";
    $servidor = "localhost:3306"; $usuario = "www_root"; $contrasenia = "RcomiC150980"; $nombreBaseDatos = "www_predeconst";
    $conexionBD = new mysqli($servidor, $usuario, $contrasenia, $nombreBaseDatos);
 if(isset($_GET["RtotalMatxModu"])){
    $data = json_decode(file_get_contents("php://input"));
    $id_proyec = $data->id_proyec;
    //$id_proyec = 11;
    //$proyecto = new stdClass();
    $modsXproyecto = Array();
    $actiXmods = new stdClass();
    $proy = new stdClass();
    $i = array();
    $Ben_Soc = 0;
    $iva = 0;
    $he_men = 0;
    $g_grales = 0;
    $utilidad = 0;
    $IT = 0;
    $nom_proy = "";
    $cadena ='';
    $sql2 = mysqli_set_charset($conexionBD, "utf8"); 
    $sql2 = mysqli_query($conexionBD,"SELECT * FROM proyectos WHERE id_proyec = '".$id_proyec."'")
    or die(mysqli_error());
    if(mysqli_num_rows($sql2) > 0){
       while($row2 = mysqli_fetch_array($sql2)){
            $proy ->id_proyec = $row2[0];
            $proy ->nom_proy = $row2[2];
            $proy ->Ben_Soc = $row2[5];
            $proy ->iva = $row2[6];
            $proy ->he_men = $row2[7];
            $proy ->g_grales = $row2[8];
            $proy ->utilidad = $row2[9];
            $proy ->IT = $row2[10];
            $proy ->cliente = $row2[11];
            $proy ->tip_cambio = $row2[12];
            $proy ->fecha = $row2[13];
            $proy ->lugar = $row2[14];
            $proyecto = [
                'proyecto' => $proy,
                'modulos' => modulos($id_proyec,  $conexionBD)
            ];

        }
        echo json_encode($proyecto);
    }
    
}

function modulos($id_proyec, $conexionBD){
    $modsXproyecto = Array();
    //$actiXmods = new stdClass();
    
    $sqlPredec = mysqli_query($conexionBD,"SELECT id_modulo, nombre, orden, codigo, fecha_inicio FROM modulos WHERE id_proyec = '".$id_proyec."' ORDER by orden ASC");
    if(mysqli_num_rows($sqlPredec) > 0){
        while($row3 = mysqli_fetch_array($sqlPredec)){
            $i = actiXmods($conexionBD, $row3[0]);
            $sum=0;
            foreach($i as $e=>$value){
                $sum = $sum + $value['costoT'];
            }

        $actiXmods =[
            "id_modulo" => $row3[0],
            "nombre" => $row3[1],
            "orden" => $row3[2],
            "codigo" => $row3[3],
            "fecha_inicio" => $row3[4],
            "insumos" => actiXmods($conexionBD, $row3[0]),
            "TotalModulo" => $sum
        ];
       
        array_push($modsXproyecto, $actiXmods);
        }
        return $modsXproyecto;
        
    }
    else{  echo json_encode(["success"=>0]); }    
}
function actiXmods($conexionBD, $id_modulo){
    $sqlPredec = mysqli_set_charset($conexionBD, "utf8"); 
    $listadeActiv = array();
    $dataActv = new stdClass();
    $sqlPredec = mysqli_query($conexionBD,
    "SELECT rel_actv_modulo.id_modulo AS modulos, 
            rel_actv_modulo.catidad AS cantXmodulo, 
            actividades.id_actividad AS idActividad, 
            actividades.descripcion AS desActividad, 
            actividades.unidad AS unid, 
            rel_actv_modulo.unitario AS unitXmodulo, 
            rel_actv_mat.id_mat As idMaterial, 
            materiales.descripcion AS insumo, 
            rel_actv_mat.cant_por_acti AS cantXactiv,
            materiales.PU AS pUnitario,
            SUM(rel_actv_mat.cant_por_acti),
            materiales.unidad AS insumoUnidad,
            SUM(rel_actv_modulo.catidad * rel_actv_mat.cant_por_acti) AS TotalCant
    FROM actividades, rel_actv_modulo, rel_actv_mat, materiales 
    WHERE rel_actv_modulo.id_modulo = $id_modulo
    AND actividades.id_actividad = rel_actv_modulo.id_actividad 
    AND rel_actv_mat.id_actividad = actividades.id_actividad 
    AND rel_actv_mat.id_mat = materiales.id_mat 
    GROUP by rel_actv_mat.id_mat"
    );

    if(mysqli_num_rows($sqlPredec) > 0){
        while($row2 = mysqli_fetch_array($sqlPredec)){
        
            $costoT = $row2[12] * $row2[9];
            $dataActv = [
                "modulo" => $row2[0],
                "cantXmodulo" => $row2[1],
                "idActividad" => $row2[2],
                "desActividad" => $row2[3],
                "unid" => $row2[11],
                "unitXmodulo" => $row2[5],
                "idMaterial" => $row2[6],
                "insumo" => $row2[7],
                "cantXactiv"  => $row2[8],
                "pUnitario"  => round($row2[9],2),
                "sumatoria" => $row2[10],
                "costoT" => round($costoT,2),
                "totalCantidad" =>round($row2[12],2)
            ];
            array_push($listadeActiv, $dataActv);
        }
        return $listadeActiv;
    }
    else{  
        $dataActv ->insumo = "SIN MATERIALES";
        $dataActv ->costoT = 0;
        array_push($listadeActiv, $dataActv);
        return $listadeActiv;
    }
}

     



?>


