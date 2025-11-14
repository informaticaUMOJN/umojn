<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//session_start();
function fxGuardarCobros($msCarrera, $msCurso, $msDescripcion, $mnTipo, $msMora ,$mnModalidad, $mnRegimen,$mnTurno,$mfValor, $mnMoneda, $msFechaVenc, $mbActivo)
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
            (COBRO_REL, CARRERA_REL, CURSOS_REL, DESC_130, TIPO_130, UMO_COBRO_REL ,MODALIDAD_130, REGIMEN_130, TURNO_130, VALOR_130, MONEDA_130, VENCIMIENTO_130, ACTIVO_130)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$msCodigo, $msCarrera,$msCurso, $msDescripcion, $mnTipo, $msMora,$mnModalidad, $mnRegimen,$mnTurno ,$mfValor, $mnMoneda, $msFechaVenc, $mbActivo]);

        return $msCodigo;

    } catch (PDOException $e) {
        echo "Error al guardar cobro: " . $e->getMessage();
        exit;
    }
}

	function fxModificarCobros($msCodigo, $msCarrera, $msCurso,$msDescripcion, $mnTipo,$msMora,$mnModalidad, $mnRegimen, $mnTurno,$mfValor, $mnMoneda, $msFechaVenc, $mbActivo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "update UMO130A set CARRERA_REL = ?,  CURSOS_REL = ?, DESC_130 = ?,TIPO_130 = ?, UMO_COBRO_REL=?, MODALIDAD_130 =?, REGIMEN_130= ?,TURNO_130=? ,VALOR_130 = ?, MONEDA_130 = ?, VENCIMIENTO_130 = ?, ACTIVO_130 = ? where COBRO_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCarrera,$msCurso ,$msDescripcion, $mnTipo, $msMora, $mnModalidad, $mnRegimen,$mnTurno ,$mfValor, $mnMoneda, $msFechaVenc, $mbActivo, $msCodigo]);
	}

	function fxBorrarCobros($msCodigo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "delete from UMO130A where COBRO_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo]);
	}
	/*
	function fxDevuelveCobros($mbLlenaGrid, $msCodigo = "")
	{
		$m_cnx_MySQL = fxAbrirConexion();
		if ($mbLlenaGrid == 1)
		{

			$msConsulta = " select UMO130A.COBRO_REL, UMO040A.NOMBRE_040 as NOMBRE_CARRERA, UMO130A.DESC_130, UMO130A.VENCIMIENTO_130, 
			case  when UMO130A.ACTIVO_130 = 1 then 'Activo'
            	else 'Inactivo'
        			end as ACTIVO_130 
    			from UMO130A join UMO040A on UMO130A.CARRERA_REL = UMO040A.CARRERA_REL order by UMO130A.COBRO_REL";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute();
		}
		else
		{
			$msConsulta = "select  COBRO_REL, CARRERA_REL,UMO_COBRO_REL, DESC_130 ,TIPO_130, MODALIDAD_130, REGIMEN_130,TURNO_130, VALOR_130, MONEDA_130, VENCIMIENTO_130,ACTIVO_130  from UMO130A where COBRO_REL = ?";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute([$msCodigo]);
		}
		return $mDatos;

	}


	function fxDevuelveCobrosCursos($mbLlenaGrid, $msCodigo = "")
	{
		$m_cnx_MySQL = fxAbrirConexion();
		if ($mbLlenaGrid == 1)
		{

			$msConsulta = " select UMO130A.COBRO_REL, UMO190A.NOMBRE_190 as NOMBRE_CURSOS, UMO130A.DESC_130, UMO130A.VENCIMIENTO_130, 
			case  when UMO130A.ACTIVO_130 = 1 then 'Activo'
            	else 'Inactivo'
        			end as ACTIVO_130 
    			from UMO130A join UMO190A on UMO130A.CURSOS_REL = UMO190A.CURSOS_REL order by UMO130A.COBRO_REL";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute();
		}
		else
		{
			$msConsulta = "select  COBRO_REL, CURSOS_REL,UMO_COBRO_REL, DESC_130 ,TIPO_130, MODALIDAD_130, REGIMEN_130,TURNO_130, VALOR_130, MONEDA_130, VENCIMIENTO_130,ACTIVO_130  from UMO130A where COBRO_REL = ?";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute([$msCodigo]);
		}
		return $mDatos;

	}

	*/
function fxDevuelveCobros($mbLlenaGrid, $msCodigo = "")
{
    $m_cnx_MySQL = fxAbrirConexion();

    if ($mbLlenaGrid == 1) {
       $msConsulta = " select UMO130A.COBRO_REL, COALESCE(UMO040A.NOMBRE_040, UMO190A.NOMBRE_190, 'Sin nombre') as NOMBRE_PROGRAMA,
        UMO130A.DESC_130, UMO130A.VENCIMIENTO_130,
		case 
            when UMO130A.CARRERA_REL is not null then 'Carrera'
            when UMO130A.CURSOS_REL IS not null then 'Curso'
            else 'Otro'
        end as TIPO_PROGRAMA,
        case
            when UMO130A.ACTIVO_130 = 1 then 'Activo'
            else 'Inactivo'
        end as ACTIVO_130 from UMO130A left join UMO040A on UMO130A.CARRERA_REL = UMO040A.CARRERA_REL left join UMO190A on UMO130A.CURSOS_REL = UMO190A.CURSOS_REL order by UMO130A.COBRO_REL";

        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute();
    } else {
        $msConsulta = " select COBRO_REL, CARRERA_REL, CURSOS_REL, UMO_COBRO_REL, DESC_130, TIPO_130, MODALIDAD_130, REGIMEN_130, TURNO_130, VALOR_130, 
                MONEDA_130, VENCIMIENTO_130, ACTIVO_130 from UMO130A where COBRO_REL = ?";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$msCodigo]);
    }
    return $mDatos;
}
?>