<?php
ob_start();
include_once('3lib/firebug/fb.php');

/*
GRAFO: vertices = tuberías, Aristas = válvulas 

Preparar Excel
1.-Crear hoja "vertices" a partir del P&ID de la red general 
2.-Generar hoja "matriz incidencia" con macro correspondiente
3.-Generar hoja "matriz adyacencia" con macro correspondiente

crea lista adyacencia a partir de la tabla de adyacencias generada en excel

instrucciones:
1.-excel: copiar la hoja matrizadyacencia en libro aparte y guardar como csv (delimitado por comas)
2.-mysql: eliminar la tabla anterior
3.-mysql: importar el archivo creado anteriormente con ; como separador de columnas
		sin delimitación de columnas y sin marcar la opcion de "la primera fila contiene nombres..."
4.-mysql: renombrar la tabla recien creada a "matrizadyacencia"
5.-este fichero: actualizar la constante CantVertices

Notas:
- ojo! la importación en mysql de la hoja guardada en formato ODS no funciona bien; hay que usar el csv
- la matriz $destinos contiene los numeros de nodo asignados a los clientes y los nombres utilizados. Ver pestaña "aristas" del xls
- la constante $origen contiene el valor del nodo asignado a los depositos
- las rutas generadas "con paths_to" con 0 saltos indican que no alcanza el destino

*/


require("3lib/PHP-Dijkstra-master/Dijkstra.php");

$imprimir_si=0; // poner a 1 si se quiere imprimir resultados en el html

function destinos() {	// lista destinos
	$destinos=array(
		13=>'BIC LAFOREST',
		15=>'NAVINDU',
		19=>'REYCON_DAWS',
		23=>'ASESA',
		26=>'BASF PTP',
		27=>'DOW TERMINAL',
		29=>'S.C.I. RACK DIXQUIMICS',
		30=>'BASF TE',
		34=>'REPSOL BUTANO',
		43=>'DOW LABORATORIO',
		47=>'POZO',
		49=>'ARAGONESAS',
		54=>'MESSER CARBUROS',
		55=>'CLARIANT',
		67=>'REPSOL TANQUES PLAYA',
		70=>'IQA OESTE',
		73=>'BASF_FABRICA',
		75=>'IQA SCI',
		77=>'ENDESA COGENERACION',
		78=>'IQA SUR',
		79=>'ERKIMIA',
		81=>'REPSOL TANQUES PLAYA POR RACK REPSOL',
		83=>'DOW SCI',
		87=>'DOW FABRICA',
		89=>'PROAS',
		90=>'EMATSA Y PUERTO',
		92=>'CELANESE Y TAQSA',
		95=>'BAYER',
		98=>'AISCONDEL');
	return($destinos);
}
// ------------------- destinos ----------------------------------------------------------

// para usar en depuración
function imprimir($cadena){
	global $imprimir_si;
	if ($imprimir_si==1) print($cadena);
}
// ------------------- imprimir ----------------------------------------------------------

function imprimir_r($cadena){
	global $imprimir_si;
	if ($imprimir_si==1) print_r($cadena);
}
// ------------------- imprimir_r ----------------------------------------------------------

function CalculaCaminos($origen,$IDValvula=0) {
	// $origen="8"; // $ORIGEN1=8;
	// $origen="9"; // $ORIGEN2=9;
	// hay que analizar de que deposito es según las valvulas en Bonavista
	// se considera origen1 la línea que lleva a la valvula 006.8 y origen 2 a la valvula 006.9
	// si se especifica IDValvula se considerará como cerrada para evaluar su repecrcusión
	require('db.php');
	//Conexion a la base de datos
	$db = new mysqli('localhost', $mysql_user, $mysql_pass, $mysql_db);
	$db1 = new mysqli('localhost', $mysql_user, $mysql_pass, $mysql_db);

	//Consulta para obtener toda la matriz
	$select='SELECT * FROM `matrizadyacencia` WHERE `matrizadyacencia`.`col 1` > 0 '; //LIMIT 10';
	imprimir("---------------------------------------------------------<br/>");
	imprimir("SELECT :: $select<br />");
	imprimir("---------------------------------------------------------<br/>");
	$rs_query = $db->query( $select );

	// el grafo completo 
	$G = new Graph();
	$CantVertices=100;
	imprimir("Cantidad vertices :: $CantVertices<br/>");
	imprimir("---------------------------------------------------------<br/>");

	// sacar todas las filas de la tabla, es decir, todos los vertices
	while ($fila = $rs_query->fetch_row()){
		imprimir("Procesando vertice $fila[0]...<br/>");

		// lista de los nodos vecinos que contiene el id y la válvula que los une
	//	$vecinos=array();
		// recorrer todo el registro para sacar los campos no nulos, es decir, con válvula
		for ($i=1;$i<=$CantVertices;$i++) {
			if (($fila[$i])!="") {
				// ejecutar si la válvula en $fila[$i] está abierta 
				//Consulta para obtener el estado de la válvula
				$select_estado='SELECT `Estado`,`ID` FROM `valvulas` WHERE `valvulas`.`Nombre` = "'. $fila[$i].'"';
//				imprimir("---------------------------------------------------------<br/>");
//				imprimir("SELECT :: $select_estado<br />");
//				imprimir("---------------------------------------------------------<br/>");
				$rs_estado = $db1->query( $select_estado );
//				imprimir("Cantidad registros lectura estado:");
//				imprimir($db1->affected_rows);
//				imprimir("<br/>");
				$estado=($rs_estado->fetch_assoc());
//				imprimir("Estado ");
//				imprimir_r($estado['Estado']);
				$abierta=($estado['Estado']==2); // estado valvula 2 = abierta
				// si se ha especificado IDValvula se considerra cerrada para evaluar su repercusión
				if (($IDValvula!=0) and ($estado['ID']==$IDValvula)) {$abierta=false;}
				if ($abierta) {
//					imprimir(".... Vertice adyacente $i --> valvula $fila[$i]<br />");
//					imprimir("Ejecutando addedge("."$fila[0]".",$i)<br/>");
					$G->addedge("$fila[0]", $i, 1); 
				}
			}
		}
	}
	imprimir ("Fin del procesamiento.<br/>");

	// analisis de suministro desde origen
	list($distances, $prev, $warnings) = $G->paths_from($origen);
	imprimir ("Calculados caminos desde el origen.<br/>");
	imprimir_r($warnings);

/*	if (in_array($origen,$warnings)) {
		// el nodo (origen) se ha quedado aislado		
		print("Atención: una fuente de agua quedará cortada.<br/>");
	} 
*/			// lista destinos
			$destinos=destinos(); // no funcionó declarar el array $destinos como global

			$pathdestinos=array();
			// array saltos, para averiguar si el cierre de una válvula afectará a algún cliente
			$saltos=array(); // contendrá la cantidad de saltos para llegar desde origen a destino
			$alertas=array(); // contendra 1 si no hay camino hasta destino
			foreach ($destinos as $key => $value) {
				if (in_array($origen,$warnings)) { // el nodo (origen) se ha quedado aislado
					$pathdestinos[$key]=array();
					$saltos[$key]=9999; // infinito
					$alertas[$key]=true;
				} else {
					$path= $G->paths_to($prev, $key);
					$pathdestinos[$key]=$path;
					$saltos[$key]=count($path);
					$alertas[$key]=(count($path)==0);
				}
			}
			// las rutas con 0 saltos indican que no alcanza el destino

			imprimir("--------- Representación del grafo indicando cantidad saltos ---<br/>");
			imprimir_r("Exploradas ".count($pathdestinos)." rutas.<br/>");
			foreach($pathdestinos as $path) {
				imprimir_r($path);
				imprimir("-- ".current($saltos)." saltos<br/>");
				next($saltos);
			}
			reset($saltos);
			imprimir("<br/>---------------------------------------------------------<br/>");

			// 
			imprimir("--------- Cantidad de saltos para cada destino  ----------------<br/>");
			$respuesta="";
			foreach($pathdestinos as $path) {
				$respuesta .= (current($path)." -- ".current($saltos)." saltos<br/>");
				next($saltos);
			}
			imprimir($respuesta);
			imprimir("<br/>---------------------------------------------------------<br/>");

			// 
			imprimir("--------- lista destinos en texto html con ok o alerta ----------------<br/>");
			$respuesta="";
			foreach($alertas as $key => $alerta) {
				$respuesta .= $destinos[$key]." -- ".($alerta==1 ? "no llega el agua" : "ok")."<br/>";
			}
			imprimir($respuesta);
			imprimir("<br/>---------------------------------------------------------<br/>");

			// 
			imprimir("--------- array con destinos en texto y alerta ----------------<br/>");
			$respuesta=array();
			foreach($alertas as $key => $alerta) {
				// $respuesta[] = array($destinos[$key],($alerta==1 ? "no llega el agua" : "ok"));
				// $key es el num de nodo
				// $destinos[$key] devuelve el nombre del destino
				// alerta==1 significa que no llega el agua a destino
				$respuesta[$key] = array($destinos[$key],($alerta==1 ? "1" : "0"));
			}
			imprimir_r($respuesta);
			imprimir("<br/>---------------------------------------------------------<br/>");
		 


	// cerrar conexiones
	$rs_estado->close();
	$rs_query->close();
	$db->close();
	$db1->close();

	// valor de retorno
	return ($respuesta);
}
// ------------------- fin CalculaCaminos ----------------------------------------------------------


function CalculaAislamientos($ab,$cerr) 
/* devuelve matriz con destinos que no recibirán agua, es decir, 
	que tienen alerta=1 en cerrada y alerta=0 en abierta
*/

{ 
	$r = array(); 
 	foreach ($ab as $key => $first) { 
 		if ($first[1]==0) { // si en abierta recibe agua sigo el analisis
		 	if (isset($cerr[$key])) {
		 		if ($cerr[$key][1]==1) {
		 			/* devuelve las key que tienen alerta=1 en cerrada y =0 en abierta, 
		 			es decir, los destinos que dejan de recibir agua */
		 			$r[]=$key; 
						$tezto=$ab[$key][0];
		 		}
		 	} else {
		 			$r[]=$key; 
		 	}
	 	}
 	}
	return $r; 
} // ------------------- fin level2_array_diff ----------------------------------------------------------


// ------------------- función principal ----------------------------------------------------------

function AnalizaGrafo($IDValvula) {
	// lista destinos
	$destinos=destinos(); // no funcionó declarar el array $destinos como global
	// $origen="8"; // $ORIGEN1=8;
	// $origen="9"; // $ORIGEN2=9;
	// hay que analizar de que deposito es según las valvulas en Bonavista
	// se considera origen1 la línea que lleva a la valvula 006.8 y origen 2 a la valvula 006.9
	$origen="8";
	$CasoAbierta1=(CalculaCaminos($origen)); // si NO se especifica valvula se usará su estado
//				print("<br/>--------- CasoAbierta1 --------------<br/>");
//				print_r($CasoAbierta1);
//				print("<br/>---------------------------------------------<br/>");
	$CasoCerrada1=(CalculaCaminos($origen,$IDValvula)); // si se especifica valvula se forzará cerrada
//				print("<br/>--------- CasoCerrada1 --------------<br/>");
//				print_r($CasoCerrada1);
//				print("<br/>---------------------------------------------<br/>");
	$diferencia1= (CalculaAislamientos($CasoAbierta1,$CasoCerrada1)); // la diferencia son aquellos destinos que dejarán de recibir agua
//				print("<br/>--------- diferencia1 --------------<br/>");
//				print_r($diferencia1);
//				print("<br/>---------------------------------------------<br/>");

	$origen="9";
	$CasoAbierta2=(CalculaCaminos($origen)); // si NO se especifica valvula se usará su estado
//				print("<br/>--------- CasoAbierta2 --------------<br/>");
//				print_r($CasoAbierta2);
//				print("<br/>---------------------------------------------<br/>");
	$CasoCerrada2=(CalculaCaminos($origen,$IDValvula)); // si se especifica valvula se forzará cerrada
//				print("<br/>--------- CasoCerrada2 --------------<br/>");
//				print_r($CasoCerrada2);
//				print("<br/>---------------------------------------------<br/>");
	$diferencia2= (CalculaAislamientos($CasoAbierta2,$CasoCerrada2)); // la diferencia son aquellos destinos que dejarán de recibir agua
//				print("<br/>--------- diferencia2 --------------<br/>");
//				print_r($diferencia2);
//				print("<br/>---------------------------------------------<br/>");

/*

se quedan sin agua los que esten en la diferencia de un origen y 
		(esten en la diferencia del otro origen OR no esten en el caso cerrada del otro origen)

*/

	// respuesta es true si nadie se queda sin agua
	$respuesta=true;
	$respuestacadena="";
	if (count($diferencia1)>0) { // alguien se queda sin agua del origen1
//		print("<br/>--------- respuesta por diferencia1 --------------<br/>");
		foreach ($diferencia1 as $value) {	
//			print("value :: $value<br/>");
			if (in_array($value, $diferencia2) or ($CasoCerrada2[$value][1]==1)) 
			// un destino esta en ambas diferencias o esta en el caso de cerrada del otro origen
			{
				$respuesta=false;
				$respuestacadena.=$destinos[$value]."<br/>";
//				print("<br/>-$destinos[$value]<br/>");
//				print("--$respuestacadena<br/>");
			}
		}
	}
	if (count($diferencia2)>0) { // alguien se queda sin agua del origen2
//		print("<br/>--------- respuesta por diferencia2 --------------<br/>");
		foreach ($diferencia2 as $value) {	
//			print("value :: $value<br/>");
			if (in_array($value, $diferencia1) or ($CasoCerrada1[$value][1]==1)) 
			// un destino esta en ambas diferencias o esta en el caso de cerrada del otro origen
			{
				$respuesta=false;
				if (strpos($respuestacadena, $destinos[$value])!== true) {
					$respuestacadena.=$destinos[$value]."<br/>";
//					print("<br/>-$destinos[$value]<br/>");
//					print("--$respuestacadena<br/>");
				}
			}
		}
	}

	if (!$respuesta){
		print("Atención! Se quedarán sin agua:<br/>");
		print($respuestacadena);
		}

	return $respuesta; 
}

?>
