<?php
require_once ("fxGeneral.php");

//Llena el Textbox del cierre de actas
if (isset($_POST["anno"]) and isset($_POST["semestre"]) and isset($_POST["parcial"]) and isset($_POST["turno"]) and isset($_POST["asignatura"]) and isset($_POST["docente"]))
{
    $m_cnx_MySQL = fxAbrirConexion();
    $mnAnno = $_POST["anno"];
    $mnSemestre = $_POST["semestre"];
    $mnParcial = $_POST["parcial"];
    $mnTurno = $_POST["turno"];
    $msAsignatura = $_POST["asignatura"];
    $msDocente = $_POST["docente"];

    $msConsulta = "select ESTADO_162 from UMO162A where ANNO_162 = ? and SEMESTRE_162 = ? and PARCIAL_162 = ? and TURNO_162 = ? ";
    $msConsulta .= "and DOCENTE_REL = ? and ASIGNATURA_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$mnAnno, $mnSemestre, $mnParcial, $mnTurno, $msDocente, $msAsignatura]);
    $mnRegistros = $mDatos->rowCount();

    if ($mnRegistros == 0)
        $mnEstado = 0;
    else
    {
        $mFila = $mDatos->fetch();
        $mnEstado = $mFila["ESTADO_162"];
    }
    echo $mnEstado;
}

//Llena el grid de los períodos cerrados
if (isset($_POST["annoCierre"]) and isset($_POST["semestreCierre"]) and isset($_POST["parcialCierre"]) and isset($_POST["turnoCierre"]))
{
    $m_cnx_MySQL = fxAbrirConexion();
    $mnAnno = $_POST["annoCierre"];
    $mnSemestre = $_POST["semestreCierre"];
    $mnParcial = $_POST["parcialCierre"];
    $mnTurno = $_POST["turnoCierre"];

    $msConsulta = "select NOMBRE_002, ANNO_162, SEMESTRE_162, (case ESTADO_162 when 0 then 'Abierto' when 1 then 'Cerrado' end) as ESTADO_162, ";
    $msConsulta .= "UMO162A.DOCENTE_REL, UMO162A.ASIGNATURA_REL, (case PARCIAL_162 when 0 then '1er. parcial' ";
    $msConsulta .= "when 1 then '2do. parcial' when 2 then '3er. parcial' when 3 then 'Ex. Extraordinario' ";
    $msConsulta .= "when 4 then 'Intersemestral' when 5 then 'Convalidación' end) as PARCIAL_162, ";
    $msConsulta .= "(case TURNO_162 when 1 then 'Diurno' when 2 then 'Matutino' when 3 then 'Vespertino' ";
    $msConsulta .= "when 4 then 'Nocturno' when 5 then 'Sabatino' when 6 then 'Dominical' end) as TURNO_162, ";
    $msConsulta .= "FECHA_162, NOMBRE_060, NOMBRE_100 from UMO162A join UMO002A on USUARIO_162 = USUARIO_REL join UMO100A on ";
    $msConsulta .= "UMO162A.DOCENTE_REL = UMO100A.DOCENTE_REL join UMO060A on UMO162A.ASIGNATURA_REL = UMO060A.ASIGNATURA_REL ";
    $msConsulta .= "where ANNO_162 = ? and SEMESTRE_162 = ? and PARCIAL_162 = ? and TURNO_162 = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$mnAnno, $mnSemestre, $mnParcial, $mnTurno]);
    $mnRegistros = $mDatos->rowCount();
    $msResultado = "[";
    $i = 1;

    while ($mFila = $mDatos->fetch())
    {
        $msResultado .= '{"NOMBRE_002":"' . $mFila["NOMBRE_002"] . '","ANNO_162":"' . $mFila["ANNO_162"] . '", ';
        $msResultado .= '"SEMESTRE_162":"' . $mFila["SEMESTRE_162"] . '", "PARCIAL_162":"' . $mFila["PARCIAL_162"] . '", ';
        $msResultado .= '"TURNO_162":"' . $mFila["TURNO_162"] . '", "FECHA_162":"' . $mFila["FECHA_162"] . '", ';
        $msResultado .= '"NOMBRE_060":"' . $mFila["NOMBRE_060"] . '", "NOMBRE_100":"' . $mFila["NOMBRE_100"] . '", ';
        $msResultado .= '"DOCENTE_REL":"' . $mFila["DOCENTE_REL"] . '", "ASIGNATURA_REL":"' . $mFila["ASIGNATURA_REL"] . '", ';
        $msResultado .= '"ESTADO_162":"' . $mFila["ESTADO_162"] . '"}';

        if ($i != $mnRegistros)
            $msResultado .= ',';

        $i++;
    }
    $msResultado .= ']';
    echo($msResultado);
}

//Cambia el estado de un registro en el grid
if (isset($_POST["annoEst"]) and isset($_POST["semestreEst"]) and isset($_POST["parcialEst"]) and isset($_POST["turnoEst"]) and isset($_POST["docenteEst"]) and isset($_POST["asignaturaEst"]) and isset($_POST["estadoEst"]))
{
    $m_cnx_MySQL = fxAbrirConexion();
    $mnAnno = $_POST["annoEst"];
    $mnSemestre = $_POST["semestreEst"];
    $mnParcial = $_POST["parcialEst"];
    $mnTurno = $_POST["turnoEst"];
    $msDocente = $_POST["docenteEst"];
    $msAsignatura = $_POST["asignaturaEst"];
    $mnEstado = $_POST["estadoEst"];

    $msConsulta = "update UMO162A set ESTADO_162 = ? where ANNO_162 = ? and SEMESTRE_162 = ? and PARCIAL_162 = ? and ";
    $msConsulta .= "TURNO_162 = ? and DOCENTE_REL = ? and ASIGNATURA_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$mnEstado, $mnAnno, $mnSemestre, $mnParcial, $mnTurno, $msDocente, $msAsignatura]);
}
?>