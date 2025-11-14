<?php
    function fxGuardarPlanEstudio($msCarrera, $msPeriodo, $msGrado, $mnHoras, $mnCreditos, $mnTurno, $mnRegimen, $mnModalidad, $mbActivo)
    {
        $m_cnx_MySQL = fxAbrirConexion();
        $msConsulta = "Select ifnull(mid(max(PLANESTUDIO_REL), 3), 0) as Ultimo from UMO050A";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute();
        $mFila = $mDatos->fetch();
        $mnNumero = intval($mFila["Ultimo"]);
        $mnNumero += 1;
        $mnLongitud = strlen($mnNumero);
        $msCodigo = "PE" . str_repeat("0", 8 - $mnLongitud) . trim($mnNumero);
        $msConsulta = "insert into UMO050A (PLANESTUDIO_REL, CARRERA_REL, PERIODO_050, GRADO_050, HORAS_050, CREDITOS_050, TURNO_050, REGIMEN_050, MODALIDAD_050, ACTIVO_050) ";
        $msConsulta .= "values(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$msCodigo, $msCarrera, $msPeriodo, $msGrado, $mnHoras, $mnCreditos, $mnTurno, $mnRegimen, $mnModalidad, $mbActivo]);
        return ($msCodigo);
    }

    function fxModificarPlanEstudio($msCodigo, $msCarrera, $msPeriodo, $msGrado, $mnHoras, $mnCreditos, $mnTurno, $mnRegimen, $mnModalidad, $mbActivo)
	{
		$m_cnx_MySQL = fxAbrirConexion();

		$msConsulta = "update UMO050A set CARRERA_REL = ?, PERIODO_050 = ?, GRADO_050 = ?, HORAS_050 = ?, CREDITOS_050 = ?, TURNO_050 = ?, REGIMEN_050 = ?, MODALIDAD_050 = ?, ACTIVO_050 = ? ";
		$msConsulta .= "where PLANESTUDIO_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCarrera, $msPeriodo, $msGrado, $mnHoras, $mnCreditos, $mnTurno, $mnRegimen, $mnModalidad, $mbActivo, $msCodigo]);
	}
	
	function fxDevuelvePlanEstudio($mbLlenaGrid, $msCodigo = "")
	{
		$m_cnx_MySQL = fxAbrirConexion();
		
		if ($mbLlenaGrid == 1)
		{
			$msConsulta = "select PLANESTUDIO_REL, NOMBRE_040, PERIODO_050, ACTIVO_050 from UMO050A join UMO040A on UMO050A.CARRERA_REL = UMO040A.CARRERA_REL order by PLANESTUDIO_REL desc";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute();
		}
		else
		{
			$msConsulta = "select PLANESTUDIO_REL, CARRERA_REL, PERIODO_050, GRADO_050, HORAS_050, CREDITOS_050, TURNO_050, REGIMEN_050, MODALIDAD_050, ACTIVO_050 from UMO050A where PLANESTUDIO_REL = ?";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute([$msCodigo]);
		}
		
		return $mDatos;
	}

	function fxBorrarDetPlanEstudio($msCodigo)
	{
		$m_cnx_MySQL = fxAbrirConexion();

		$msConsulta = "delete from UMO051A where PLANESTUDIO_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo]);
	}

	function fxGuardarDetPlanEstudio($msCodigo, $mnConsecutivo, $msAsignatura, $msRequisito, $mnSemestre, $mnHPreseciales, $mnHAutoestudio, $mnHTrabajo, $mnHTotales, $mnCreditos)
	{
		$m_cnx_MySQL = fxAbrirConexion();

		$msConsulta = "insert into UMO051A (PLANESTUDIO_REL, CONSECUTIVO_REL, ASIGNATURA_REL, UMO_ASIGNATURA_REL, SEMESTRE_051, HPRESENCIALES_051, HAUTOESTUDIO_051, ";
		$msConsulta .= "HTRABAJO_051, HTOTALES_051, CREDITOS_051) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		if ($msRequisito!="")
			$mDatos->execute([$msCodigo, $mnConsecutivo, $msAsignatura, $msRequisito, $mnSemestre, $mnHPreseciales, $mnHAutoestudio, $mnHTrabajo, $mnHTotales, $mnCreditos]);
		else
			$mDatos->execute([$msCodigo, $mnConsecutivo, $msAsignatura, NULL, $mnSemestre, $mnHPreseciales, $mnHAutoestudio, $mnHTrabajo, $mnHTotales, $mnCreditos]);
	}

	function fxObtenerDetPlanEstudio($msCodigo)
	{
		$m_cnx_MySQL = fxAbrirConexion();

		$msConsulta = "select PLANESTUDIO_REL, CONSECUTIVO_REL, ASIGNATURA_REL, UMO_ASIGNATURA_REL, fxNombreAsignatura(ASIGNATURA_REL) as ASIGNATURA, ";
		$msConsulta .= "fxNombreAsignatura(UMO_ASIGNATURA_REL) as REQUISITO, SEMESTRE_051, HPRESENCIALES_051, HAUTOESTUDIO_051, HTRABAJO_051, HTOTALES_051, CREDITOS_051 ";
		$msConsulta .= "from UMO051A where PLANESTUDIO_REL = ? order by SEMESTRE_051, ASIGNATURA_REL";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo]);
		return $mDatos;
	}
?>