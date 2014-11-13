<?php
ob_start();
include_once('3lib/firebug/fb.php');

//Versiones anteriores de php 5.2, o instalaciones sin la funcion json_encode()
//require_once('json_encode.php');


require_once('db.php');

//Conexion a la base de datos
$db = new mysqli('localhost', $mysql_user, $mysql_pass, $mysql_db);

//Consulta para obtener lista de arquetas

$select='SELECT DISTINCT `Arqueta` FROM `valvulas`';
fb($select,"select");
$rs_query = $db->query( $select );

/*
* Creo el array que se transformara en formato JSON
*/
$data=array();
while ($rs_row = $rs_query->fetch_assoc()){
	$registro  = array('arqueta' => $rs_row['Arqueta']);
	array_push($data,$registro); 
}

//Imprimo el resultado en formato de cadena JSON
print json_encode($data);

?>