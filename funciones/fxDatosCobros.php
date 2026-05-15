<?php
require_once("fxGeneral.php");
$m_cnx_MySQL = fxAbrirConexion();

if (isset($_POST["tipoEstudio"])) {
    $mnTipoEstudio = $_POST["tipoEstudio"];
    $msResultado = "<option value=''>Seleccione un valor</option>";

    $msConsulta = "select COBRO_REL, DESC_130 from UMO130A where ACTIVO_130 = 1 and TIPOCOBRO_130 = 1 and TIPOESTUDIO_130 = ? order by COBRO_REL;";

    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$mnTipoEstudio]);
    while ($fila = $mDatos->fetch()) {
        $msResultado .= "<option value='" . $fila["COBRO_REL"] . "'>" . $fila["DESC_130"] . "</option>";
    }
    echo $msResultado;
}
?>