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
	require_once ("funciones/fxCursosPosgrado.php");
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
		$mbPermisoUsuario = fxPermisoUsuario("catCursosPosgrado");
		
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
			if (isset($_POST["txtCursos"]))
			{
				$msCodigo = $_POST["txtCursos"];
				$msNombre = $_POST["txtNombre"];
				$msCarrera = $_POST["cboCarrera"];
				$msCodCurso = $_POST["txtCodCurso"];
				$mbActivo = $_POST["optActivo"];

				{
					if ($msCodigo == "")
					{
						$msCodigo = fxGuardarCursoPosgrado($msCarrera, $msNombre, $msCodCurso, $mbActivo);
						$msBitacora = $msCodigo . "; " . $msCarrera . "; " . $msNombre . "; " . $msCodCurso . "; " . $mbActivo;
						fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO240A", $msCodigo, "", "Agregar", $msBitacora);
					}
					else
					{
						fxModificarCursoPosgrado($msCodigo, $msCarrera, $msNombre, $msCodCurso, $mbActivo);
						$msBitacora = $msCodigo . "; " . $msCarrera . "; " . $msNombre . "; " . $msCodCurso . "; " . $mbActivo;
						fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO240A", $msCodigo, "", "Modificar", $msBitacora);
					}
				}
									
				?><meta http-equiv="Refresh" content="0;url=gridCursosPosgrado.php"/><?php
			}
			else
			{
				if (isset($_POST["UMOJN"]))
					$msCodigo = $_POST["UMOJN"];
				else
					$msCodigo = "";
				
				if ($msCodigo != "")
				{
					$mDatos = fxDevuelveCursoPosgrado(0, $msCodigo);
					$mFila = $mDatos->fetch();
					$msCarrera = $mFila["CARRERA_REL"];
					$msNombre = $mFila["NOMBRE_240"];
					$msCodCurso = $mFila["CODIGO_240"];
					$mbActivo = $mFila["ACTIVO_240"];
				}
				else
				{
					$msCarrera = "";
					$msNombre = "";
					$msCodCurso = "";
					$mbActivo = 0;
				}
	?>
    <div class="container text-left">
    	<div id="DivContenido">
			<div class = "row">
				<div class="col-xs-12 col-md-11">
					<div class="degradado"><strong>Catálogo de Cursos de posgrado</strong></div>
				</div>
			</div>

			<div class = "row">
                <div class="col-sm-12 offset-sm-0 col-md-10 offset-md-2">
					<form id="catCursosPosgrado" name="catCursosPosgrado" action="catCursosPosgrado.php" onsubmit="return verificarFormulario()" method="post">
						<div class = "form-group row">
							<label for="txtCursos" class="col-sm-12 col-md-2 col-form-label">Curso</label>
							<div class="col-sm-12 col-md-3">
							<?php
								echo('<input type="text" class="form-control" id="txtCursos" name="txtCursos" value="' . $msCodigo . '" readonly />'); 
							?>
							</div>
						</div>
						
						<div class="form-group row">
							<label for="cboCarrera" class="col-sm-12 col-md-2 col-form-label">Carrera</label>
							<div class="col-sm-12 col-md-7">
								<select class="form-control" id="cboCarrera" name="cboCarrera">
									<?php
										$m_cnx_MySQL = fxAbrirConexion();
										$msConsulta = "select CARRERA_REL, NOMBRE_040 from UMO040A where POSGRADO_040 = 1 order by NOMBRE_040";
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
							<label for="txtNombre" class="col-sm-12 col-md-2 col-form-label">Nombre</label>
							<div class="col-sm-12 col-md-7">
							<?php echo('<input type="text" class="form-control" id="txtNombre" name="txtNombre" value="' . $msNombre . '" />'); ?>
							</div>
						</div>
                        
						<div class = "form-group row">
							<label for="txtCodCurso" class="col-sm-12 col-md-2 col-form-label">Código</label>
							<div class="col-sm-12 col-md-3">
								<?php echo('<input type="text" class="form-control" id="txtCodCurso" name="txtCodCurso" value="' . $msCodCurso . '" />'); ?>
							</div>
						</div>

						<div class="form-group row">
                            <label for="optActivo" class="col-sm-auto col-md-2 form-label">Activo</label>
                            <div class="col-sm-12 col-md-3">
                                <div class="radio">
                                <?php
									if ($mbActivo == 1)
										echo('<input type="radio" id="optActivo1" name="optActivo" value="0" /> No <input type="radio" id="optActivo2" name="optActivo" value="1" checked/> Si');
									else
										echo('<input type="radio" id="optActivo1" name="optActivo" value="0" checked/> No <input type="radio" id="optActivo2" name="optActivo" value="1" /> Si');
								?>
								</div>
							</div>
                        </div>

						<div class = "row">
							<div class="col-auto offset-sm-0 col-md-12 offset-md-2">
								<input type="submit" id="Guardar" name="Guardar" value="Guardar" class="btn btn-primary" />
								<input type="button" id="Cancelar" name="Cancelar" value="Cancelar" class="btn btn-primary" onclick="location.href='gridCursosPosgrado.php';"/>
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

		if(document.getElementById('txtCodCurso').value=="")
		{
			$.messager.alert('UMOJN','Falta el Código.','warning');
			return false;ódigo
		}
        
		return true;
	}
</script>