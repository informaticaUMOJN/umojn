<?php
require_once ("fxGeneral.php");

//Llena el Textbox del cierre de actas
if (isset($_POST["anno"]) and isset($_POST["semestre"]) and isset($_POST["parcial"]) and isset($_POST["turno"]))
{
    $m_cnx_MySQL = fxAbrirConexion();
    $mnAnno = $_POST["anno"];
    $mnSemestre = $_POST["semestre"];
    $mnParcial = $_POST["parcial"];
    $mnTurno = $_POST["turno"];

    $msConsulta = "select * from UMO162A where ANNO_162 = ? and SEMESTRE_162 = ? and PARCIAL_162 = ? and TURNO_162 = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$mnAnno, $mnSemestre, $mnParcial, $mnTurno]);
    $mnRegistros = $mDatos->rowCount();
    echo $mnRegistros;
}

//Llena el grid de los períodos cerrados
if (isset($_POST["annoCierre"]))
{
    $m_cnx_MySQL = fxAbrirConexion();
    $mnAnnoCierre = $_POST["annoCierre"];

    $msConsulta = "select NOMBRE_002, ANNO_162, SEMESTRE_162, (case PARCIAL_162 when 0 then '1er. parcial' ";
    $msConsulta .= "when 1 then '2do. parcial' when 2 then '3er. parcial' when 3 then 'Ex. Extraordinario' ";
    $msConsulta .= "when 4 then 'Intersemestral' when 5 then 'Convalidación' end) as PARCIAL_162, ";
    $msConsulta .= "(case TURNO_162 when 1 then 'Diurno' when 2 then 'Matutino' when 3 then 'Vespertino' ";
    $msConsulta .= "when 4 then 'Nocturno' when 5 then 'Sabatino' when 6 then 'Dominical' end) as TURNO_162, ";
    $msConsulta .= "FECHA_162 from UMO162A join UMO002A on USUARIO_162 = USUARIO_REL where ANNO_162 = ? ";
    $msConsulta .= "order by SEMESTRE_162 desc, PARCIAL_162 desc";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$mnAnnoCierre]);
    $mnRegistros = $mDatos->rowCount();
    $msResultado = "[";
    $i = 1;

    while ($mFila = $mDatos->fetch())
    {
        $msResultado .= '{"NOMBRE_002":"' . $mFila["NOMBRE_002"] . '","ANNO_162":"' . $mFila["ANNO_162"] . '", ';
        $msResultado .= '"SEMESTRE_162":"' . $mFila["SEMESTRE_162"] . '", "PARCIAL_162":"' . $mFila["PARCIAL_162"] . '", ';
        $msResultado .= '"TURNO_162":"' . $mFila["TURNO_162"] . '", "FECHA_162":"' . $mFila["FECHA_162"] . '"}';

        if ($i != $mnRegistros)
            $msResultado .= ',';

        $i++;
    }
    $msResultado .= ']';
    echo($msResultado);
}
?>