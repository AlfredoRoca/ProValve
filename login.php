<?php
ob_start();
include_once('3lib/firebug/fb.php');
fb($GLOBALS);
fb($_POST,"login.php");
	
//header ("content-type:text/xml");

	$clave = $_REQUEST["clave"];
	if(isset($clave) && $clave)
	{
		
        require_once('db.php');

        //Conexion a la base de datos
        $db = new mysqli('localhost', $mysql_user, $mysql_pass, $mysql_db);

        $where = " WHERE Clave='$clave'";
		fb($where,"where");
		$select='SELECT * FROM `miembros`';
		fb($select,"select");
		$rs_query = $db->query( $select . $where );
		fb($select . $where,"select + where");
		fb($rs_query->num_rows,"num rows");
		if ($rs_query->num_rows>0) {
			$rs_row = $rs_query->fetch_assoc();

			// fuente: http://www.phpeasystep.com/phptu/6.html
			if (session_start()){
				fb("Sesión iniciada");
			}
			// devuelve XML
//			echo "<id>" . $rs_row['ID'] . "</id>" ; //<text>Granted</text>";	
			fb("<id>" . $rs_row['ID'] . "</id>"); //<text>Granted</text>");	
			$_SESSION["proValve"]="Registered";
			ob_end_flush();
			header("Location: principal.php");
			exit;
		}
		else {
//			echo "Wrong Username or Password";
			echo "<id>0</id>"; //<text>Access denied</text>";	
			fb("<id>0</id>"); // <text>Access denied</text>");	
			ob_end_flush();
			header("Location: index.php");
			exit;
		}
	}
	else { 
		fb("Sin clave");
			ob_end_flush();
		header("Location: index.php");
		exit;
	}
?>


