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

if (isset($_POST["tipoEstudio2"]) and isset($_POST["cobro"])) {
    $mnTipoEstudio = $_POST["tipoEstudio2"];
    $msCodCobro = $_POST["cobro"];

    $msConsulta = "select A.* from (select UMO220A.CLIENTE_REL, CONCAT_WS(' ', NOMBRES_220, APELLIDOS_220) AS NOMBRECLIENTE ";
    $msConsulta .= "from UMO220A where TIPOESTUDIO_220 = ?) as A left join (select UMO220A.CLIENTE_REL, COBRO_REL from UMO220A ";
    $msConsulta .= "join UMO131A on UMO220A.CLIENTE_REL = UMO131A.CLIENTE_REL where COBRO_REL = ?) as B on A.CLIENTE_REL = B.CLIENTE_REL ";
    $msConsulta .= "where B.CLIENTE_REL is null order by A.NOMBRECLIENTE";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$mnTipoEstudio, $msCodCobro]);
    $mnRegistros = $mDatos->rowCount();
    $msResultado = "[";
    $i = 1;

    while ($mFila = $mDatos->fetch()) {
        $msResultado .= '{"cliente":"' . $mFila["CLIENTE_REL"] . '","nombre":"' . $mFila["NOMBRECLIENTE"] . '"}';
        if ($i != $mnRegistros)
            $msResultado .= ',';

        $i++;
    }

    $msResultado .= ']';
    echo($msResultado);
}
?>