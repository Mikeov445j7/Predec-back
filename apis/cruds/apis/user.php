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
    if($mail!=''&&$password!=''&&$nombre!=''&&$apellido!=''){
        $sqlPredec = mysqli_query($conexionBD,"INSERT INTO `preda_us`(`mail`, `password`, `nombre`, `apellido`) VALUES ('$mail','$password',' $nombre','$apellido')");
        echo json_encode(["success"=>1]);
    }

    exit();
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

?>
