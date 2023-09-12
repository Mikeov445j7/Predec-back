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


if(isset($_GET["CopiarProyecto"])){

        $data = json_decode(file_get_contents("php://input"));
        $id_proyec = $data->id_proyec;
        $id_us_destino = $data->id_us_destino;
        //$id_proyec = 10;
        //$id_us_destino = 10;
        $sql = mysqli_query($conexionBD,"SELECT * FROM proyectos WHERE id_proyec = '".$id_proyec."'");
    if(mysqli_num_rows($sql) > 0){
        while($row = mysqli_fetch_array($sql)){
                $nombre  = $row[2];
                $codigo  = $row[3];
                $fecha_creacion  = $row[4];
                $Ben_Soc  = $row[5];
                $iva  = $row[6];
                $he_men  = $row[7];
                $g_grales  = $row[8];
                $utilidad  = $row[9];
                $IT  = $row[10];
                $cliente  = $row[11];
                $tip_cambio  = $row[12];
                $fecha  = $row[13];
                $ubicacion  = $row[14];
                
                //echo json_encode($nombre);
                copia_proyecto($conexionBD, $id_us_destino, $id_proyec,$nombre,$codigo,$Ben_Soc,$iva,$he_men,$g_grales,$utilidad,$IT,$cliente,$tip_cambio,$fecha,$ubicacion);
                //copia_proyecto($id_proyec,$nombre,$codigo,$Ben_Soc,$iva,$he_men,$g_grales,$utilidad,$IT,$cliente,$tip_cambio,$fecha,$ubicacion);
        }
        //cerrar_notificacion($id_envio);
    }
}
function copia_proyecto($conexionBD, $id_us, $id_proyecOr,$nombre,$codigo,$Ben_Soc,$iva,$he_men,$g_grales,$utilidad,$IT,$cliente,$tip_cambio,$fecha,$ubicacion){
    $fecha_creacion = Date('Y-m-d');
   $insertar = mysqli_query($conexionBD, 
   "INSERT INTO `proyectos`(`id_us`, `nombre`, `codigo`, `fecha_creacion`, `Ben_Soc`, `iva`, `he_men`, `g_grales`, `utilidad`, `IT`, `cliente`, `tip_cambio`, `fecha`, `ubicacion`, `id_proyecOr`) 
                    VALUES ('$id_us', '$nombre', '$codigo', '$fecha_creacion', '$Ben_Soc', '$iva', '$he_men', '$g_grales', '$utilidad', '$IT','$cliente', '$tip_cambio', '$fecha', '$ubicacion', '$id_proyecOr')");
                    
                    /*if ($conexionBD->query($insertar) === TRUE) {
                        echo "New record created successfully";
                      } else {
                        echo "Error: " . $insertar . "<br>" . $conexionBD->error;
                      }*/
                   if($insertar){
                        $sql = mysqli_query($conexionBD,"SELECT id_proyec FROM proyectos WHERE id_proyecOr = '".$id_proyecOr."' AND id_us = '".$id_us."'");
                                    if(mysqli_num_rows($sql) > 0){
                                        while($row = mysqli_fetch_array($sql)){
                                                $id_proyec = $row[0];
                                        }
                                        
                                    }
                        
                        llama_modulos($conexionBD, $id_proyec, $id_proyecOr);
                    }
    
}
function llama_modulos($conexionBD, $id_proyec, $id_proyecOr){
    
    $sql = mysqli_query($conexionBD, "SELECT * FROM modulos WHERE id_proyec = '".$id_proyecOr."'");
    if(mysqli_num_rows($sql) > 0){
   
            while($row = mysqli_fetch_array($sql)){
          
                   copia_modulo($conexionBD, $id_proyec, $row[2], $row[3], $row[4], $row[0]);
            }
    }
}
function copia_modulo($conexionBD, $id_proyec,$orden,$nombre,$codigo,$id_modOr){
    $fecha_inicio = Date('Y-m-d');
    $insertar = mysqli_query( $conexionBD, "INSERT INTO `modulos`(`id_proyec`, `orden`, `nombre`, `codigo`, `id_modOr`, `fecha_inicio`) 
                             VALUES ('$id_proyec', '$orden', '$nombre', '$codigo', '$id_modOr', '$fecha_inicio')");
                if($insertar){
                   $sql = mysqli_query($conexionBD, "SELECT id_modulo FROM modulos WHERE $id_modOr = '".$id_modOr."'");

                    /*if ($conexionBD->query($insertar) === TRUE) {
                        echo "New record created successfully";
                      } else {
                        echo "Error: " . $insertar . "<br>" . $conexionBD->error;
                      }*/
                    if(mysqli_num_rows($sql) > 0){
                                        while($row = mysqli_fetch_array($sql)){
                                                $id_modulo = $row[0];
                                        }
                                    }
                  //echo json_encode($id_proyec);
                  Llama_actividades($conexionBD,$id_modulo,$id_modOr);
                }

}
function Llama_actividades($conexionBD, $id_modulo,$id_modOr){
    $fecha_ini_actv = Date('Y-m-d');
    $fecha_fin_actv = Date('Y-m-d');
    $sql = mysqli_query($conexionBD, "SELECT * FROM rel_actv_modulo WHERE id_modulo = '".$id_modOr."'");
    if(mysqli_num_rows($sql) > 0){
            while($row = mysqli_fetch_array($sql)){
                    $insertar = mysqli_query($conexionBD,"INSERT INTO `rel_actv_modulo`(`id_modulo`, `id_actividad`, `catidad`, `unitario`, `orden`) 
                                            VALUES ($id_modulo, $row[2], $row[3], $row[4], $row[5])"); 
                                    
                                if($insertar){
                                        //echo "actividades copiadas<br>";
                                        echo json_encode(["success"=>1]);
                                }
                           /*if ($conexionBD->query($insertar) === TRUE) {
                                echo "New record created successfully";
                            } else {
                                echo "Error: " . $insertar . "<br>" . $conexionBD->error;
                            }*/
            }
    }
}

?>