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
	require_once ("funciones/fxAsignaturas.php");
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
		$mbPermisoUsuario = fxPermisoUsuario("catAsignaturas");
		
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
			if (isset($_POST["txtAsignatura"]))
			{
				$msCodigo = $_POST["txtAsignatura"];
				$msCarrera = $_POST["cboCarrera"];
				$msCodAcademico = $_POST["txtCodigo"];
				$msNombre = $_POST["txtNombre"];
				$msDescGral = $_POST["txtDescGral"];
				$mnParciales = $_POST["cboParciales"];

				if ($msCodigo == "")
				{
					$msCodigo = fxGuardarAsignatura($msCarrera, $msCodAcademico, $msNombre, $msDescGral, $mnParciales);
					$msBitacora = $msCodigo . "; " . $msCarrera . "; " . $msCodAcademico . "; " . $msNombre . "; " . $msDescGral . "; " . $mnParciales;
					fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO060A", $msCodigo, "", "Agregar", $msBitacora);
				}
				else
				{
					fxModificarAsignatura ($msCodigo, $msCarrera, $msCodAcademico, $msNombre, $msDescGral, $mnParciales);
					$msBitacora = $msCodigo . "; " . $msCarrera . "; " . $msCodAcademico . "; " . $msNombre . "; " . $msDescGral . "; " . $mnParciales;
					fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO060A", $msCodigo, "", "Modificar", $msBitacora);
				}
									
				?><meta http-equiv="Refresh" content="0;url=gridAsignaturas.php"/><?php
			}
			else
			{
				if (isset($_POST["UMOJN"]))
					$msCodigo = $_POST["UMOJN"];
				else
					$msCodigo = "";
				
				if ($msCodigo != "")
				{
					$mDatos = fxDevuelveAsignatura(0, $msCodigo);
					$mFila = $mDatos->fetch();
					$msCarrera = $mFila["CARRERA_REL"];
					$msCodAcademico = $mFila["CODIGO_060"];
					$msNombre = $mFila["NOMBRE_060"];
					$msDescGral = $mFila["DESCGRAL_060"];
					$mnParciales = $mFila["PARCIALES_060"];
				}
				else
				{
					$msCarrera = "";
					$msCodAcademico = "";
					$msNombre = "";
					$msDescGral = "";
					$mnParciales = 3;
				}
	?>
    <div class="container text-left">
    	<div id="DivContenido">
			<div class = "row">
				<div class="col-xs-12 col-md-11">
					<div class="degradado"><strong>Catálogo de asignaturas</strong></div>
				</div>
			</div>

			<div class = "row">
                <div class="col-sm-12 offset-sm-0 col-md-10 offset-md-2">
					<form id="catAsignaturas" name="catAsignaturas" action="catAsignaturas.php" onsubmit="return verificarFormulario()" method="post">
						<div class = "form-group row">
							<label for="txtAsignatura" class="col-sm-12 col-md-3 col-form-label">Asignatura</label>
							<div class="col-sm-12 col-md-3">
							<?php
								echo('<input type="text" class="form-control" id="txtAsignatura" name="txtAsignatura" value="' . $msCodigo . '" readonly />'); 
							?>
							</div>
						</div>
						
						<div class = "form-group row">
							<label for="txtCodigo" class="col-sm-12 col-md-3 col-form-label">Código académico</label>
							<div class="col-sm-12 col-md-3">
							<?php echo('<input type="text" class="form-control" id="txtCodigo" name="txtCodigo" value="' . $msCodAcademico . '" />'); ?>
							</div>
						</div>

						<div class="form-group row">
							<label for="cboCarrera" class="col-sm-12 col-md-3 col-form-label">Carrera</label>
							<div class="col-sm-12 col-md-7">
								<select class="form-control" id="cboCarrera" name="cboCarrera">
									<?php
										$msConsulta = "select CARRERA_REL, NOMBRE_040 from UMO040A where POSGRADO_040 = 0 order by NOMBRE_040";
										$mDatos = $m_cnx_MySQL->prepare($msConsulta);
										$mDatos->execute();
										while ($mFila = $mDatos->fetch())
										{
											$msValor = rtrim($mFila["CARRERA_REL"]);
											$msTexto = rtrim($mFila["NOMBRE_040"]);
											if ($msCodigo == "")
												echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
											else
											{
												if ($msCarrera == "")
												{
													echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
													$msCarrera = $msValor;
												}
												else
												{
													if ($msCarrera == $msValor)
														echo("<option value='" . $msValor . "' selected>" . $msTexto . "</option>");
													else
														echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
												}
											}
										}
									?>
								</select>
							</div>
						</div>

						<div class = "form-group row">
							<label for="txtNombre" class="col-sm-12 col-md-3 col-form-label">Nombre</label>
							<div class="col-sm-12 col-md-7">
							<?php echo('<input type="text" class="form-control" id="txtNombre" name="txtNombre" value="' . $msNombre . '" />'); ?>
							</div>
						</div>

						<div class="form-group row">
							<label for="txtDescGral" class="col-sm-12 col-md-3 col-form-label">Descripción general</label>
							<div class="col-sm-12 col-md-7">
								<?php echo('<textarea class="form-control" id="txtDescGral" name="txtDescGral" rows="2" maxlength="300">' . $msDescGral . '</textarea>'); ?>
							</div>
						</div>

						<div class="form-group row">
							<label for="cboParciales" class="col-sm-12 col-md-3 col-form-label">Parciales</label>
							<div class="col-sm-12 col-md-2">
								<select class="form-control" id="cboParciales" name="cboParciales">
									<?php
										if ($mnParciales == 2){
											echo("<option value='2' selected>2</option>");
											echo("<option value='3'>3</option>");
										}
										else{
											echo("<option value='2'>2</option>");
											echo("<option value='3' selected>3</option>");
										}
									?>
								</select>
							</div>
						</div>

						<div class = "row">
							<div class="col-auto offset-sm-0 col-md-12 offset-md-3">
								<input type="submit" id="Guardar" name="Guardar" value="Guardar" class="btn btn-primary" />
								<input type="button" id="Cancelar" name="Cancelar" value="Cancelar" class="btn btn-primary" onclick="location.href='gridAsignaturas.php';"/>
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
	function verificarFormulario()
	{		
		if(document.getElementById('txtCodigo').value=="")
		{
			$.messager.alert('UMOJN','Falta el código académico.','warning');
			return false;
		}

		if(document.getElementById('txtNombre').value=="")
		{
			$.messager.alert('UMOJN','Falta el Nombre.','warning');
			return false;
		}


		return true;
	}
</script>