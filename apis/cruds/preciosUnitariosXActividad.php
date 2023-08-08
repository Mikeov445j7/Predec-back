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

if(isset($_GET["PUXact"])){
    
    $data = json_decode(file_get_contents("php://input"));
    echo "holaaaa";
    $id_proyec = $data->id_proyec;
    $id_mod = $data->id_mod;
    $id_actividad= $data->id_actividad;
    $t_precios = 1;
    $consulta = '';
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
     
            
    //$s = mysql_query("SELECT actividades.descripcion,  FROM actividades WHERE id_actividad = '".$id_actividad ."'")
    $s = mysqli_query($conexionBD, "SELECT modulos.nombre, actividades.descripcion, actividades.unidad, rel_actv_modulo.catidad
                        FROM actividades, rel_actv_modulo, modulos
                        WHERE modulos.id_modulo = '".$id_mod."' 
                        AND rel_actv_modulo.id_modulo = '".$id_mod."' 
                        AND actividades.id_actividad = ".$id_actividad." 
                        AND actividades.id_actividad = rel_actv_modulo.id_actividad");
        if(mysqli_num_rows($s) > 0){
            
               while($r = mysqli_fetch_array($s)){
                        $nModulo = $r[0];
                        $desc = $r[1];
                        $unidad = $r[2];
                        $cantidad = $r[3];
                        
                    }
                 $cadena = $cadena."<table style = ' border:1px solid #000; border-spacing: 0; width:100%;'>";
                            $cadena = $cadena."<tr>";
                                $cadena = $cadena."<td style='border:none;'>Item: ".$desc."</td>";
                                $cadena = $cadena."<td style='border:none;'></td>";
                                $cadena = $cadena."<td style='border:none;'></td>";
                                $cadena = $cadena."<td style='border:none;'></td>";
                                $cadena = $cadena."<td style='border:none;'></td>";
                                $cadena = $cadena."<td style='border:none;'></td>";
                                $cadena = $cadena."<td style='text-align: right; border:none;'>".round($cantidad,2)." ".$unidad ."</td>";
                            $cadena = $cadena."</tr>";
                            $cadena = $cadena."<tr>";
                                $cadena = $cadena."<td style='border:none;'>MODULO: ".$nModulo."  Proyecto: ".$nom_proy."</td>";
                                $cadena = $cadena."<td style='border:none;'></td>";
                                $cadena = $cadena."<td style='border:none;'></td>";
                                $cadena = $cadena."<td style='border:none;'></td>";
                                $cadena = $cadena."<td style='border:none;'></td>";
                                $cadena = $cadena."<td style='border:none;'></td>";
                                $cadena = $cadena."<td style='text-align: right; border:none;'>fecha: ".$fecha." </td>";
                            $cadena = $cadena."</tr>";
                            $cadena = $cadena."<tr>";
                                $cadena = $cadena."<td style='border:none;'>Cliente: ".$cliente." </td>";
                                $cadena = $cadena."<td style='border:none;'></td>";
                                $cadena = $cadena."<td style='border:none;'></td>";
                                $cadena = $cadena."<td style='border:none;'></td>";
                                $cadena = $cadena."<td style='border:none;'></td>";
                                $cadena = $cadena."<td style='border:none;'></td>";
                                $cadena = $cadena."<td style='text-align: right; border:none;'>Tipo Cambio:".$tip_cambio." </td>";
                            $cadena = $cadena."</tr>";
                     $cadena = $cadena."</table>";       
          }
          
            $cadena = $cadena."<table style = 'border-spacing: 0; width:100%; font-family:Arial;'>";
            $cadena = $cadena."<tr>
                    <th style ='border:1px solid #000; text-align: center;'>N°</th>
                    <th style ='border:1px solid #000; text-align: center;'>P.</th>
                    <th style ='border:1px solid #000; text-align: center;'>Insumo/Parámetro</th>
                    <th style ='border:1px solid #000; text-align: center;'>Und.</th>
                    <th style ='border:1px solid #000; text-align: center;'>Cant.</th>
                    <th style ='border:1px solid #000; text-align: center;'>Unit. (Bs)</th>
                    <th style ='border:1px solid #000; text-align: center;'>Parcial (Bs)</th>
                </tr>";
    
    $sql = mysqli_query($conexionBD,"SELECT actividades.descripcion, materiales.descripcion,  rel_actv_mat.cant_por_acti, materiales.unidad, materiales.PU, materiales.id_mat
                        FROM actividades, materiales, rel_actv_mat
                        WHERE rel_actv_mat.id_actividad = '".$id_actividad."'
                        AND rel_actv_mat.id_mat = materiales.id_mat
                        AND actividades.id_actividad = rel_actv_mat.id_actividad ");
        if(mysqli_num_rows($sql) > 0){
            $n = 0;
                        $cadena = $cadena."<tr>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'>A</td>";
                            $cadena = $cadena."<td style='border:1px solid #000'>MATERIALES</td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                        $cadena = $cadena."</tr>";
             $A = 0;
             $total = 0;
           
               while($row = mysqli_fetch_array($sql)){
                
                   $PUFM = $row[4];
               
                $total = ($row[2] * $PUFM);
                
                $A = $A+$total;
                $n++;
                        $cadena = $cadena."<tr>";
                            $cadena = $cadena."<td style='border:1px solid #000'>".$n."</td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'>".$row[1]."</td>";
                            $cadena = $cadena."<td style='border:1px solid #000'>".$row[3]."</td>";
                            $cadena = $cadena."<td style='border:1px solid #000; text-align: right;'>".$row[2]."</td>";
                            $cadena = $cadena."<td style='border:1px solid #000; text-align: right;'>".$PUFM."</td>";
                            $cadena = $cadena."<td style='border:1px solid #000; text-align: right;'>".$total."</td>";
                        $cadena = $cadena."</tr>";
                    }
                        $cadena = $cadena."<tr>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'>A</td>";
                            $cadena = $cadena."<td style='border:1px solid #000'>TOTAL MATERIALES</td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000; text-align: right;'>A=</td>";
                            $cadena = $cadena."<td style='border:1px solid #000; text-align: right;'>".$A."</td>";
                        $cadena = $cadena."</tr>";
            }
            else{
                $A = 0;
               $cadena = $cadena."<tr>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'>SIN ITEMS</td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                $cadena = $cadena."</tr>";
            }   
                $cadena = $cadena."<tr>";
                            $cadena = $cadena."<td style='border:1px solid #000'>.</td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                $cadena = $cadena."</tr>";
          
            
            
         $sql2 = mysqli_query($conexionBD,"SELECT actividades.descripcion, mano_obra.descripcion,  rel_actv_mo.cant, mano_obra.unidad, mano_obra.PU, mano_obra.id_mo
                        FROM actividades, mano_obra, rel_actv_mo
                        WHERE rel_actv_mo.id_actividad = '".$id_actividad."'
                        AND rel_actv_mo.id_mo = mano_obra.id_mo
                        AND actividades.id_actividad = rel_actv_mo.id_actividad ");
        
            
            $B =0;
            $E = 0;
            $total = 0;
                        $cadena = $cadena."<tr>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'>B</td>";
                            $cadena = $cadena."<td style='border:1px solid #000'>MANO DE OBRA</td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                        $cadena = $cadena."</tr>";
        if(mysqli_num_rows($sql2) > 0){
           // $cadena = $cadena."<div class='tituloseccion'>Lista: ".$tit."</div>";
         
            $n = 0;
            
               while($row2 = mysqli_fetch_array($sql2)){
                    $PUFMO = $row2[4];
              
   
                $total = ($row2[2] * $PUFMO);
                
                $B = $B +$total;
                $n++;
                        $cadena = $cadena."<tr>";
                            $cadena = $cadena."<td style='border:1px solid #000;'>".$n."</td>";
                            $cadena = $cadena."<td style='border:1px solid #000;'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000;'>".$row2[1]."</td>";
                            $cadena = $cadena."<td style='border:1px solid #000;'>".$row2[3]."</td>";
                            $cadena = $cadena."<td style='border:1px solid #000; text-align: right;'>".$row2[2]."</td>";
                            $cadena = $cadena."<td style='border:1px solid #000; text-align: right;'>".$PUFMO."</td>";
                            $cadena = $cadena."<td style='border:1px solid #000; text-align: right;'>".$total."</td>";
                        $cadena = $cadena."</tr>";
                    }
                    $cadena = $cadena."<tr>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'>B</td>";
                            $cadena = $cadena."<td style='border:1px solid #000'>SUB TOTAL M.O. (B)</td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000; text-align: right;'>B=</td>";
                            $cadena = $cadena."<td style='border:1px solid #000; text-align: right;'>".$B."</td>";
                        $cadena = $cadena."</tr>";
                   $cadena = $cadena."<tr>";   
                            $cadena = $cadena."<td style='border:1px solid #000'>.</td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                    $cadena = $cadena."</tr>";
                          
                        
                
                        $cadena = $cadena."<tr>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'>C</td>";
                            $cadena = $cadena."<td style='border:1px solid #000'>BENEFICIOS SOCIALES (C)</td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000; text-align: right;'>".$Ben_Soc."% de</td>";
                            $cadena = $cadena."<td style='border:1px solid #000; text-align: right;'>B=</td>";
                                 $C = ($B *($Ben_Soc/100));
                            $cadena = $cadena."<td style='border:1px solid #000; text-align: right;'>".$C."</td>";
                        $cadena = $cadena."</tr>";
                        $cadena = $cadena."<tr>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'>D</td>";
                            $cadena = $cadena."<td style='border:1px solid #000'>IMPUESTO AL VALOR AGREGADO IVA (D)</td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000; text-align: right;'>".$iva."% de</td>";
                            $cadena = $cadena."<td style='border:1px solid #000; text-align: right;'>B+C=</td>";
                                 $D = (($B + $C)*($iva/100));
                            $cadena = $cadena."<td style='border:1px solid #000; text-align: right;'>".round($D, 2)."</td>";
                        $cadena = $cadena."</tr>";
                         $cadena = $cadena."<tr>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'>E</td>";
                            $cadena = $cadena."<td style='border:1px solid #000'>TOTAL MANO DE OBRA (E)</td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000; text-align: right;'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000; text-align: right;'>B+C+D=</td>";
                                 $E = $B + $C + $D;
                            $cadena = $cadena."<td style='border:1px solid #000; text-align: right;'>".round($E, 2)."</td>";
                        $cadena = $cadena."</tr>";
              
            //$E = $B + $C + $D;
            }
            
            else{
                $B = 0;
               $cadena = $cadena."<tr>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'>SIN ITEMS</td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000; text-align: right;'>B=</td>";
                            $cadena = $cadena."<td style='border:1px solid #000; text-align: right;'>0</td>";
               $cadena = $cadena."</tr>";
              
                
            }   
             $cadena = $cadena."<tr>";   
                            $cadena = $cadena."<td style='border:1px solid #000'>.</td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
               $cadena = $cadena."</tr>";
                     
            
            
         $sql3 = mysqli_query($conexionBD,"SELECT actividades.descripcion, equipo.descripcion,  rel_actv_equip.cant, equipo.unidad, equipo.PU, equipo.id_equip
                        FROM actividades, equipo, rel_actv_equip
                        WHERE rel_actv_equip.id_actividad = '".$id_actividad."'
                        AND rel_actv_equip.id_equip = equipo.id_equip
                        AND actividades.id_actividad = rel_actv_equip.id_actividad ");
       
                
                        $cadena = $cadena."<tr>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'>F</td>";
                            $cadena = $cadena."<td style='border:1px solid #000'>EQUIPO</td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                        $cadena = $cadena."</tr>";
        if(mysqli_num_rows($sql3) > 0){
           // $cadena = $cadena."<div class='tituloseccion'>Lista: ".$tit."</div>";
         $n=0;
          $F =0;
          $total = 0;  
            
               while($row3 = mysqli_fetch_array($sql3)){
                if($t_precios==0){//precios estandar
                   $PUFEQ = $row3[4];
                }
                
                $total = ($row3[2] * $PUFEQ);
                
              
                $F = $F + $total;
                $n++;
                        $cadena = $cadena."<tr>";
                            $cadena = $cadena."<td style='border:1px solid #000'>".$n."</td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'>".$row3[1]."</td>";
                            $cadena = $cadena."<td style='border:1px solid #000'>".$row3[3]."</td>";
                            $cadena = $cadena."<td style='border:1px solid #000; text-align: right;'>".$row3[2]."</td>";
                            $cadena = $cadena."<td style='border:1px solid #000; text-align: right;'>".$PUFEQ."</td>";
                            $cadena = $cadena."<td style='border:1px solid #000; text-align: right;'>".$total."</td>";
                        $cadena = $cadena."</tr>";
                        
                    }
                       $cadena = $cadena."<tr>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'>F</td>";
                            $cadena = $cadena."<td style='border:1px solid #000'>TOTAL EQUIPO (F)</td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000; text-align: right;'>F=</td>";
                            $cadena = $cadena."<td style='border:1px solid #000; text-align: right;'>".round($F, 2)."</td>";
                        $cadena = $cadena."</tr>";
                     
            }
            else{
                $F = 0;
               $cadena = $cadena."<tr>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'>.</td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'>SIN ITEMS</td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000; text-align: right;'>F=</td>";
                            $cadena = $cadena."<td style='border:1px solid #000; text-align: right;'>0</td>";
               $cadena = $cadena."</tr>";
              
               
            }   
                $cadena = $cadena."<tr>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'>.</td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'>.</td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
               $cadena = $cadena."</tr>";        
                   
                        $cadena = $cadena."<tr>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'>G</td>";
                            $cadena = $cadena."<td style='border:1px solid #000'>HERRAMIENTAS MENORES (G)</td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000; text-align: right;'>".$he_men."% de</td>";
                            $cadena = $cadena."<td style='border:1px solid #000; text-align: right;'>E=</td>";
                            $G = $E * ($he_men/100);
                            $cadena = $cadena."<td style='border:1px solid #000; text-align: right;'>".round($G, 2)."</td>";
                        $cadena = $cadena."</tr>";
                        
                        
                        $cadena = $cadena."<tr>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'>H</td>";
                            $cadena = $cadena."<td style ='border:1px solid #000'>TOTAL HERRAMIENTAS (H)</td>";
                            $cadena = $cadena."<td style ='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $H = $G+$F;
                            $cadena = $cadena."<td style='border:1px solid #000; text-align: right;'>G+F=</td>";
                            $cadena = $cadena."<td style='border:1px solid #000; text-align: right;'>".round($H, 2)."</td>";
                        $cadena = $cadena."</tr>";
                        $cadena = $cadena."<tr>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'>I</td>";
                            $cadena = $cadena."<td style='border:1px solid #000'>GASTOS GENERALES (I)</td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000; text-align: right;'>".$g_grales."% de</td>";
                            $I = ($A+$E+$H)*($g_grales/100);
                            $cadena = $cadena."<td style='border:1px solid #000; text-align: right;'>A+E+H=</td>";
                            $cadena = $cadena."<td style='border:1px solid #000; text-align: right;'>".round($I, 2)."</td>";
                        $cadena = $cadena."</tr>";
                        $cadena = $cadena."<tr>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'>J</td>";
                            $cadena = $cadena."<td style='border:1px solid #000'>UTILIDAD (J)</td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000; text-align: right;'>".$utilidad."% de</td>";
                            $J = ($A+$E+$H+$I)*($utilidad/100);
                            $cadena = $cadena."<td style='border:1px solid #000; text-align: right;'>A+E+H+I+J=</td>";
                            $cadena = $cadena."<td style='border:1px solid #000; text-align: right;'>".round($J, 2)."</td>";
                        $cadena = $cadena."</tr>";
                        $cadena = $cadena."<tr>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'>K</td>";
                            $cadena = $cadena."<td style='border:1px solid #000'>IMPUESTO A LAS TRANSACCIONES I.T.  (K)</td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000; text-align: right;'>".round($IT, 2)."% de</td>";
                            $cadena = $cadena."<td style='border:1px solid #000; text-align: right;'>A+H+I+J=</td>";
                            $K = ($A+$E+$H+$I+$J)*($IT/100);
                            $cadena = $cadena."<td style='border:1px solid #000; text-align: right;'>".round($K, 2)."</td>";
                        $cadena = $cadena."</tr>";
         
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<th style='border:1px solid #000'>Precio Adoptado:</th>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000'></td>";
                            $cadena = $cadena."<td style='border:1px solid #000; text-align: right;'> A+E+H+I+J+K=</td>";
                            $L = ($A+$E+$H+$I+$J+$K);
                            $cadena = $cadena."<th style='border:1px solid #000; text-align: right;'>".round($L, 2)."";
                            $precio_untario = round($L, 2);
                            
                        $cadena = $cadena."</tr>";
                       ;
                $cadena = $cadena."</table>";
           
     echo json_encode(["html"=>$cadena]); 
}
?>