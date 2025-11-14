<?php
	//*****CURSOS DE POSGRADO************************************************************//
	function fxGuardarCursoPosgrado($msCarrera, $msNombre, $msCodCurso, $mbActivo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "Select ifnull(mid(max(CURSOPOSGRADO_REL), 3), 0) as Ultimo from UMO240A";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute();
		$mFila = $mDatos->fetch();
		$mnNumero = intval($mFila["Ultimo"]);
		$mnNumero += 1;
		$mnLongitud = strlen($mnNumero);
		$msCodigo = "CP" . str_repeat("0", 4 - $mnLongitud) . trim($mnNumero);
		$msConsulta = "insert into UMO240A (CURSOPOSGRADO_REL, CARRERA_REL, NOMBRE_240, CODIGO_240, ACTIVO_240) values(?, ?, ?, ?, ?)";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo, $msCarrera, $msNombre, $msCodCurso, $mbActivo]);
		return $msCodigo;
	}
	
	function fxModificarCursoPosgrado($msCodigo, $msCarrera, $msNombre, $msCodCurso, $mbActivo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "update UMO240A set CARRERA_REL = ?, NOMBRE_240 = ?, CODIGO_240 = ?, ACTIVO_240 = ? where CURSOPOSGRADO_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCarrera, $msNombre, $msCodCurso, $mbActivo, $msCodigo]);
	}
	
	function fxBorrarCursoPosgrado($msCodigo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "delete from UMO240A where CURSOPOSGRADO_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo]);
	}
	
	function fxDevuelveCursoPosgrado($mbLlenaGrid, $msCodigo = "")
	{
		$m_cnx_MySQL = fxAbrirConexion();

		if ($mbLlenaGrid == 1)
		{
			$msConsulta = "select CURSOPOSGRADO_REL, NOMBRE_040, NOMBRE_240, ACTIVO_240 from UMO240A, UMO040A where UMO240A.CARRERA_REL = UMO040A.CARRERA_REL order by CURSOPOSGRADO_REL desc";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute();
		}
		else
		{
			$msConsulta = "select CURSOPOSGRADO_REL, CARRERA_REL, NOMBRE_240, CODIGO_240, ACTIVO_240 from UMO240A where CURSOPOSGRADO_REL = ?";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute([$msCodigo]);
		}
		return $mDatos;
	}
?>