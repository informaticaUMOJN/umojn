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
	require_once ("funciones/fxCarreras.php");
	$Registro = fxVerificaUsuario();
	
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
		$mbPermisoUsuario = fxPermisoUsuario("catCarreras");
		
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
			if (isset($_POST["txtCarrera"]))
			{
				$msCodigo = $_POST["txtCarrera"];
				$msNombre = $_POST["txtNombre"];
				$msSiglas = $_POST["txtSiglas"];
				$mbPosgrado = $_POST["optPosgrado"];

				{
					if ($msCodigo == "")
					{
						$msCodigo = fxGuardarCarrera($msNombre, $msSiglas, $mbPosgrado);
						$msBitacora = $msCodigo . "; " . $msNombre . "; " . $msSiglas . "; " . $mbPosgrado;
						fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO040A", $msCodigo, "", "Agregar", $msBitacora);
					}
					else
					{
						fxModificarCarrera ($msCodigo, $msNombre, $msSiglas, $mbPosgrado);
						$msBitacora = $msCodigo . "; " . $msNombre . "; " . $msSiglas . "; " . $mbPosgrado;
						fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO040A", $msCodigo, "", "Modificar", $msBitacora);
					}
				}
									
				?><meta http-equiv="Refresh" content="0;url=gridCarreras.php"/><?php
			}
			else
			{
				if (isset($_POST["UMOJN"]))
					$msCodigo = $_POST["UMOJN"];
				else
					$msCodigo = "";
				
				if ($msCodigo != "")
				{
					$mDatos = fxDevuelveCarrera(0, $msCodigo);
					$mFila = $mDatos->fetch();
					$msNombre = $mFila["NOMBRE_040"];
					$msSiglas = $mFila["SIGLAS_040"];
					$mbPosgrado = $mFila["POSGRADO_040"];
				}
				else
				{
					$msNombre = "";
					$msSiglas = "";
					$mbPosgrado = 0;
				}
	?>
    <div class="container text-left">
    	<div id="DivContenido">
			<div class = "row">
				<div class="col-xs-12 col-md-11">
					<div class="degradado"><strong>Catálogo de carreras</strong></div>
				</div>
			</div>

			<div class = "row">
                <div class="col-sm-12 offset-sm-0 col-md-10 offset-md-2">
					<form id="catCarreras" name="catCarreras" action="catCarreras.php" onsubmit="return verificarFormulario()" method="post">
						<div class = "form-group row">
							<label for="txtCarrera" class="col-sm-12 col-md-2 col-form-label">Carrera</label>
							<div class="col-sm-12 col-md-3">
							<?php
								echo('<input type="text" class="form-control" id="txtCarrera" name="txtCarrera" value="' . $msCodigo . '" readonly />'); 
							?>
							</div>
						</div>
						
						<div class = "form-group row">
							<label for="txtNombre" class="col-sm-12 col-md-2 col-form-label">Nombre</label>
							<div class="col-sm-12 col-md-7">
							<?php echo('<input type="text" class="form-control" id="txtNombre" name="txtNombre" value="' . $msNombre . '" />'); ?>
							</div>
						</div>

						<div class = "form-group row">
							<label for="txtSiglas" class="col-sm-12 col-md-2 col-form-label">Siglas</label>
							<div class="col-sm-12 col-md-2">
							<?php echo('<input type="text" class="form-control" id="txtSiglas" name="txtSiglas" value="' . $msSiglas . '" maxlength="5"/>'); ?>
							</div>
						</div>

						<div class = "form-group row">
                            <label for="optPosgrado" class="col-sm-12 col-md-2 form-label">Posgrado</label>
                            <div class="col-sm-12 col-md-3">
                                <div class = "radio">
                                <?php
                                    if ($mbPosgrado == 1)
                                        echo('<input type="radio" id="optPosgrado1" name="optPosgrado" value="0" /> No <input type="radio" id="optPosgrado2" name="optPosgrado" value="1" checked="checked" /> Si');
                                    else
                                        echo('<input type="radio" id="optPosgrado1" name="optPosgrado" value="0" checked="checked" /> No <input type="radio" id="optPosgrado2" name="optPosgrado" value="1" /> Si');
                                ?>
                                </div>
                            </div>
                        </div>

						<div class = "row">
							<div class="col-auto offset-sm-0 col-md-12 offset-md-2">
								<input type="submit" id="Guardar" name="Guardar" value="Guardar" class="btn btn-primary" />
								<input type="button" id="Cancelar" name="Cancelar" value="Cancelar" class="btn btn-primary" onclick="location.href='gridCarreras.php';"/>
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
		if(document.getElementById('txtNombre').value=="")
		{
			$.messager.alert('UMOJN','Falta el Nombre.','warning');
			return false;
		}

		if(document.getElementById('txtSiglas').value=="")
		{
			$.messager.alert('UMOJN','Faltan las siglas.','warning');
			return false;
		}

		return true;
	}
</script>