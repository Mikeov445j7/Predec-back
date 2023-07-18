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

    if(isset($_GET["RpXmod"])){
        $data = json_decode(file_get_contents("php://input"));
        $id_proyec = $data->id_proyec;
        $modsXproyecto = Array();
        $actiXmods = new stdClass();
        $proy = new stdClass();
        //$id_proyec = 2;
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
                                    'fecha_inicio'=>$row3[6]
                                    ],
                        'listadeActiv'=>actiXmods($conexionBD,$row3[0],$Ben_Soc,$iva,$he_men,$g_grales,$utilidad,$IT)
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
    function actiXmods($conexionBD, $id_modulo, $Ben_Soc,$iva,$he_men,$g_grales,$utilidad,$IT){
        $listadeActiv = array();
        
        $sqlPredec = mysqli_query($conexionBD,
        "SELECT rel_actv_modulo.id_rel_am, actividades.id_actividad, actividades.descripcion, actividades.unidad, 
                rel_actv_modulo.catidad, rel_actv_modulo.unitario
         FROM actividades, rel_actv_modulo
         WHERE rel_actv_modulo.id_modulo = '".$id_modulo."' 
         AND actividades.id_actividad = rel_actv_modulo.id_actividad 
         ORDER BY rel_actv_modulo.orden");
    
        if(mysqli_num_rows($sqlPredec) > 0){
            //$actividades = mysqli_fetch_all($sqlPredec,MYSQLI_ASSOC);
            while($row2 = mysqli_fetch_array($sqlPredec)){
                $cantXmod = $row2[4];
                $dataActv= [
                    'id_rel_am' => $row2[0],
                    'id_actividad' => $row2[1],
                    'descripcion' => $row2[2],
                    'unidad' => $row2[3],
                    'catidad' =>  $row2[4]
                ];
                $A = A($conexionBD, $row2[1]);
                $N = B($conexionBD, $row2[1]);
                $C = F($conexionBD, $row2[1]);
                $A = floatval($A);
                $N = floatval($N);
                $C = floatval($C);
                
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
                $unitario = $Q;
                $parcial = $Q * $cantXmod;
                $actividad = [
                    'actividad'=> $dataActv,
                    'parcial'=> $parcial,
                    'unitario'=> $unitario,
                    'Ben_Soc'=> $Ben_Soc,
                    'iva'=>$iva,
                    'he_men'=>$he_men,
                    'g_grales'=>$g_grales,
                    'utilidad'=>$utilidad,
                    'IT'=>$IT,
                    'A' => $A,
                    'N' => $N,
                    'C' => $C,
                    'E' => $E,
                    'F' => $F,
                    'G' => $G,
                    'H' => $H,
                    'I' => $I,
                    'L' => $L,
                    'M' => $M,
                    'P' => $P,
                    'Q' => $Q
                ];

                array_push($listadeActiv, $actividad);
            }
            //$listadeActiv = $actividades;
            return $listadeActiv;
        }
        else{  

            $actividad = [
                'actividad'=> [
                    'id_rel_am' => 0,
                    'id_actividad' => 0,
                    'descripcion' => 'SIN ACTIVIDADES',
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
    function A($conexionBD, $id){
        $sqlPredec = mysqli_set_charset($conexionBD, "utf8"); 
        $sqlPredec = mysqli_query($conexionBD,"SELECT actividades.descripcion, materiales.descripcion, rel_actv_mat.id_rel, rel_actv_mat.cant_por_acti, materiales.unidad, materiales.PU
                    FROM actividades, materiales, rel_actv_mat
                    WHERE rel_actv_mat.id_actividad = '$id' AND rel_actv_mat.id_mat = materiales.id_mat AND actividades.id_actividad = rel_actv_mat.id_actividad ");
            if(mysqli_num_rows($sqlPredec) > 0){
                    //$materiales = mysqli_fetch_all($sqlPredec,MYSQLI_ASSOC);
                    $sum = 0;
                    while($row2 = mysqli_fetch_array($sqlPredec)){
                        $parcial = $row2[3] * $row2[5];
                        $sum = $sum + $parcial;
                    }
                    $A = $sum;
            } else { 
                $A = 0;
            }
            return $A; 
    }
    function B($conexionBD, $id){
        $sqlPredec = mysqli_set_charset($conexionBD, "utf8");
        $sqlPredec = mysqli_query($conexionBD,
            "SELECT actividades.descripcion, mano_obra.descripcion,  rel_actv_mo.id_rel_mat_mo, rel_actv_mo.cant, mano_obra.unidad, mano_obra.PU
             FROM actividades, mano_obra, rel_actv_mo
             WHERE rel_actv_mo.id_actividad = '$id' AND rel_actv_mo.id_mo = mano_obra.id_mo AND actividades.id_actividad = rel_actv_mo.id_actividad ");
            if(mysqli_num_rows($sqlPredec) > 0){
                //$manoObra = mysqli_fetch_all($sqlPredec,MYSQLI_ASSOC);
                $sum = 0;
                while($row2 = mysqli_fetch_array($sqlPredec)){
                    $parcial = $row2[3] * $row2[5];
                    $sum = $sum + $parcial;
                }
                $B = $sum;
    
            } else { 
                $B = 0;
            }
            return $B;
    }
    function F($conexionBD, $id){
        $sqlPredec = mysqli_set_charset($conexionBD, "utf8");
        $F = new stdClass();
        $equipo = new stdClass();
        $eq = array();
        $sqlPredec = mysqli_query($conexionBD,
        "SELECT actividades.descripcion, equipo.descripcion,  rel_actv_equip.id_rel_mat_equip, rel_actv_equip.cant, equipo.unidad, equipo.PU 
        FROM actividades, equipo, rel_actv_equip
        WHERE rel_actv_equip.id_actividad = '$id' AND rel_actv_equip.id_equip = equipo.id_equip AND actividades.id_actividad = rel_actv_equip.id_actividad");
        if(mysqli_num_rows($sqlPredec) > 0){
            $sum = 0;
            while($row2 = mysqli_fetch_array($sqlPredec)){
                $parcial = $row2[3] * $row2[5];
                $sum = $sum + $parcial;
            }
            $F = $sum;
        } else { 
            $F = 0;
        }
        return $F;
    }

?>