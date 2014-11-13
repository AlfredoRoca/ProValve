<?php
ob_start();
include_once('3lib/firebug/fb.php');
fb($GLOBALS);
fb($_POST,"peticion.php");

require('graph.php');

        function DecidePermiso($IDValvula) {
            return AnalizaGrafo($IDValvula); // esta en grapf.php
        }


        // escribe estado nuevo en base de datos
        function CambiaEstado($ID, $NuevoEstado) {
            require_once('db.php');

            //Conexion a la base de datos
            $db = new mysqli('localhost', $mysql_user, $mysql_pass, $mysql_db);

            //Consulta para estado valvula

            $where = ' WHERE `ID` = ' . $ID;
            $update='UPDATE `valvulas` SET `Estado`=' . $NuevoEstado ;
            $rs_query = $db->query( $update . $where );

            fb($update . $where, "SQL");

        }

if ($_POST["accion"] == 'permiso') {
    if (DecidePermiso($_POST["ID"])) {
        echo("Permiso concedido. Puedes cerrar. Envía confirmación.");
    }
    else {
        echo("Permiso denegado.");
    }

}
else {
    // accion == "forzar estado real"
    CambiaEstado($_POST["ID"],$_POST["estado"]);
// informe con mas detalle
//    echo("ID: " . $_POST["ID"] . " -> Estado actualizado a " . $_POST["estado"]);
// informe con poco detalle
    echo("Estado actualizado. Comprueba.");
}

?>
