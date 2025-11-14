<?php
require_once ("fxGeneral.php");

if (isset($_POST["asignatura"]) and isset($_POST["turno"]) and isset($_POST["anno"]) and isset($_POST["semestre"]))
{
	$m_cnx_MySQL = fxAbrirConexion();
    $msCodigo = $_POST["asignatura"];
    $mnSemestre = $_POST["semestre"];
    $mnAnno = $_POST["anno"];
    $mnTurno = $_POST["turno"];
    $msConsulta = "select '' as ASISTENCIA_REL, UMO030A.MATRICULA_REL, NOMBRE1_010, NOMBRE2_010, APELLIDO1_010, APELLIDO2_010, 1 as ESTADO_151 ";
	$msConsulta .= "from UMO031A, UMO030A, UMO010A, UMO050A where UMO031A.MATRICULA_REL = UMO030A.MATRICULA_REL and UMO030A.ESTUDIANTE_REL = UMO010A.ESTUDIANTE_REL ";
    $msConsulta .= "and UMO030A.PLANESTUDIO_REL = UMO050A.PLANESTUDIO_REL and UMO031A.ASIGNATURA_REL = ? and TURNO_050 = ? and ESTADO_030 = 0 and ANNOLECTIVO_030 = ? and SEMESTREACADEMICO_030 = ? ";
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
            
        switch ($mFila['ESTADO_151'])
        {
            case 0:
                $msEstado = "Presente";
                break;

            case 1:
                $msEstado = "Ausente";
                break;

            default:
                $msEstado = "Justificado";
        }
        $msResultado .= '{"matricula":"' . $mFila["MATRICULA_REL"] . '","estudiante":"' . $msEstudiante . '","estado":"' . $msEstado . '"}';
        if ($i != $mnRegistros)
            $msResultado .= ',';

        $i++;
    }
    $msResultado .= ']';
    echo($msResultado);
}

if (isset($_POST["asignatura2"]) and isset($_POST["fecha"]))
{
    $m_cnx_MySQL = fxAbrirConexion();
    $msAsignatura = $_POST["asignatura2"];
    $msFecha = $_POST["fecha"];
    $msConsulta = "select * from UMO150A where FECHA_150 = ? and ASIGNATURA_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msFecha, $msAsignatura]);
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