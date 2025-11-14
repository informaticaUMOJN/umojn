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
	require_once ("funciones/fxCursoslibres.php");
	$mnRegistro = fxVerificaUsuario();
	
	if ($mnRegistro == 0)
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
		$mbPermisoUsuario = fxPermisoUsuario("catCursosLibres");
		
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

				{
					if ($msCodigo == "")
					{
						$msCodigo = fxGuardarCursosLibres($msNombre );
						$msBitacora = $msCodigo . "; " . $msNombre  ;
						fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO190A", $msCodigo, "", "Agregar", $msBitacora);
					}
					else
					{
						fxModificarCursosLibres($msCodigo, $msNombre );
						$msBitacora = $msCodigo . "; " . $msNombre  ;
						fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO190A", $msCodigo, "", "Modificar", $msBitacora);
					}
				}
									
				?><meta http-equiv="Refresh" content="0;url=gridCursosLibres.php"/><?php
			}
			else
			{
				if (isset($_POST["UMOJN"]))
					$msCodigo = $_POST["UMOJN"];
				else
					$msCodigo = "";
				
				if ($msCodigo != "")
				{
					$mDatos = fxDevuelveCursosLibres(0, $msCodigo);
					$mFila = $mDatos->fetch();
					$msNombre = $mFila["NOMBRE_190"];
				}
				else
				{
					$msNombre = "";
				}
	?>
    <div class="container text-left">
    	<div id="DivContenido">
			<div class = "row">
				<div class="col-xs-12 col-md-11">
					<div class="degradado"><strong>Catálogo de Cursos Libres</strong></div>
				</div>
			</div>

			<div class = "row">
                <div class="col-sm-12 offset-sm-0 col-md-10 offset-md-2">
					<form id="catCursosLibres" name="catCursosLibres" action="catCursosLibres.php" onsubmit="return verificarFormulario()" method="post">
						<div class = "form-group row">
							<label for="txtCursos" class="col-sm-12 col-md-2 col-form-label">Cursos</label>
							<div class="col-sm-12 col-md-3">
							<?php
								echo('<input type="text" class="form-control" id="txtCursos" name="txtCursos" value="' . $msCodigo . '" readonly />'); 
							?>
							</div>
						</div>
						
						<div class = "form-group row">
							<label for="txtNombre" class="col-sm-12 col-md-2 col-form-label">Nombre</label>
							<div class="col-sm-12 col-md-7">
							<?php echo('<input type="text" class="form-control" id="txtNombre" name="txtNombre" value="' . $msNombre . '" />'); ?>
							</div>
						</div>
                        
                      

						<div class = "row">
							<div class="col-auto offset-sm-0 col-md-12 offset-md-2">
								<input type="submit" id="Guardar" name="Guardar" value="Guardar" class="btn btn-primary" />
								<input type="button" id="Cancelar" name="Cancelar" value="Cancelar" class="btn btn-primary" onclick="location.href='gridCursosLibres.php';"/>
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
        
		return true;
	}
</script>