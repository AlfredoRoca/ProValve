<?php

require('graph.php');

	// parametro: ID de la válvula para comprobar que pasa si se cierra esa valvula
    if (AnalizaGrafo("99")) {
        echo("Permiso concedido. Puedes cerrar. Envía confirmación");
    }
    else {
        echo("Permiso denegado.");
    }


