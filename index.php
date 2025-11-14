 <?php
	#Inicia la sesión antes de la respuesta HTML
	session_start();
	$_SESSION["gnVerifica"] = 0;
	require_once ("funciones/fxGeneral.php");
	require_once ("funciones/fxUsuarios.php");
?>
<!DOCTYPE html>
<html lang="es-NI" class="no-js">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="description" content="Control Administrativo y Académico de UMOJN."/>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<link rel="icon" href="imagenes/favicon.png" />
<link rel="stylesheet" type="text/css" href="css/style.css" />
<link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.css" />
<link rel="stylesheet" type="text/css" href="css/easyui.css" />
<link rel="stylesheet" type="text/css" href="css/icon.css" />
<link rel="stylesheet" type="text/css" href="css/estilos.css"/>

<script src="js/jquery.min.js"></script>
<script src="js/jquery-3.4.1.js"></script>
<script src="js/jquery.easyui.min.js"></script>
<script src="bootstrap/js/bootstrap.js"></script>
<title>Aplicación web UMOJN</title>
<style>
	.divFondo{
		height: 100vh;
		background-image: url(imagenes/splash.png);
		background-repeat: no-repeat;
		background-size: contain;
		background-position-x: right;
	}

	label{
		font-size: xx-large;
		font-weight: bolder;
	}
	@media screen and (max-width: 1000px) {
		label{
		font-size: large;
		}
	}
</style>
</head>

<body>
<?php
	if (isset($_POST['txtUsuario']))
	{
		$msUsuario = htmlentities($_POST['txtUsuario']);
		$msClave = $_POST['txtClave'];
        $msEncriptado = crypt($msClave, '_appUMOJN');
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "Select CLAVE_002 from UMO002A where USUARIO_REL =? and ACTIVO_002 = 1";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msUsuario]);
		$mFila = $mDatos->fetch();
		$msClaveBD = $mFila["CLAVE_002"];
		$mbResultado = hash_equals($msClaveBD, $msEncriptado);

		if ($mbResultado == true) //El usuario pasó la validación
		{
			$msConsulta = "Select NOMBRE_002 from UMO002A where USUARIO_REL =?";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute([$msUsuario]);
			$mFila = $mDatos->fetch();
			$_SESSION["gsNombre"] = $mFila["NOMBRE_002"];
			$_SESSION["gsUsuario"] = $msUsuario;
			$_SESSION["gsClave"] = $msEncriptado;
			$_SESSION["gnVerifica"] = 1;
			$_SESSION["gsDocente"] = "";

			$msConsulta = "Select DOCENTE_REL from UMO100A where USUARIO_REL =?";
			$mAuxiliar = $m_cnx_MySQL->prepare($msConsulta);
			$mAuxiliar->execute([$msUsuario]);
			$mnConteo = $mAuxiliar->rowCount();
			$mbAdministrador = fxVerificaAdministrador();

			if ($mnConteo != 0 and $mbAdministrador == 0)
			{
?>				
				<script>
					$.messager.alert('UMOJN','Su Usuario no está autorizado.','warning');
					$("a").click(function(){window.location="index.php"});
				</script>
<?php
			}
			else
			{
				fxAgregarBitacora($_SESSION["gsUsuario"], "UMO000A", $_SESSION["gsUsuario"], "", "Sesión inicio", "");
				echo('<meta http-equiv="Refresh" content="0;url=frmInicio.php">');
			}
		}
		else //El usuario no pasó la validación
		{
			$msConsulta = "Select * from UMO002A where USUARIO_REL =? and ACTIVO_002 = 1";
			$mAuxiliar = $m_cnx_MySQL->prepare($msConsulta);
			$mAuxiliar->execute([$msUsuario]);
			$mnRegistros = $mAuxiliar->rowCount();

			if ($mnRegistros != 0) //El usuario está Activo
			{
?>
				<script>
					$.messager.alert('UMOJN','Usuario no autorizado o Contraseña errónea.','warning');
					$("a").click(function(){window.location="index.php"});
				</script>
<?php
			}
			else
			{
?>
				<script>
					$.messager.alert('UMOJN','El Usuario está inactivo.','warning');
					$("a").click(function(){window.location="index.php"});
				</script>
<?php
			}
		}
	}
	else
	{
?>
<form action="index.php" method="post" onsubmit="return verificarFormulario()">
	<div class="divFondo">
		<div class="divLogin">
			<input class="form-control" style="width:100%;" type="text" placeholder="Usuario" name="txtUsuario" id="txtUsuario">
			<input class="form-control" style="margin-top:8%; width:100%;" type="password" placeholder="Contraseña" name="txtClave" id="txtClave">

			<input class="btn btn-primary" style="margin-top:8%; width:100%;" type="submit" value="Entrar">
		</div>
	</div>
</form>
</body>
</html>
<?php } ?>

<script>
	function verificarFormulario()
	{
		if (document.getElementById("txtUsuario").value == ''){
			$.messager.alert('Docentes UMOJN','Falta el Usuario.','warning');
			return false;
		}

		if (document.getElementById("txtClave").value == ''){
			$.messager.alert('Docentes UMOJN','Falta la Contraseña.','warning');
			return false;
		}

		return true;
	}
</script>