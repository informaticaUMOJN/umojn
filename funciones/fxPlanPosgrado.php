<?php
    function fxGuardarPlanPosgrado($msCarrera, $msPeriodo, $msGrado, $mnHoras, $mnCreditos, $mnTurno, $mnRegimen, $mnModalidad, $mbActivo)
    {
        $m_cnx_MySQL = fxAbrirConexion();
        $msConsulta = "Select ifnull(mid(max(PLANPOSGRADO_REL), 3), 0) as Ultimo from UMO230A";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute();
        $mFila = $mDatos->fetch();
        $mnNumero = intval($mFila["Ultimo"]);
        $mnNumero += 1;
        $mnLongitud = strlen($mnNumero);
        $msCodigo = "PP" . str_repeat("0", 8 - $mnLongitud) . trim($mnNumero);
        $msConsulta = "insert into UMO230A (PLANPOSGRADO_REL, CARRERA_REL, PERIODO_230, GRADO_230, HORAS_230, CREDITOS_230, TURNO_230, REGIMEN_230, MODALIDAD_230, ACTIVO_230) ";
        $msConsulta .= "values(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$msCodigo, $msCarrera, $msPeriodo, $msGrado, $mnHoras, $mnCreditos, $mnTurno, $mnRegimen, $mnModalidad, $mbActivo]);
        return ($msCodigo);
    }

    function fxModificarPlanPosgrado($msCodigo, $msCarrera, $msPeriodo, $msGrado, $mnHoras, $mnCreditos, $mnTurno, $mnRegimen, $mnModalidad, $mbActivo)
	{
		$m_cnx_MySQL = fxAbrirConexion();

		$msConsulta = "update UMO230A set CARRERA_REL = ?, PERIODO_230 = ?, GRADO_230 = ?, HORAS_230 = ?, CREDITOS_230 = ?, TURNO_230 = ?, REGIMEN_230 = ?, MODALIDAD_230 = ?, ACTIVO_230 = ? ";
		$msConsulta .= "where PLANESTUDIO_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCarrera, $msPeriodo, $msGrado, $mnHoras, $mnCreditos, $mnTurno, $mnRegimen, $mnModalidad, $mbActivo, $msCodigo]);
	}
	
	function fxDevuelvePlanPosgrado($mbLlenaGrid, $msCodigo = "")
	{
		$m_cnx_MySQL = fxAbrirConexion();
		
		if ($mbLlenaGrid == 1)
		{
			$msConsulta = "select PLANPOSGRADO_REL, NOMBRE_040, PERIODO_230, ACTIVO_230 from UMO230A join UMO040A on UMO230A.CARRERA_REL = UMO040A.CARRERA_REL order by PLANPOSGRADO_REL desc";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute();
		}
		else
		{
			$msConsulta = "select PLANPOSGRADO_REL, CARRERA_REL, PERIODO_230, GRADO_230, HORAS_230, CREDITOS_230, TURNO_230, REGIMEN_230, MODALIDAD_230, ACTIVO_230 from UMO230A where PLANPOSGRADO_REL = ?";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute([$msCodigo]);
		}
		
		return $mDatos;
	}

	function fxBorrarDetPlanPosgrado($msCodigo)
	{
		$m_cnx_MySQL = fxAbrirConexion();

		$msConsulta = "delete from UMO231A where PLANPOSGRADO_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo]);
	}

	function fxGuardarDetPlanPosgrado($msCodigo, $mnConsecutivo, $msCurso, $msPeriodo, $msModulo, $mnHPreseciales, $mnHAutoestudio, $mnHTrabajo, $mnHTotales, $mnCreditos)
	{
		$m_cnx_MySQL = fxAbrirConexion();

		$msConsulta = "insert into UMO231A (PLANPOSGRADO_REL, DETPLAN_REL, CURSOPOSGRADO_REL, PERIODO_231, MODULO_231, HPRESENCIALES_231, HAUTOESTUDIO_231, ";
		$msConsulta .= "HTRABAJO_231, HTOTALES_231, CREDITOS_231) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo, $mnConsecutivo, $msCurso, $msPeriodo, $msModulo, $mnHPreseciales, $mnHAutoestudio, $mnHTrabajo, $mnHTotales, $mnCreditos]);
	}

	function fxObtenerDetPlanPosgrado($msCodigo)
	{
		$m_cnx_MySQL = fxAbrirConexion();

		$msConsulta = "select PLANPOSGRADO_REL, DETPLAN_REL, UMO231A.CURSOPOSGRADO_REL, NOMBRE_240, PERIODO_231, MODULO_231, ";
		$msConsulta .= "HPRESENCIALES_231, HAUTOESTUDIO_231, HTRABAJO_231, HTOTALES_231, CREDITOS_231 ";
		$msConsulta .= "from UMO231A join UMO240A on UMO231A.CURSOPOSGRADO_REL = UMO240A.CURSOPOSGRADO_REL "; 
		$msConsulta .= "where PLANPOSGRADO_REL = ? order by DETPLAN_REL";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo]);
		return $mDatos;
	}
?>