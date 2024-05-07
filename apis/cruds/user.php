<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET,POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Conecta a la base de datos  con usuario, contraseña y nombre de la BD
global $servidor;
//$servidor = "localhost:3306"; $usuario = "boliviad_bduser1"; $contrasenia = "Prede02082016"; $nombreBaseDatos = "boliviad_predeconst";
//$servidor = "localhost"; $usuario = "root"; $contrasenia = ""; $nombreBaseDatos = "predeconst";
$servidor = "localhost"; $usuario = "c1402643_predec"; $contrasenia = "22poWEzodu"; $nombreBaseDatos = "c1402643_predec";
$conexionBD = new mysqli($servidor, $usuario, $contrasenia, $nombreBaseDatos);
$tabla = "preda_us";

if (isset($_GET["listar"])){
    $sqlPredec = mysqli_set_charset($conexionBD, "utf8"); 
    $sqlPredec = mysqli_query($conexionBD,"SELECT * FROM preda_us");
    if(mysqli_num_rows($sqlPredec) > 0){
        $usuarios = mysqli_fetch_all($sqlPredec, MYSQLI_ASSOC);
        echo json_encode($usuarios);
    }
    else{  echo json_encode(["success"=>0]); }
}
if (isset($_GET["buscar"])){
    $sqlPredec = mysqli_set_charset($conexionBD, "utf8");
    $sqlPredec = mysqli_query($conexionBD,"SELECT * FROM preda_us WHERE cel LIKE '%".$_GET["buscar"]."%' OR mail LIKE '%".$_GET["buscar"]."%'");
    if(mysqli_num_rows($sqlPredec) > 0){
        $empleaados = mysqli_fetch_all($sqlPredec,MYSQLI_ASSOC);
        echo json_encode($empleaados);
        exit();
    }
    else{  echo json_encode(["success"=>0]); }
}
if (isset($_GET["verUs"])){
    $data = json_decode(file_get_contents("php://input"));
    $id_us = $data->id_us;
    $sqlPredec = mysqli_set_charset($conexionBD, "utf8"); 
    $sqlPredec = mysqli_query($conexionBD,"SELECT * FROM preda_us WHERE id_us = $id_us");
    if(mysqli_num_rows($sqlPredec) > 0){
        $usuarios = mysqli_fetch_all($sqlPredec, MYSQLI_ASSOC);
        echo json_encode($usuarios);
    }
    else{  echo json_encode(["success"=>0]); }
}
if (isset($_GET["verificauser"])){
    $sqlPredec = mysqli_set_charset($conexionBD, "utf8"); 
    $sqlPredec = mysqli_query($conexionBD,"SELECT * FROM preda_us WHERE mail = '".$_GET["verificauser"]."'");
    if(mysqli_num_rows($sqlPredec) > 0){
        echo json_encode(["success"=>1]);
    }
    else{  echo json_encode(["success"=>0]); }
}

if(isset($_GET["insertar"])){
    $data = json_decode(file_get_contents("php://input"));
    $mail=$data->mail;
    $password=$data->password;
    $nombre=$data->nombre;
    $apellido=$data->apellido;
    $cel=$data->cel;
    if($mail!=''&&$password!=''&&$nombre!=''&&$apellido!=''){
        $sqlPredec = mysqli_query($conexionBD,
        "INSERT INTO `preda_us`(`mail`, `password`, `nombre`, `apellido`, `genero`, `acercade`, `ip_user`, `img_profile`, `uninique_id`, `activo`, `distri`, `ci`, `premiun`, `cel`)
         VALUES ('$mail','$password',' $nombre','$apellido','5','6','0','8','9','10','11','0','0', '$cel')");
        
            echo json_encode(["success"=>1]);
            exit();
     
    }
}
if(isset($_GET["editar"])){
    $data = json_decode(file_get_contents("php://input"));
    $id_us = $data->id_us;
    $nombre=$data->nombre;
    $apellido=$data->apellido;
    $ci =$data->ci;
    $cel =$data->cel;
    if($nombre!=''&&$apellido!=''&&$ci!=''&&$cel!=''){
        $sqlPredec = mysqli_query($conexionBD,
            "UPDATE `preda_us` SET `nombre`='$nombre',`apellido`='$apellido',`ci`='$ci',`cel`='$cel' WHERE id_us = $id_us");
            echo json_encode(["success"=>1]);
            exit();
    }else {
        echo json_encode(["success"=>0]);
    }
}
if(isset($_GET["premiun"])){
    $data = json_decode(file_get_contents("php://input"));
    $id_us = $data->id_us;
    $fecha_Expiracion = $data->fecha_Expiracion;
    if($id_us!='' && $fecha_Expiracion!=''){
        $sqlPredec = mysqli_query($conexionBD,
            "UPDATE `preda_us` SET `premiun`= 7, `fecha_Expiracion` = '$fecha_Expiracion' WHERE id_us = $id_us");
            echo json_encode(["success"=>1]);
            exit();
    }else {
        echo json_encode(["success"=>0]);
    }
}

if (isset($_GET["login"])){
    $data = json_decode(file_get_contents("php://input"));
    $mail=$data->mail;
    $password=$data->password;
    $sqlPredec = mysqli_set_charset($conexionBD, "utf8"); 
    $sqlPredec = mysqli_query($conexionBD,"SELECT * FROM preda_us WHERE mail = '$mail' AND password = '$password' ");
    if(mysqli_num_rows($sqlPredec) > 0){
        $usuarios = mysqli_fetch_all($sqlPredec, MYSQLI_ASSOC);
        echo json_encode($usuarios);
    }
}
if (isset($_GET["contarUs"])){
    $usuarios = new stdClass();
    $sqlPredec = mysqli_set_charset($conexionBD, "utf8"); 
    $sqlPredec = mysqli_query($conexionBD,
               "SELECT * FROM preda_us WHERE premiun = 7;");
        if(mysqli_num_rows($sqlPredec) > 0){
                $premium = mysqli_num_rows( $sqlPredec ); //mysqli_fetch_all($sqlPredec,MYSQLI_ASSOC);
        }else { $premium = "0";}
    $sqlPredec = mysqli_query($conexionBD,
               "SELECT * FROM preda_us WHERE premiun = 0;");
        if(mysqli_num_rows($sqlPredec) > 0){
                $libre = mysqli_num_rows( $sqlPredec ); //mysqli_fetch_all($sqlPredec,MYSQLI_ASSOC);
        }else { $libre = "0";}
    $sqlPredec = mysqli_query($conexionBD,
               "SELECT * FROM preda_us WHERE premiun = 8;");
        if(mysqli_num_rows($sqlPredec) > 0){
                $admins = mysqli_num_rows( $sqlPredec ); //mysqli_fetch_all($sqlPredec,MYSQLI_ASSOC);
        }else { $admins = "0";}


        $usuarios -> premium = $premium;
        $usuarios -> libre = $libre;
        $usuarios -> admins = $admins;

    echo json_encode($usuarios);
}

?>
