<?php
ob_start();
include_once('3lib/firebug/fb.php');
fb("QueryValvulas.php");

//Versiones anteriores de php 5.2, o instalaciones sin la funcion json_encode()
//require_once('json_encode.php');


require_once('db.php');
//La variable arqueta que enviamos con el parámetro data de $.ajax()
$arqueta = $_POST['arqueta'];

fb($arqueta,"arqueta");


//Conexion a la base de datos
$db = new mysqli('localhost', $mysql_user, $mysql_pass, $mysql_db);

//Consulta para obtener lista válvulas de la arqueta seleccionada
$where = ' WHERE `Arqueta` = "' . $arqueta . '"';
fb($where,"where");
$select='SELECT `Nombre`, `Arqueta` ,`ID`, `Estado` FROM `valvulas`';
fb($select,"select");
$rs_query = $db->query( $select . $where );
fb($select . $where,"select + where");

/*
* Creo el array que se transformara en formato JSON
*/

$data_total=array();

// array con nombres valvulas de una arqueta
$data_valvs=array();
while ($rs_row = $rs_query->fetch_assoc()){
	$reg_valvs  = array('nombre' => $rs_row['Nombre'],'ID' => $rs_row['ID'], 'estado' => $rs_row['Estado']);
	array_push($data_valvs,$reg_valvs);
}

$reg_arqueta  = array('arqueta' => $arqueta,'valvulas' => $data_valvs);
array_push($data_total,$reg_arqueta); 

//Imprimo el resultado en formato de cadena JSON
print json_encode($data_total);
fb(json_encode($data_total),"JSON encode");
?>
