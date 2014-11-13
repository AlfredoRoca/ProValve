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
$rs_query_arq = $db->query( $select );

$data_total=array();
// PARA CADA UNA DE LAS ARQUETAS ENCONTRADAS ...
while ($rs_arq = $rs_query_arq->fetch_assoc()){

	// ... Consulta para obtener SU lista válvulas 
	$where = ' WHERE `Arqueta` = "' . $rs_arq['Arqueta'] . '"';
	$select='SELECT `Nombre`, `Arqueta` ,`ID` , `Estado` FROM `valvulas`';
	$rs_query_valv = $db->query( $select . $where );
//	fb($select . $where,"select + where");

	// array con nombres valvulas de una arqueta (para todas las arquetas)
	$data_valvs=array();
	while ($rs_row = $rs_query_valv->fetch_assoc()){
		$reg_valvs  = array('nombre' => $rs_row['Nombre'],'ID' => $rs_row['ID'], 'estado' => $rs_row['Estado']);
		array_push($data_valvs,$reg_valvs);
	}

	$reg_arqueta  = array('arqueta' => $rs_arq['Arqueta'],'valvulas' => $data_valvs);
	array_push($data_total,$reg_arqueta); 

}


//Imprimo el resultado en formato de cadena JSON
print json_encode($data_total);
fb(json_encode($data_total),"JSON encode");

?>
