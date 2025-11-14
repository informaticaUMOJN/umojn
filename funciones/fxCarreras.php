<?php
	//*****CARRERAS************************************************************//
	function fxGuardarCarrera($msNombre, $msSiglas, $mbPosgrado)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "Select ifnull(mid(max(CARRERA_REL), 4), 0) as Ultimo from UMO040A";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute();
		$mFila = $mDatos->fetch();
		$mnNumero = intval($mFila["Ultimo"]);
		$mnNumero += 1;
		$mnLongitud = strlen($mnNumero);
		$msCodigo = "CRR" . str_repeat("0", 4 - $mnLongitud) . trim($mnNumero);
		$msConsulta = "insert into UMO040A (CARRERA_REL, NOMBRE_040, SIGLAS_040, POSGRADO_040) values(?, ?, ?, ?)";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo, $msNombre, $msSiglas, $mbPosgrado]);
		return $msCodigo;
	}
	
	function fxModificarCarrera($msCodigo, $msNombre, $msSiglas, $mbPosgrado)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "update UMO040A set NOMBRE_040 = ?, SIGLAS_040 = ?, POSGRADO_040 = ? where CARRERA_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msNombre, $msSiglas, $mbPosgrado, $msCodigo]);
	}
	
	function fxBorrarCarrera($msCodigo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "delete from UMO040A where CARRERA_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo]);
	}
	
	function fxDevuelveCarrera($mbLlenaGrid, $msCodigo = "")
	{
		$m_cnx_MySQL = fxAbrirConexion();

		if ($mbLlenaGrid == 1)
		{
			$msConsulta = "select CARRERA_REL, NOMBRE_040, SIGLAS_040, POSGRADO_040 from UMO040A order by NOMBRE_040";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute();
		}
		else
		{
			$msConsulta = "select CARRERA_REL, NOMBRE_040, SIGLAS_040, POSGRADO_040 from UMO040A where CARRERA_REL = ?";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute([$msCodigo]);
		}
		return $mDatos;
	}
?>