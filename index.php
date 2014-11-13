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
<div data-role="page" id="login" data-title="Welcome" data-theme="a">
	<div data-role="header" data-position="fixed">
		<h1>ProValve Login</h1>
	</div><!-- /header -->

	<div id="content_login" data-role="content" >	
		<form action="login.php" method="post" data-ajax="false">	
			<div data-role="fieldcontain">
<!--				<label for="clave">Clave:</label>
-->				<input type="password" name="clave" id="clave" value="" />
				<button id="submit" data-icon="check" data-inline="false" >Entrar</button>
			</div>
		</form>
	</div><!-- /content -->
	
	<div data-role="footer" data-position="fixed">
		<h4>@ARM 2012</h4>
	</div><!-- /footer -->
</div><!-- /page -->
<!-- fin pagina bienvenida -->

</body>