<?php
ob_start();
include_once('3lib/firebug/fb.php');
fb($GLOBALS);
fb($_POST,"principal.php");

if(!$_SESSION["proValve"]="Registered"){
	fb("volver a main_login.php");
	header("location:main_login.php");
	exit;
}
else {
	fb($_SESSION["proValve"]);
}
?>

<!DOCTYPE html> 
<html> 
	<head> 
	<title>ProValve</title> 
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1"> 
	<link rel="stylesheet" type="text/css" href="css/jquery.mobile-1.1.1.min.css" />
	<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="js/jquery.mobile-1.1.1.min.js"></script>
	<script type="text/javascript" src="js/ProValve.js"></script>
</head> 
<body> 


<!-- inicio pagina bienvenida -->
<div data-role="page" id="welcome" data-title="Welcome" data-theme="a" data-dom-cache="false" >
	<div data-role="header" data-position="fixed">
		<h1>Welcome</h1>
		<a id="botonAcercade" href="#acercade" data-role="button" data-rel="dialog" data-theme="b" data-icon="grid">Acerca de...</a>
		<a id="botonLogout" href="logout.php" data-role="button" data-theme="e" data-icon="delete">Logout</a>
	</div><!-- /header -->

	<div id="content_welcome" data-role="content" >	
		<p>Gestión 2.0 de las válvulas de agua industrial del Camp de Tarragona</p>	
		<a id="VerButton" data-role="button" href="#arquetas">Ver arquetas</a>
	</div><!-- /content -->
	
	<div data-role="footer" data-position="fixed">
		<h4>@ARM 2012</h4>
	</div><!-- /footer -->
</div><!-- /page -->
<!-- fin pagina bienvenida -->

<!-- inicio pagina lista arquetas -->
<div data-role="page" id="arquetas" data-title="Arquetas" data-dom-cache="false" >

	<div data-role="header" data-position="fixed">
		<h1>Arquetas</h1>
		<a href="principal.php" data-role="button" data-inline="true" data-icon="home" data-theme="b">Inicio</a>
		<a href="#" data-rel="back" data-role="button" data-inline="true" data-icon="back" data-theme="b">Volver...</a>
	</div><!-- /header -->
	<div data-role="content" data-theme="a">
	 	<ul id="lista_arquetas" data-role="listview" data-inset="true" data-theme="a" data-filter="true">
		</ul>
	</div>
	<div data-role="footer" data-position="fixed">
		<h4>@ARM 2012</h4>
	</div><!-- /footer -->

</div>

<!-- fin pagina lista arquetas -->

	<div data-role="footer" data-position="fixed">
		<h4>@ARM 2012</h4>
	</div><!-- /footer -->

<!-- pagina valvulas -->

<!-- pagina Acerca de -->
<div data-role="page" id="acercade" data-title="Atención">
	<div data-role="header" data-theme="b"><h1>Bienvenido a ProValve</h1></div>
	<div data-role="content" data-theme="a">
		Bienvenido a la gestión 2.0 de las válvulas de agua industrial del Camp de Tarragona <br />
	   	Su posición actual es:<br /> 
	   		Latitud : <div id=lat> </div> <br />
	    	Longitud : <div id=lng> </div> <br />
    	<a href="#" data-role="button" data-inline="true" data-rel="back">Aceptar</a>

		<script>

			navigator.geolocation.getCurrentPosition (function (pos)
			{
			  var lat = pos.coords.latitude;
			  var lng = pos.coords.longitude;
			  $("#lat").text (lat);
			  $("#lng").text (lng);
			});

		</script>

	</div>
	<div data-role="footer">
		<h4>@ARM 2012</h4>
	</div><!-- /footer -->
</div>
<!-- pagina Acerca de -->


	<!-- AJAX para leer lista arquetas -->
<script type="text/javascript">

// jQuery(document).live('pageinit',function(event){
		$.ajax({
			type: "POST",
			url: "QueryTotal.php", 
		}).done(function(resp) {
			var items=[];
			var listitems=[];
			var linea1;
			items=jQuery.parseJSON(resp);
			// para cada arqueta ...
			$.each(items,function(index,value){
				// ... se compone un item de la lista principal
				linea1='<li data-icon="arrow-r" ><a data-ajax="false" href="valvulas.html?AR=' + value.arqueta + '">Arqueta ' + value.arqueta + '</a></li>';  
				listitems.push(linea1);
			});
			$('#lista_arquetas').html(listitems.join(''));
		    $('#lista_arquetas').listview( "refresh" );    
		});

		//Call listview jQuery UI Widget after adding 
	    //items to the list for correct rendering

// });

</script>


</body>
</html>