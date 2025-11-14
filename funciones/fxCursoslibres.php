<?php
	//*****CURSOS LIBRES************************************************************//
	function fxGuardarCursosLibres($msNombre )
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "Select ifnull(mid(max(CURSOS_REL), 4), 0) as Ultimo from UMO190A";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute();
		$mFila = $mDatos->fetch();
		$mnNumero = intval($mFila["Ultimo"]);
		$mnNumero += 1;
		$mnLongitud = strlen($mnNumero);
		$msCodigo = "CSL" . str_repeat("0", 4 - $mnLongitud) . trim($mnNumero);
		$msConsulta = "insert into UMO190A (CURSOS_REL, NOMBRE_190) values(?, ?)";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo, $msNombre ]);
		return $msCodigo;
	}
	
	function fxModificarCursosLibres($msCodigo, $msNombre )
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "update UMO190A set NOMBRE_190 = ? where CURSOS_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msNombre , $msCodigo]);
	}
	
	function fxBorrarCursosLibres($msCodigo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "delete from UMO190A where CURSOS_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo]);
	}
	
	function fxDevuelveCursosLibres($mbLlenaGrid, $msCodigo = "")
	{
		$m_cnx_MySQL = fxAbrirConexion();

		if ($mbLlenaGrid == 1)
		{
			$msConsulta = "select CURSOS_REL, NOMBRE_190 FROM UMO190A";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute();
		}
		else
		{
			$msConsulta = "select CURSOS_REL, NOMBRE_190 from UMO190A where CURSOS_REL = ?";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute([$msCodigo]);
		}
		return $mDatos;
	}
?>