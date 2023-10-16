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

    if(isset($_GET["RpresuGral"])){
        $data = json_decode(file_get_contents("php://input"));
        $id_proyec = $data->id_proyec;
        //$id_proyec = 11;
        $Ben_Soc = 0;
        $iva = 0;
        $he_men = 0;
        $g_grales = 0;
        $utilidad = 0;
        $IT = 0;
        $nom_proy = "";
        $cadena ='';
        $RpresuGral = new stdClass();
        $totales = array();
        $tabla = array();
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
                $RpresuGral -> proyecto = $proy;
                $RpresuGral -> calculo = modulos($conexionBD, $id_proyec);
      
            }
            $totales = $RpresuGral -> calculo;
            $sumTA = 0;
            foreach($totales as $a=>$value){
                $sumTA = $sumTA + $value->A;
            }
            $sumTB = 0;
            foreach($totales as $a=>$value){
                $sumTB = $sumTB + $value->B;
            }
            $sumTC = 0;
            foreach($totales as $a=>$value){
                $sumTC = $sumTC + $value->C;
            }
      
        }
        $A = $sumTA;
        $B = $sumTB;
        $C = $sumTC;
        $N = $B;
        //E Beneficios Sociales 55.00% de (N) =
        $E = round(($N*($Ben_Soc/100)),2);
        //F Impuesto IVA 14.94% de (N+E) = 
        $F = round(($N+$E)*($iva/100),2);
        //G TOTAL MANO DE OBRA (N+E+F) = 
        $G = round(($N+$E+$F),2);
        //H Herramientas menores 5.00% de (G) =
        $H = round(($G*($he_men/100)),2);
        //I TOTAL EQUIPO Y MAQUINARIA (C+H) = 
        $I = round(($C+$H),2);
        //L GASTOS GENERALES 7.00% de (A+G+I) =
        $L = round((($A+$G+$I)*($g_grales/100)),2);
        //M Utilidad 7.00% de (A+G+I+L) =
        $M = round((($A+$G+$I+$L)*($utilidad/100)),2);
        //P IT 3.09% de (A+G+I+L+M) = 0.58
        $P = round((($A+$G+$I+$L+$M)*($IT/100)),2);
        //Q TOTAL ITEM (A+G+I+L+M+P) =
        $Q = round(($A+$G+$I+$L+$M+$P),2);
          
            array_push($tabla,['n'=>'A' , 'parametro'=>'MATERIAL' , 'montoBs'=> round($A,2) , 'montoUS'=> round(($A/$tip_cambio),2) , 'formula'=> '-' , 'porcentaje'=>' ' , 'incidencia'=> round((($A/$Q)*100),2)]);
            array_push($tabla,['n'=>'B' , 'parametro'=>'MANO DE OBRA' , 'montoBs'=> round($B,2) , 'montoUS'=> round(($B/$tip_cambio),2) , 'formula'=> '-' , 'porcentaje'=>' ' , 'incidencia'=> round((($B/$Q)*100),2)]);
            array_push($tabla,['n'=>'C' , 'parametro'=>'EQUIPO Y MAQUINARIA' , 'montoBs'=> round($C,2) , 'montoUS'=> round(($C/$tip_cambio),2) , 'formula'=> '-' , 'porcentaje'=>' ' , 'incidencia'=> round((($C/$Q)*100),2)]);
            array_push($tabla,['n'=>'D' , 'parametro'=>'TOTAL MATERIALES' , 'montoBs'=> round($A,2) , 'montoUS'=> round(($A/$tip_cambio),2) , 'formula'=> 'A' , 'porcentaje'=>' ' , 'incidencia'=> round((($D/$Q)*100),2)]);
            array_push($tabla,['n'=>'E' , 'parametro'=>'Beneficios Sociales' , 'montoBs'=> round($E,2) , 'montoUS'=> round(($E/$tip_cambio),2) , 'formula'=> 'N' , 'porcentaje'=> $Ben_Soc.'%' , 'incidencia'=> round((($E/$Q)*100),2)]);
            array_push($tabla,['n'=>'F' , 'parametro'=>'Impuesto IVA' , 'montoBs'=> round($F,2) , 'montoUS'=> round(($F/$tip_cambio),2) , 'formula'=> 'N+E' , 'porcentaje'=> $iva.'%' , 'incidencia'=> round((($F/$Q)*100),2)]);
            array_push($tabla,['n'=>'G' , 'parametro'=>'TOTAL MANO DE OBRA' , 'montoBs'=> round($G,2) , 'montoUS'=> round(($G/$tip_cambio),2) , 'formula'=> 'N+E+F' , 'porcentaje'=>' ' , 'incidencia'=> round((($G/$Q)*100),2)]);
            array_push($tabla,['n'=>'H' , 'parametro'=>'Herramientas menores' , 'montoBs'=> round($H,2) , 'montoUS'=> round(($H/$tip_cambio),2) , 'formula'=> 'G' , 'porcentaje'=>$he_men.'%' , 'incidencia'=> round((($H/$Q)*100),2)]);
            array_push($tabla,['n'=>'I' , 'parametro'=>'TOTAL EQUIPO Y MAQUINARIA' , 'montoBs'=> round($I,2) , 'montoUS'=> round(($I/$tip_cambio),2) , 'formula'=> 'C+H' , 'porcentaje'=>' ' , 'incidencia'=> round((($I/$Q)*100),2)]);
            array_push($tabla,['n'=>'L' , 'parametro'=>'GASTOS GENERALES' , 'montoBs'=> round($L,2) , 'montoUS'=> round(($L/$tip_cambio),2) , 'formula'=> 'A+G+I' , 'porcentaje'=>$g_grales.'%' , 'incidencia'=> round((($L/$Q)*100),2)]);
            array_push($tabla,['n'=>'M' , 'parametro'=>'Utilidad' , 'montoBs'=> round($M,2) , 'montoUS'=> round(($M/$tip_cambio),2) , 'formula'=> 'A+G+I+L' , 'porcentaje'=>$utilidad.'%' , 'incidencia'=> round((($M/$Q)*100),2)]);
            array_push($tabla,['n'=>'N' , 'parametro'=>'Subtotal Mano de Obra' , 'montoBs'=> round($N,2) , 'montoUS'=> round(($N/$tip_cambio),2) , 'formula'=> 'B' , 'porcentaje'=>' ' , 'incidencia'=> round((($n/$Q)*100),2)]);
            array_push($tabla,['n'=>'P' , 'parametro'=>'IT' , 'montoBs'=> round($P,2) , 'montoUS'=> round(($P/$tip_cambio),2) , 'formula'=> 'A+G+I+L+M' , 'porcentaje'=>$IT.'%' , 'incidencia'=> round((($P/$Q)*100),2)]);
            array_push($tabla,['n'=>'Q' , 'parametro'=>'Total presupuesto:' , 'montoBs'=> round($Q,2) , 'montoUS'=> round(($Q/$tip_cambio),2) , 'formula'=> 'A+G+I+L+M+P' , 'porcentaje'=>' ' , 'incidencia'=> round((($Q/$Q)*100),2)]);
            $RpresuGral -> tabla = $tabla;
           echo json_encode($RpresuGral);
        exit();
    }
function modulos($conexionBD, $id_proyec){
    $presupuesto = new stdClass();
    $r = new stdClass();
    $mod = array();
    $A = 0;
    $B = 0;
    $C = 0;
    $A = floatval($A);
    $B = floatval($B);
    $C = floatval($C);
    $sqlPredec = mysqli_query($conexionBD,"SELECT * FROM modulos WHERE id_proyec = $id_proyec ORDER by orden ASC");
    if(mysqli_num_rows($sqlPredec) > 0){
 
        while($row3 = mysqli_fetch_array($sqlPredec)){
           $presupuesto =  actiXmods($conexionBD, $id_proyec, $row3[0], $row3[3]);

           array_push($mod, $presupuesto);

        }
    }
    else{  
        $A = 0;
        $B = 0;
        $C = 0;
     } 
     
     return $mod;
}
function actiXmods($conexionBD, $id_proyec, $id_modulo, $nom){
    $listadeActiv = array();
    
    $sqlPredec = mysqli_query($conexionBD,
    "SELECT rel_actv_modulo.id_rel_am, actividades.id_actividad, rel_actv_modulo.catidad
     FROM actividades, rel_actv_modulo
     WHERE rel_actv_modulo.id_modulo = '".$id_modulo."' 
     AND actividades.id_actividad = rel_actv_modulo.id_actividad 
     ORDER BY rel_actv_modulo.orden");

    if(mysqli_num_rows($sqlPredec) > 0){
        //$actividades = mysqli_fetch_all($sqlPredec,MYSQLI_ASSOC);
        while($row2 = mysqli_fetch_array($sqlPredec)){
            $cantXmod = $row2[2];
            $A = Mat($conexionBD, $id_proyec, $row2[1]);
            $N = Mo($conexionBD, $id_proyec, $row2[1]);
            $C = Eq($conexionBD, $id_proyec, $row2[1]);
            $A = $A * $cantXmod;
            $N = $N * $cantXmod;
            $C = $C * $cantXmod;
            $actividad = [
                'A' => $A,
                'B' => $N,
                'C' => $C,
             ];
             array_push($listadeActiv, $actividad);
        }
       // $sumA = array();
        $sumA = 0;
        foreach($listadeActiv as $a=>$value){
            $sumA = $sumA + $value['A'];
        }
        $T = new stdClass();
        $T -> A = $sumA;
        $T -> mod = $nom;
        $sumB = 0;
        foreach($listadeActiv as $a=>$value){
            $sumB = $sumB + $value['B'];
        }
        $T -> B = $sumB;
        $sumC = 0;
        foreach($listadeActiv as $a=>$value){
            $sumC = $sumC + $value['C'];
        }
        $T -> C = $sumC;

        return $T;
    }
    else{  

        $T = [
            'mod' => $nom,
            'A' => 0,
            'B' => 0,
            'C' => 0
        ];

        return $T;
    }
}
function Mat($conexionBD, $id_proyec, $id){
    $sqlPredec = mysqli_set_charset($conexionBD, "utf8");
    $sqlPredec = mysqli_query($conexionBD,
    "SELECT actividades.descripcion, materiales.descripcion, rel_actv_mat.id_rel, rel_actv_mat.cant_por_acti, materiales.unidad, materiales.PU, materiales.id_mat
    FROM actividades, materiales, rel_actv_mat
    WHERE rel_actv_mat.id_actividad = '$id' AND rel_actv_mat.id_mat = materiales.id_mat AND actividades.id_actividad = rel_actv_mat.id_actividad");
        if(mysqli_num_rows($sqlPredec) > 0){
            //$manoObra = mysqli_fetch_all($sqlPredec,MYSQLI_ASSOC);
            $sum = 0;
            while($row2 = mysqli_fetch_array($sqlPredec)){
                $pu = intersecc($conexionBD, 'pu_us_mat', $id_proyec, $row2[6], $row2[5]);
                $parcial = $row2[3] * $pu;
                $sum = $sum + $parcial;
            }
            $A = $sum;

        } else { 
            $A = 0;
        }
        return $A;
}
function Mo($conexionBD, $id_proyec, $id){
    $sqlPredec = mysqli_set_charset($conexionBD, "utf8");
    $sqlPredec = mysqli_query($conexionBD,
        "SELECT actividades.descripcion, mano_obra.descripcion,  rel_actv_mo.id_rel_mat_mo, rel_actv_mo.cant, mano_obra.unidad, mano_obra.PU, mano_obra.id_mo
         FROM actividades, mano_obra, rel_actv_mo
         WHERE rel_actv_mo.id_actividad = '$id' AND rel_actv_mo.id_mo = mano_obra.id_mo AND actividades.id_actividad = rel_actv_mo.id_actividad ");
        if(mysqli_num_rows($sqlPredec) > 0){
            //$manoObra = mysqli_fetch_all($sqlPredec,MYSQLI_ASSOC);
            $sum = 0;
            while($row2 = mysqli_fetch_array($sqlPredec)){
                $pu = intersecc($conexionBD, 'pu_us_mo', $id_proyec, $row2[6], $row2[5]);
                $parcial = $row2[3] * $pu;
                $sum = $sum + $parcial;
            }
            $B = $sum;

        } else { 
            $B = 0;
        }
        return $B;
}
function eq($conexionBD, $id_proyec, $id){
    $sqlPredec = mysqli_set_charset($conexionBD, "utf8");
    $sqlPredec = mysqli_query($conexionBD,
    "SELECT actividades.descripcion, equipo.descripcion,  rel_actv_equip.id_rel_mat_equip, rel_actv_equip.cant, equipo.unidad, equipo.PU, equipo.id_equip 
    FROM actividades, equipo, rel_actv_equip
    WHERE rel_actv_equip.id_actividad = '$id' AND rel_actv_equip.id_equip = equipo.id_equip AND actividades.id_actividad = rel_actv_equip.id_actividad");
    if(mysqli_num_rows($sqlPredec) > 0){
        $sum = 0;
        while($row2 = mysqli_fetch_array($sqlPredec)){
            $pu = intersecc($conexionBD, 'pu_us_eq', $id_proyec, $row2[6], $row2[5]);
            $parcial = $row2[3] * $pu;
            $sum = $sum + $parcial;
        }
        $F = $sum;
    } else { 
        $F = 0;
    }
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