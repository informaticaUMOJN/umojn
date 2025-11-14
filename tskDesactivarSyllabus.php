<?php
    /*Se desactiva el syllabus cuando se concluye la última fecha de clases*/
    require_once ("funciones/fxGeneral.php");
    $m_cnx_MySQL = fxAbrirConexion();
    
    $mdFechaHoy = date('Y-m-d');
	
	//Desactiva los Cobros que ya han sido pagados
	$msConsulta = "select SYLLABUS_REL from UMO070A where ACTIVO_070 = 1";
	$mDatos = $m_cnx_MySQL->prepare($msConsulta);
	$mDatos->execute();
	
	while ($mFila = $mDatos->fetch())
	{
		$msSyllabus = $mFila["SYLLABUS_REL"];

        $msConsulta = "Select ifnull(max(FECHA_073), '1900-01-01') as ULTIMAFECHA from UMO073A where SYLLABUS_REL = ?";
        $mDatosAux = $m_cnx_MySQL->prepare($msConsulta);
        $mDatosAux->execute([$msSyllabus]);
        $mrAux = $mDatosAux->fetch();
        $mdUltimaFecha = $mrAux["ULTIMAFECHA"];
			
        if ($mdFechaHoy > $mdUltimaFecha)
        {
            $msConsulta = "update UMO070A set ACTIVO_070 = 0 where SYLLABUS_REL = ?";
            $mDatosAux = $m_cnx_MySQL->prepare($msConsulta);
            $mDatosAux->execute([$msSyllabus]);
        }
    }
?>