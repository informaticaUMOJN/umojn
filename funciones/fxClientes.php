<?php
	//*****CLIENTES************************************************************//
	function fxGuardarCliente($msCedula, $msNombres, $msApellidos, $mnTipoEstudio)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "Select ifnull(mid(max(CLIENTE_REL), 3), 0) as Ultimo from UMO220A";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute();
		$mFila = $mDatos->fetch();
		$mnNumero = intval($mFila["Ultimo"]);
		$mnNumero += 1;
		$mnLongitud = strlen($mnNumero);
		$msCodigo = "CL" . str_repeat("0", 8 - $mnLongitud) . trim($mnNumero);
		$msConsulta = "insert into UMO220A (CLIENTE_REL, CEDULA_220, NOMBRES_220, APELLIDOS_220, TIPOESTUDIO_220) values(?, ?, ?, ?, ?)";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo, $msCedula, $msNombres, $msApellidos, $mnTipoEstudio]);
		return $msCodigo;
	}
	
	function fxModificarCliente($msCodigo, $msCedula, $msNombres, $msApellidos, $mnTipoEstudio)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "update UMO220A set CEDULA_220 = ?, NOMBRES_220 = ?, APELLIDOS_220 = ?, TIPOESTUDIO_220 = ? where CLIENTE_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCedula, $msNombres, $msApellidos, $mnTipoEstudio, $msCodigo]);
	}
	
	function fxBorrarCliente($msCodigo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "delete from UMO220A where CLIENTE_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo]);
	}
	
	function fxDevuelveCliente($mbLlenaGrid, $msCodigo = "")
	{
		$m_cnx_MySQL = fxAbrirConexion();

		if ($mbLlenaGrid == 1)
		{
			$msConsulta = "select CLIENTE_REL, NOMBRES_220, APELLIDOS_220, (case TIPOESTUDIO_220 when 0 then 'Grado regular' ";
			$msConsulta .= "when 1 then 'Grado sabatino' when 2 then 'Posgrado' when 3 then 'Curso libre' end) as TIPOESTUDIO_220 ";
			$msConsulta .= "from UMO220A order by CLIENTE_REL desc";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute();
		}
		else
		{
			$msConsulta = "select CLIENTE_REL, CEDULA_220, NOMBRES_220, APELLIDOS_220, TIPOESTUDIO_220 from UMO220A where CLIENTE_REL = ?";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute([$msCodigo]);
		}
		return $mDatos;
	}
?>