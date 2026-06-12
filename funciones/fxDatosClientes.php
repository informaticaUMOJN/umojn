<?php
require_once ("fxGeneral.php");

/**********Verificar la cédula del Cliente**********/
if (isset($_POST["cliente"]) and isset($_POST["cedula"]))
{
	$m_cnx_MySQL = fxAbrirConexion();
	$msCliente = $_POST["cliente"];
	$msCedula = $_POST["cedula"];
	$msConsulta = "Select CLIENTE_REL, concat_ws(' ', NOMBRES_220, APELLIDOS_220) as CLIENTE from UMO220A where CEDULA_220 = ? and CLIENTE_REL <> ?";
	$mDatos = $m_cnx_MySQL->prepare($msConsulta);
	$mDatos->execute([$msCedula, $msCliente]);
	$mnRegistros = $mDatos->rowCount();
	$msResultado = "";

	if ($mnRegistros > 0)
	{
		$mFila = $mDatos->fetch();
		$msResultado .= $mFila["CLIENTE_REL"] . " - " . $mFila["CLIENTE"];
	}
	
	echo $msResultado;
}
?>