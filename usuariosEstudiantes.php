<?php
	require_once ("funciones/fxGeneral.php");
	require_once ("funciones/fxUsuarios.php");
	set_time_limit(600);
	$m_cnx_MySQL = fxAbrirConexion();
	
	//Obtiene los estudiantes del catálogo
	$msConsulta = "select UMO010A.ESTUDIANTE_REL, NOMBRE1_010, NOMBRE2_010, APELLIDO1_010, APELLIDO2_010, CARNET_010, CORREOI_010, ESTADO_030 from UMO010A join UMO030A on UMO010A.ESTUDIANTE_REL = UMO030A.ESTUDIANTE_REL";
	$mDatos = $m_cnx_MySQL->prepare($msConsulta);
	$mDatos->execute();
		
	while ($mFila = $mDatos->fetch())
	{
		$msEstudiante = $mFila["ESTUDIANTE_REL"];
		$msNombre = $mFila["NOMBRE1_010"];
		$msCorreo = $mFila["CORREOI_010"];
		$msApellido = $mFila["APELLIDO1_010"];
		$msCodUsuario = trim(strtolower($msNombre)) . '.' . trim(strtolower($msApellido));
		$mbEstado = $mFila["ESTADO_030"];

		$msNombreCompleto = $mFila["NOMBRE1_010"];
		if (trim($mFila["NOMBRE2_010"]) != "")
			$msNombreCompleto .= ' ' . trim($mFila["NOMBRE2_010"]);
		$msNombreCompleto .= ' ' . $mFila["APELLIDO1_010"];
		if (trim($mFila["APELLIDO2_010"]) != "")
			$msNombreCompleto .= ' ' . trim($mFila["APELLIDO2_010"]);

		$msCarnet = trim($mFila["CARNET_010"]);
		$msClave = "";
		for ($i=0; $i<strlen($msCarnet); $i++)
		{
			$mChar = substr($msCarnet, $i, 1);
			if ($mChar != "-")
				$msClave .= $mChar;
		}
		
		if (!fxExisteUsuario($msCodUsuario))
			fxGuardarUsuario($msCodUsuario, $msNombreCompleto, $msCorreo, $msClave, 0, 0, 0, 1);

		$msConsulta = "update UMO010A set USUARIO_REL = ? where ESTUDIANTE_REL = ?";
		$mAux = $m_cnx_MySQL->prepare($msConsulta);
		$mAux->execute([$msCodUsuario, $msEstudiante]);

		if ($mbEstado != 0)
		{
			$msConsulta = "update UMO002A set ACTIVO_002 = 0 where USUARIO_REL = ?";
			$mAux = $m_cnx_MySQL->prepare($msConsulta);
			$mAux->execute([$msCodUsuario]);
		}
	}
	echo('Completado');
?>