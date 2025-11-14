<?php
require_once ("fxGeneral.php");

if (isset($_POST["asignatura"]) and isset($_POST["turno"]) and isset($_POST["anno"]) and isset($_POST["semestre"]) and isset($_POST["parcial"]))
{
	$m_cnx_MySQL = fxAbrirConexion();
    $msCodigo = $_POST["asignatura"];
    $mnTurno = $_POST["turno"];
    $mnAnno = $_POST["anno"];
    $mnParcial = $_POST["parcial"];
    $mnSemestre = $_POST["semestre"];
    $msConsulta = "select '' as CALIFICACION_REL, UMO030A.MATRICULA_REL, NOMBRE1_010, NOMBRE2_010, APELLIDO1_010, APELLIDO2_010, 0 as NOTA_161 ";
    $msConsulta .= "from UMO031A, UMO030A, UMO010A, UMO050A where UMO031A.MATRICULA_REL = UMO030A.MATRICULA_REL and UMO030A.ESTUDIANTE_REL = UMO010A.ESTUDIANTE_REL ";
    $msConsulta .= "and UMO030A.PLANESTUDIO_REL = UMO050A.PLANESTUDIO_REL and UMO031A.ASIGNATURA_REL = ? and TURNO_050 = ? and ANNOLECTIVO_030 = ? and SEMESTREACADEMICO_030 = ? and ESTADO_030 = 0 ";
    $msConsulta .= "order by APELLIDO1_010, APELLIDO2_010, NOMBRE1_010, NOMBRE2_010";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo, $mnTurno, $mnAnno, $mnSemestre]);
    $mnRegistros = $mDatos->rowCount();
    $msResultado = "[";
    $i = 1;

    while ($mFila = $mDatos->fetch())
    {
        $msEstudiante = trim($mFila["APELLIDO1_010"]);
        if (trim($mFila["APELLIDO2_010"]) != "")
            $msEstudiante .= " " . $mFila["APELLIDO2_010"];

        $msEstudiante .= ", " . $mFila["NOMBRE1_010"];

        if (trim($mFila["NOMBRE2_010"]) != "")
            $msEstudiante .= " " . $mFila["NOMBRE2_010"];

        if ($mnParcial <= 2)
            $mNota = 0;
        else
            $mNota = "No aplica";
            
        $msResultado .= '{"matricula":"' . $mFila["MATRICULA_REL"] . '","estudiante":"' . $msEstudiante . '","nota":"' . $mNota . '"}';
        if ($i != $mnRegistros)
            $msResultado .= ',';

        $i++;
    }
    $msResultado .= ']';

	echo ($msResultado);
}

/******Valida que las calificaciones no se repitan******/
if (isset($_POST["asignatura2"]) and isset($_POST["anno"]) and isset($_POST["semestre"]) and isset($_POST["parcial"]) and isset($_POST["turno"]))
{
    $m_cnx_MySQL = fxAbrirConexion();
    $msAsignatura = $_POST["asignatura2"];
    $mnAnno = $_POST["anno"];
    $mnSemestre = $_POST["semestre"];
    $mnParcial = $_POST["parcial"];
    $mnTurno = $_POST["turno"];
    $msConsulta = "select * from UMO160A where ANNO_160 = ? and SEMESTRE_160 = ? and PARCIAL_160 = ? and TURNO_160 = ? and ASIGNATURA_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$mnAnno, $mnSemestre, $mnParcial, $mnTurno, $msAsignatura]);
    $mnRegistros = $mDatos->rowCount();
    echo($mnRegistros);
}

/**********Llenar el combo de las Asignaturas**********/
if (isset($_POST["carreraAsg"]) and isset($_POST["docenteAsg"]) and isset($_POST["annoAsg"]) and isset($_POST["semestreAsg"]) and isset($_POST["turnoAsg"]))
{
	$m_cnx_MySQL = fxAbrirConexion();
	$msCarrera = $_POST["carreraAsg"];
    $msDocente = $_POST["docenteAsg"];
    $mnAnno = $_POST["annoAsg"];
    $mnSemestre = $_POST["semestreAsg"];
    $mnTurno = $_POST["turnoAsg"];
    $msConsulta = "select UMO060A.ASIGNATURA_REL, NOMBRE_060 from UMO060A, UMO070A ";
    $msConsulta .= "where UMO060A.ASIGNATURA_REL = UMO070A.ASIGNATURA_REL and CARRERA_REL = ? and DOCENTE_REL = ? ";
    $msConsulta .= "and ANNO_070 = ? and SEMESTRE_070 = ? and TURNO_070 = ? order by NOMBRE_060";
	$mDatos = $m_cnx_MySQL->prepare($msConsulta);
	$mDatos->execute([$msCarrera, $msDocente, $mnAnno, $mnSemestre, $mnTurno]);
	$mnRegistros = $mDatos->rowCount();
	$msResultado = "";

	if ($mnRegistros > 0)
	{
		while ($mFila = $mDatos->fetch())
		{
			$msResultado .= "<option value='" . $mFila["ASIGNATURA_REL"] . "'>" . $mFila["NOMBRE_060"] . "</option>";
		}
	}
	
	echo $msResultado;
}
?>