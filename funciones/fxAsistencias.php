<?php
	function fxGuardarAsistencia($msDocente, $msAsignatura, $msCarrera, $mdFecha, $mnTurno, $mnAnno, $mnSemestre)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "Select ifnull(mid(max(ASISTENCIA_REL), 4), 0) as Ultimo from UMO150A";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute();
		$mFila = $mDatos->fetch();
		$mnNumero = intval($mFila["Ultimo"]);
		$mnNumero += 1;
		$mnLongitud = strlen($mnNumero);
		$msCodigo = "AST" . str_repeat("0", 7 - $mnLongitud) . trim($mnNumero);
		$msConsulta = "insert into UMO150A (ASISTENCIA_REL, DOCENTE_REL, ASIGNATURA_REL, CARRERA_REL, FECHA_150, TURNO_150, ANNO_150, SEMESTRE_150) values(?, ?, ?, ?, ?, ?, ?, ?)";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo, $msDocente, $msAsignatura, $msCarrera, $mdFecha, $mnTurno, $mnAnno, $mnSemestre]);
		return $msCodigo;
	}
	
	function fxModificarAsistencia($msCodigo, $msDocente, $msAsignatura, $msCarrera, $mdFecha, $mnTurno, $mnAnno, $mnSemestre)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "update UMO150A set DOCENTE_REL = ?, ASIGNATURA_REL = ?, CARRERA_REL = ?, FECHA_150 = ?, TURNO_150 = ?, ANNO_150 = ?, SEMESTRE_150 = ? where ASISTENCIA_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msDocente, $msAsignatura, $msCarrera, $mdFecha, $mnTurno, $mnAnno, $mnSemestre, $msCodigo]);
	}
	
	function fxBorrarAsistencia($msCodigo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "delete from UMO151A where ASISTENCIA_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo]);
		$msConsulta = "delete from UMO150A where ASISTENCIA_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo]);
	}
	
	function fxDevuelveAsistencia($mbLlenaGrid, $msDocente, $msCodigo)
	{
		$m_cnx_MySQL = fxAbrirConexion();

		if ($mbLlenaGrid == 1)
		{
			if ($msDocente == "")
			{
				$msConsulta = "select ASISTENCIA_REL, NOMBRE_100, NOMBRE_060, NOMBRE_040, FECHA_150, TURNO_150, ANNO_150, SEMESTRE_150 from UMO150A, UMO100A, UMO060A, UMO040A ";
				$msConsulta .= "where UMO150A.DOCENTE_REL = UMO100A.DOCENTE_REL and UMO150A.ASIGNATURA_REL = UMO060A.ASIGNATURA_REL and ";
				$msConsulta .= "UMO150A.CARRERA_REL = UMO040A.CARRERA_REL order by ASISTENCIA_REL desc";
				$mDatos = $m_cnx_MySQL->prepare($msConsulta);
				$mDatos->execute();
			}
			else
			{
				$msConsulta = "select ASISTENCIA_REL, NOMBRE_100, NOMBRE_060, NOMBRE_040, FECHA_150, TURNO_150, ANNO_150, SEMESTRE_150 from UMO150A, UMO100A, UMO060A, UMO040A ";
				$msConsulta .= "where UMO150A.DOCENTE_REL = UMO100A.DOCENTE_REL and UMO150A.ASIGNATURA_REL = UMO060A.ASIGNATURA_REL and ";
				$msConsulta .= "UMO150A.CARRERA_REL = UMO040A.CARRERA_REL and UMO150A.DOCENTE_REL = ? order by ASISTENCIA_REL desc";
				$mDatos = $m_cnx_MySQL->prepare($msConsulta);
				$mDatos->execute([$msDocente]);
			}
		}
		else
		{
			$msConsulta = "select ASISTENCIA_REL, DOCENTE_REL, ASIGNATURA_REL, CARRERA_REL, FECHA_150, TURNO_150, ANNO_150, SEMESTRE_150 from UMO150A where ASISTENCIA_REL = ?";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute([$msCodigo]);
		}
		return $mDatos;
	}

	function fxGuardarDetAsistencia($msCodigo, $msMatricula, $mnEstado)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "insert into UMO151A (ASISTENCIA_REL, MATRICULA_REL, ESTADO_151) values (?, ?, ?)";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo, $msMatricula, $mnEstado]);
	}

	function fxDevuelveDetAsistencia($msCodigo, $msAsignatura="", $mnTurno = 0, $mnAnno=0, $mnSemestre=0)
	{
		$m_cnx_MySQL = fxAbrirConexion();

		if ($msCodigo != "")
		{
			$msConsulta = "select ASISTENCIA_REL, UMO030A.MATRICULA_REL, NOMBRE1_010, NOMBRE2_010, APELLIDO1_010, APELLIDO2_010, ESTADO_151 ";
			$msConsulta .= "from UMO151A, UMO030A, UMO010A where UMO151A.MATRICULA_REL = UMO030A.MATRICULA_REL and ";
			$msConsulta .= "UMO030A.ESTUDIANTE_REL = UMO010A.ESTUDIANTE_REL and UMO151A.ASISTENCIA_REL = ? ";
			$msConsulta .= "order by APELLIDO1_010, APELLIDO2_010, NOMBRE1_010, NOMBRE2_010";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute([$msCodigo]);
		}
		else
		{
			$msConsulta = "select '' as ASISTENCIA_REL, UMO030A.MATRICULA_REL, NOMBRE1_010, NOMBRE2_010, APELLIDO1_010, APELLIDO2_010, 1 as ESTADO_151 ";
			$msConsulta .= "from UMO031A, UMO030A, UMO010A, UMO050A where UMO031A.MATRICULA_REL = UMO030A.MATRICULA_REL and UMO030A.ESTUDIANTE_REL = UMO010A.ESTUDIANTE_REL ";
			$msConsulta .= "and UMO030A.PLANESTUDIO_REL = UMO050A.PLANESTUDIO_REL and UMO031A.ASIGNATURA_REL = ? and TURNO_050 = ? and ESTADO_030 = 0 and ANNOLECTIVO_030 = ? and SEMESTREACADEMICO_030 = ? ";
			$msConsulta .= "order by APELLIDO1_010, APELLIDO2_010, NOMBRE1_010, NOMBRE2_010";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute([$msAsignatura, $mnTurno, $mnAnno, $mnSemestre]);
		}

		return $mDatos;
	}

	function fxBorrarDetAsistencia($msCodigo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "delete from UMO151A where ASISTENCIA_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo]);
	}
?>