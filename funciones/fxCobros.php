<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function fxGuardarCobros($msCobroMora, $msDescripcion, $mnTipoCobro, $mnTipoEstudio, $mnValor, $mnMoneda, $msFechaVenc, $mbActivo)
{ 
    try {
        $m_cnx_MySQL = fxAbrirConexion();

        $msConsulta = "SELECT IFNULL(MID(MAX(COBRO_REL), 7), 0) AS Ultimo FROM UMO130A";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute();
        $mFila = $mDatos->fetch();
        $mnNumero = intval($mFila["Ultimo"]) + 1; 
        $msCodigo = "CBR" . str_pad($mnNumero, 7, "0", STR_PAD_LEFT);

        $msConsulta = "INSERT INTO UMO130A 
            (COBRO_REL, UMO_COBRO_REL, DESC_130, TIPOCOBRO_130, TIPOESTUDIO_130, VALOR_130, MONEDA_130, VENCIMIENTO_130, ACTIVO_130)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
		if ($mnTipoCobro == 2) //Tipo Mora
        	$mDatos->execute([$msCodigo, $msCobroMora, $msDescripcion, $mnTipoCobro, $mnTipoEstudio, $mnValor, $mnMoneda, $msFechaVenc, $mbActivo]);
		else
			$mDatos->execute([$msCodigo, null, $msDescripcion, $mnTipoCobro, $mnTipoEstudio, $mnValor, $mnMoneda, $msFechaVenc, $mbActivo]);

        return $msCodigo;

    } catch (PDOException $e) {
        echo "Error al guardar cobro: " . $e->getMessage();
        exit;
    }
}

function fxModificarCobros($msCodigo, $msCobroMora, $msDescripcion, $mnTipoCobro, $mnTipoEstudio, $mnValor, $mnMoneda, $msFechaVenc, $mbActivo)
{
	$m_cnx_MySQL = fxAbrirConexion();
	$msConsulta = "update UMO130A set UMO_COBRO_REL=?, DESC_130 = ?, TIPOCOBRO_130 = ?, TIPOESTUDIO_130 = ?, VALOR_130 = ?, MONEDA_130 = ?, VENCIMIENTO_130 = ?, ACTIVO_130 = ? where COBRO_REL = ?";
	$mDatos = $m_cnx_MySQL->prepare($msConsulta);
	if ($mnTipoCobro == 2) //Tipo Mora
		$mDatos->execute([$msCobroMora, $msDescripcion, $mnTipoCobro, $mnTipoEstudio, $mnValor, $mnMoneda, $msFechaVenc, $mbActivo, $msCodigo]);
	else
		$mDatos->execute([null, $msDescripcion, $mnTipoCobro, $mnTipoEstudio, $mnValor, $mnMoneda, $msFechaVenc, $mbActivo, $msCodigo]);
}

function fxBorrarCobros($msCodigo)
{
	$m_cnx_MySQL = fxAbrirConexion();
	$msConsulta = "delete from UMO130A where COBRO_REL = ?";
	$mDatos = $m_cnx_MySQL->prepare($msConsulta);
	$mDatos->execute([$msCodigo]);
}

function fxGuardarDetCobros($msCodigo, $msCliente, $mnAdeudado, $mnMoneda)
{
	$m_cnx_MySQL = fxAbrirConexion();
	$msConsulta = "insert into UMO131A (COBRO_REL, CLIENTE_REL, ADEUDADO_131, ABONADO_131, MONEDA_131, DESCUENTO_131, ANULADO_131)";
	$msConsulta .= "values (?, ?, ?, ?, ?, ?, ?)";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
	$mDatos->execute([$msCodigo, $msCliente, $mnAdeudado, 0, $mnMoneda, 0, 0]);
}

function fxDevuelveCobros($mbLlenaGrid, $msCodigo = "")
{
    $m_cnx_MySQL = fxAbrirConexion();

    if ($mbLlenaGrid == 1) {
       	$msConsulta = "select COBRO_REL, DESC_130, VENCIMIENTO_130, ACTIVO_130 from UMO130A order by COBRO_REL desc";
    	$mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute();
    } else {
        $msConsulta = "select COBRO_REL, UMO_COBRO_REL, DESC_130, TIPOCOBRO_130, TIPOESTUDIO_130, VALOR_130, MONEDA_130, VENCIMIENTO_130, ACTIVO_130 from UMO130A where COBRO_REL = ?";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$msCodigo]);
    }
    return $mDatos;
}

function fxDevuelveClientes($msCodigo)
{
    $m_cnx_MySQL = fxAbrirConexion();

    $msConsulta = "select UMO131A.CLIENTE_REL, CONCAT_WS(' ', NOMBRES_220, APELLIDOS_220) as NOMBRECLIENTE, ABONADO_131, DESCUENTO_131, ADEUDADO_131 ";
    $msConsulta .= "from UMO131A join UMO220A on UMO131A.CLIENTE_REL = UMO220A.CLIENTE_REL where COBRO_REL = ? order by NOMBRECLIENTE";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo]);
    return $mDatos;
}

function fxDevuelveClientesNuevos($mnTipoEstudio, $msCobro)
{
    $m_cnx_MySQL = fxAbrirConexion();

    $msConsulta = "select A.* from (select UMO220A.CLIENTE_REL, CONCAT_WS(' ', NOMBRES_220, APELLIDOS_220) AS NOMBRECLIENTE ";
    $msConsulta .= "from UMO220A where TIPOESTUDIO_220 = ?) as A left join (select UMO220A.CLIENTE_REL, COBRO_REL from UMO220A ";
    $msConsulta .= "join UMO131A on UMO220A.CLIENTE_REL = UMO131A.CLIENTE_REL where COBRO_REL = ?) as B on A.CLIENTE_REL = B.CLIENTE_REL ";
    $msConsulta .= "where B.CLIENTE_REL is null order by A.NOMBRECLIENTE";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$mnTipoEstudio, $msCobro]);
    return $mDatos;
}
?>