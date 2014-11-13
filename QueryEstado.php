<?php
ob_start();
include_once('3lib/firebug/fb.php');

fb($GLOBALS,"QueryEstado.php");

require_once('db.php');

//Conexion a la base de datos
$db = new mysqli('localhost', $mysql_user, $mysql_pass, $mysql_db);

//Consulta para estado valvula

$where = ' WHERE `ID` = ' . $_POST['ID'] . '';
$select='SELECT `Nombre`, `Arqueta` ,`ID` , `Estado` FROM `valvulas`';
$rs_query = $db->query( $select . $where );

fb($select . $where);

$rs = $rs_query->fetch_assoc();

$datos_valvula = array('Arqueta' => $rs['Arqueta'],'Nombre' => $rs['Nombre'],'ID' => $rs['ID'], 'Estado' => $rs['Estado']);

fb($datos_valvula,"datos_valvula");
fb(json_encode($datos_valvula),"datos_valvula");

//Imprimo el resultado en formato de cadena JSON
print json_encode($datos_valvula);

?>
