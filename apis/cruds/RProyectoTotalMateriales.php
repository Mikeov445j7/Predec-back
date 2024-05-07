<?php
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: access");
    header("Access-Control-Allow-Methods: GET,POST");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    // Conecta a la base de datos  con usuario, contrase�a y nombre de la BD
    //$servidor = "localhost:3306"; $usuario = "boliviad_bduser1"; $contrasenia = "Prede02082016"; $nombreBaseDatos = "boliviad_predeconst";
    //$servidor = "localhost"; $usuario = "root"; $contrasenia = ""; $nombreBaseDatos = "predeconst";
    $servidor = "localhost"; $usuario = "c1402643_predec"; $contrasenia = "22poWEzodu"; $nombreBaseDatos = "c1402643_predec";
    $conexionBD = new mysqli($servidor, $usuario, $contrasenia, $nombreBaseDatos);

    if(isset($_GET["RProyectoTotalMateriales"])){

        //$data = json_decode(file_get_contents("php://input"));
        //$id_proyec = $data->id_proyec;
        $id_proyec = 11;
        //$proyecto = new stdClass();
        $modsXproyecto = Array();
        $actiXmods = new stdClass();
        $proy = new stdClass();
        $sql2 = mysqli_set_charset($conexionBD, "utf8"); 
        $sql2 = mysqli_query($conexionBD,"SELECT * FROM proyectos WHERE id_proyec = '".$id_proyec."'")
        or die(mysqli_error());
        if(mysqli_num_rows($sql2) > 0){
           while($row2 = mysqli_fetch_array($sql2)){
                $proy ->id_proyec = $row2[0];
                $proy ->nom_proy = $row2[2];
                $proy ->cliente = $row2[11];
                $proy ->fecha = $row2[13];
                $proy ->lugar = $row2[14];
                $proyecto = [
                    'proyecto' => $proy,
                    //'modulos' => modulos($id_proyec,  $conexionBD)
                ];
    
            }
            echo json_encode($proyecto);
        }
    }

?>