<?php
require_once ("fxGeneral.php");

/**********Llenar el detalle de los estudiantes**********/
if (isset($_POST["curso"]) and isset($_POST["cohorte"]))
{
	$m_cnx_MySQL = fxAbrirConexion();
    $msCurso = $_POST["curso"];
    $msCohorte = $_POST["cohorte"];
    $msConsulta = "select '' as ASISTENCIAPOS_REL, UMO260A.MATRICULAPOS_REL, NOMBRE1_250, NOMBRE2_250, APELLIDO1_250, APELLIDO2_250, 1 as ESTADO_301 ";
    $msConsulta .= "from UMO260A, UMO250A where UMO260A.ESTUDIANTEPOS_REL = UMO250A.ESTUDIANTEPOS_REL ";
    $msConsulta .= "and UMO260A.CURSOPOSGRADO_REL = ? and ESTADO_260 = 0 and COHORTE_260 = ? ";
    $msConsulta .= "order by APELLIDO1_250, APELLIDO2_250, NOMBRE1_250, NOMBRE2_250";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCurso, $msCohorte]);
    $mnRegistros = $mDatos->rowCount();
    $msResultado = "[";
    $i = 1;

    while ($mFila = $mDatos->fetch())
    {
        $msEstudiante = trim($mFila["APELLIDO1_250"]);
        if (trim($mFila["APELLIDO2_250"]) != "")
            $msEstudiante .= " " . $mFila["APELLIDO2_250"];

        $msEstudiante .= ", " . $mFila["NOMBRE1_250"];

        if (trim($mFila["NOMBRE2_250"]) != "")
            $msEstudiante .= " " . $mFila["NOMBRE2_250"];
            
        $msResultado .= '{"matricula":"' . $mFila["MATRICULA_REL"] . '","estudiante":"' . $msEstudiante . '","asistencia":"0","acumulado":"0","trabajo":"0","nota":"0"}';
        if ($i != $mnRegistros)
            $msResultado .= ',';

        $i++;
    }
    $msResultado .= ']';
    echo($msResultado);
}

/**********Verificar la existencia de la calificación**********/
if (isset($_POST["curso2"]) and isset($_POST["cohorte2"]))
{
    $m_cnx_MySQL = fxAbrirConexion();
    $msCurso = $_POST["curso2"];
    $msCohorte = $_POST["cohorte2"];
    $msConsulta = "select * from UMO310A where CURSO_REL = ? and COHORTE_310 = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msFecha, $msCurso, $msCohorte]);
    $mnRegistros = $mDatos->rowCount();
    echo($mnRegistros);
}

/**********Llenar el combo de los Cursos**********/
if (isset($_POST["docenteCur"]) and isset($_POST["cohorteCur"]))
{
	$m_cnx_MySQL = fxAbrirConexion();
	$msDocente = $_POST["docenteCur"];
	$msCohorte = $_POST["cohorteCur"];
    $msConsulta = "select distinct UMO240A.CURSOPOSGRADO_REL, NOMBRE_240 from UMO240A, UMO260A, UMO290A where ";
    $msConsulta .= "UMO240A.CURSOPOSGRADO_REL = UMO260A.CURSOPOSGRADO_REL and UMO240A.CURSOPOSGRADO_REL = UMO290A.CURSOPOSGRADO_REL and ";
    $msConsulta .= "UMO290A.DOCENTE_REL = ? and COHORTE_290 = ? and ACTIVO_290 = ? order by NOMBRE_240";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msDocente, $msCohorte, 1]);
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
?>
?>