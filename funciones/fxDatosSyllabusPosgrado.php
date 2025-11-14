<?php
require_once ("fxGeneral.php");

/**********Llenar el combo de los Cursos**********/
if (isset($_POST["planPosgrado"]))
{
	$m_cnx_MySQL = fxAbrirConexion();
	$msCodigo = $_POST["planPosgrado"];
	$msConsulta = "Select UMO240A.CURSOPOSGRADO_REL, NOMBRE_240 from UMO240A, UMO231A where UMO231A.CURSOPOSGRADO_REL = UMO240A.CURSOPOSGRADO_REL and PLANPOSGRADO_REL = ? order by NOMBRE_240";
	$mDatos = $m_cnx_MySQL->prepare($msConsulta);
	$mDatos->execute([$msCodigo]);
	$mnRegistros = $mDatos->rowCount();
	$msResultado = "";

	if ($mnRegistros > 0)
	{
		while ($mFila = $mDatos->fetch())
		{
			$msResultado .= "<option value='" . $mFila["CURSOPOSGRADO_REL"] . "'>" . $mFila["NOMBRE_240"] . "</option>";
		}
	}
	
	echo $msResultado;
}

/**********Llenar el combo del Plan de estudios**********/
if (isset($_POST["carreraPE"]))
{
	$m_cnx_MySQL = fxAbrirConexion();
	$msCarrera = $_POST["carreraPE"];
	$msConsulta = "Select PLANPOSGRADO_REL, PERIODO_230, ACTIVO_230 from UMO230A where CARRERA_REL = ?";
	$mDatos = $m_cnx_MySQL->prepare($msConsulta);
	$mDatos->execute([$msCarrera]);
	$mnRegistros = $mDatos->rowCount();
	$msResultado = "";

	if ($mnRegistros > 0)
	{
		while ($mFila = $mDatos->fetch())
		{
			$msPlanPosgrado = $mFila["PLANPOSGRADO_REL"];
			$msPeriodo = $mFila["PERIODO_230"];

			if ($msSyllabus == "")
			{
				if (intval($mFila["ACTIVO_230"])==1)
					$msResultado .= "<option value='" . $msPlanPosgrado . "'>Período " . $msPeriodo . "</option>";
			}
			else
				$msResultado .= "<option value='" . $msPlanPosgrado . "'>Período " . $msPeriodo . "</option>";
		}
	}
	
	$msRespuesta = array('resultado'=>$msResultado, 'planEstudio'=>$msPlanPosgrado);
	echo json_encode($msRespuesta);
}
?>