<?php
	session_start();
	if (!isset($_SESSION["gnVerifica"]) or $_SESSION["gnVerifica"] != 1)
	{
		echo('<meta http-equiv="Refresh" content="0;url=index.php"/>');
		exit('');
	}
	
	include ("masterApp.php");
	require_once ("funciones/fxGeneral.php");
	require_once ("funciones/fxUsuarios.php");
	require_once ("funciones/fxClientes.php");
	$Registro = fxVerificaUsuario();
	$m_cnx_MySQL = fxAbrirConexion();

	if ($Registro == 0)
	{
?>

<div class="container text-center">
	<div id="DivContenido">
	    <img src="imagenes/errordeacceso.png"/>
    </div>
 </div>
<?php }
	else
	{
		$mbAdministrador = fxVerificaAdministrador();
		$mbPermisoUsuario = fxPermisoUsuario("catClientes");
		
		if ($mbAdministrador == 0 and $mbPermisoUsuario == 0)
		{?>
        <div class="container text-center">
        	<div id="DivContenido">
				<img src="imagenes/errordeacceso.png"/>
            </div>
        </div>
		<?php }
		else
		{
			if (isset($_POST["txtCliente"]))
			{
				$msCodigo = $_POST["txtCliente"];
				$msCedula = $_POST["txtCedula"];
				$msNombres = $_POST["txtNombres"];
				$msApellidos = $_POST["txtApellidos"];
				$mnTipoEstudio = $_POST["cboTipoEstudio"];

				{
					if ($msCodigo == "")
					{
						$msCodigo = fxGuardarCliente($msCedula, $msNombres, $msApellidos, $mnTipoEstudio);
						$msBitacora = $msCodigo . "; " . $msCedula . "; " . $msNombres . "; " . $msApellidos . "; " . $mnTipoEstudio;
						fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO220A", $msCodigo, "", "Agregar", $msBitacora);
					}
					else
					{
						fxModificarCliente($msCodigo, $msCedula, $msNombres, $msApellidos, $mnTipoEstudio);
						$msBitacora = $msCodigo . "; " . $msCedula . "; " . $msNombres . "; " . $msApellidos . "; " . $mnTipoEstudio;
						fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO220A", $msCodigo, "", "Modificar", $msBitacora);
					}
				}
									
				?><meta http-equiv="Refresh" content="0;url=gridClientes.php"/><?php
			}
			else
			{
				if (isset($_POST["UMOJN"]))
					$msCodigo = $_POST["UMOJN"];
				else
					$msCodigo = "";
				
				if ($msCodigo != "")
				{
					$mDatos = fxDevuelveCliente(0, $msCodigo);
					$mFila = $mDatos->fetch();
					$msCedula = $mFila["CEDULA_220"];
					$msNombres = $mFila["NOMBRES_220"];
					$msApellidos = $mFila["APELLIDOS_220"];
					$mnTipoEstudio = $mFila["TIPOESTUDIO_220"];
				}
				else
				{
					$msCodigo = "";
					$msCedula = "";
					$msNombres = "";
					$msApellidos = "";
					$mnTipoEstudio = 0;
				}
	?>
    <div class="container text-left">
    	<div id="DivContenido">
			<div class = "row">
				<div class="col-xs-12 col-md-11">
					<div class="degradado"><strong>Catálogo de clientes</strong></div>
				</div>
			</div>

			<div class = "row">
                <div class="col-sm-12 offset-sm-0 col-md-10 offset-md-2">
					<form id="catClientes" name="catClientes" action="catClientes.php" onsubmit="return verificarFormulario()" method="post">
						<div class = "form-group row">
							<label for="txtCliente" class="col-sm-12 col-md-2 col-form-label">Cliente</label>
							<div class="col-sm-12 col-md-3">
							<?php
								echo('<input type="text" class="form-control" id="txtCliente" name="txtCliente" value="' . $msCodigo . '" readonly />'); 
							?>
							</div>
						</div>
						
						<div class = "form-group row">
							<label for="txtCedula" class="col-sm-12 col-md-2 col-form-label">Cédula</label>
							<div class="col-sm-12 col-md-3">
							<?php echo('<input type="text" class="form-control" id="txtCedula" name="txtCedula" value="' . $msCedula . '" />'); ?>
							</div>
						</div>

						<div class = "form-group row">
							<label for="txtNombres" class="col-sm-12 col-md-2 col-form-label">Nombres</label>
							<div class="col-sm-12 col-md-7">
							<?php echo('<input type="text" class="form-control" id="txtNombres" name="txtNombres" value="' . $msNombres . '" />'); ?>
							</div>
						</div>

						<div class = "form-group row">
							<label for="txtApellidos" class="col-sm-12 col-md-2 col-form-label">Apellidos</label>
							<div class="col-sm-12 col-md-7">
							<?php echo('<input type="text" class="form-control" id="txtApellidos" name="txtApellidos" value="' . $msApellidos . '" />'); ?>
							</div>
						</div>

						<div class="form-group row">
							<label for="cboTipoEstudio" class="col-sm-12 col-md-2 col-form-label">Tipo de estudio</label>
							<div class="col-sm-12 col-md-3">
								<select class="form-control" id="cboTipoEstudio" name="cboTipoEstudio">
									<option value=""></option>
									<?php
										if ($mnTipoEstudio == 0)
											echo("<option value='0' selected>Grado regular</option>");
										else
											echo("<option value='0'>Grado regular</option>");

										if ($mnTipoEstudio == 1)
											echo("<option value='1' selected>Grado sabatino</option>");
										else
											echo("<option value='1'>Grado sabatino</option>");

										if ($mnTipoEstudio == 2)
											echo("<option value='2' selected>Posgrado</option>");
										else
											echo("<option value='2'>Posgrado</option>");

										if ($mnTipoEstudio == 3)
											echo("<option value='3' selected>Curso libre</option>");
										else
											echo("<option value='3'>Curso libre</option>");
									?>
								</select>
							</div>
						</div>

						<div class = "row">
							<div class="col-auto offset-sm-0 col-md-10 offset-md-2">
								<input type="submit" id="Guardar" name="Guardar" value="Guardar" class="btn btn-primary" />
								<input type="button" id="Cancelar" name="Cancelar" value="Cancelar" class="btn btn-primary" onclick="location.href='gridClientes.php';"/>
							</div>
						</div>
					</form>
                </div>
			</div>
		</div>
	</div>
<?php	}
	}
}
?>
</body>
</html>
<script type='text/javascript'>
	var mCedula;
	var existeCedula;
	var mCliente; 

	function verificarFormulario()
	{		
		if(document.getElementById('txtCedula').value=="")
		{
			$.messager.alert('UMOJN','Falta la Cédula.','warning');
			return false;
		}

		$.when(obtenerCedula()).done(function(respuesta)
		{
			if (respuesta != "")
				existeCedula = true;
			else
				existeCedula = false;

			mCliente = respuesta;
		})

		if(mCedula.indexOf("-") > -1)
		{
			document.getElementById('txtCedula').focus();
			$.messager.alert('UMOJN','Escriba la Cédula sin guiones.','warning');
			return false;
		}

		if (existeCedula == true)
		{
			document.getElementById('txtCedula').focus();
			$.messager.alert('UMOJN','La Cédula ya fue registrada con el cliente ' + mCliente,'warning');
			return false;
		}

		if(document.getElementById('txtNombres').value=="")
		{
			$.messager.alert('UMOJN','Faltan los Nombres.','warning');
			return false;
		}

		if(document.getElementById('txtApellidos').value=="")
		{
			$.messager.alert('UMOJN','Faltan los Apellidos.','warning');
			return false;
		}

		return true;
	}

	function obtenerCedula()
	{
		mCliente = document.getElementById('txtCliente').value;
		mCedula = document.getElementById('txtCedula').value;
		parametros = '{"cedula":"' + mCedula + '", "cliente":"' + mCliente + '"}';
		datosJson = JSON.parse(parametros);
	
		return $.ajax({
			url:'funciones/fxDatosClientes.php',
			type:'post',
			async: false,
			data:datosJson,
			beforeSend: function(){console.log(datosJson)}
		})
	}
</script>