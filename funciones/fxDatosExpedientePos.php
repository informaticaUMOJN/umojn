<?php
require_once ("fxGeneral.php");

if (isset($_POST["carrera"]) and isset($_POST["generacion"]))
{
	$m_cnx_MySQL = fxAbrirConexion();
    $msCarrera = $_POST["carrera"];
    $mnGeneracion = $_POST["generacion"];
    $msConsulta = "SELECT ESTUDIANTEPOS_REL, ANNOACADEMICO_250, CARNET_250, NOMBRE1_250, NOMBRE2_250, APELLIDO1_250, APELLIDO2_250 FROM UMO250A WHERE CARRERA_REL = ? AND ANNOACADEMICO_250 = ?";;
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCarrera, $mnGeneracion]);
    $mnRegistros = $mDatos->rowCount();
    $msResultado = "[";
    $i = 1;

    while ($mFila = $mDatos->fetch())
    {
        $msEstudiante = trim($mFila["NOMBRE1_250"]);
        if (trim($mFila["NOMBRE2_250"]) != "")
            $msEstudiante .= " " . $mFila["NOMBRE2_250"];

        $msEstudiante .= " " . $mFila["APELLIDO1_250"];

        if (trim($mFila["APELLIDO2_250"]) != "")
            $msEstudiante .= " " . $mFila["APELLIDO2_250"];
            
        

        $msResultado .= '{"codigo":"' . $mFila["ESTUDIANTEPOS_REL"] . '","carnet":"' . $mFila["CARNET_250"] . '","generacion":"' . $mFila["ANNOACADEMICO_250"] . '","estudiante":"' . $msEstudiante . '"}';
        if ($i != $mnRegistros)
            $msResultado .= ',';

        $i++;
    }
    $msResultado .= ']';
    echo($msResultado);
}
?>