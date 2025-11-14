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
	require_once ("funciones/fxDiasNHabiles.php");

	$m_cnx_MySQL = fxAbrirConexion();
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
		$mbPermisoUsuario = fxPermisoUsuario("hrrDiasFeriados");
		
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
			if (isset($_POST["txtDias"]))
			{
				$msCodigo = $_POST["txtDias"];
				$msFecha = $_POST["dtpFecha"];
                $msMotivo = $_POST["txtMotivo"];

				if (isset($_POST["gridDocumentos"]))
				    $gridDocumentos = $_POST["gridDocumentos"];
					
				if ($msCodigo == "")
				{
					$msCodigo = fxGuardarNHabiles($msFecha, $msMotivo);
					$msBitacora = $msCodigo . "; " . $msFecha . "; " . "; " .$msMotivo;
					fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO007A", $msCodigo, "", "Agregar", $msBitacora);
				}
				else
				{
					fxModificarNHabiles ($msCodigo, $msFecha, $msMotivo);
					$msBitacora = $msCodigo . "; " . $msFecha . "; " . "; " . $msMotivo;
					fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO007A", $msCodigo, "", "Modificar", $msBitacora);
				}
				?><meta http-equiv="Refresh" content="0;url=gridDiasFeriados.php"/><?php
			}
			else
			{
				if (isset($_POST["UMOJN"]))
					$msCodigo = $_POST["UMOJN"];
				else
					$msCodigo = "";
				if ($msCodigo != "")
				{
					$objRecordSet = fxDevuelveNHabiles(0, $msCodigo);
					$mFila = $objRecordSet->fetch();
					$msFecha = $mFila["FECHA_007"];
					$msMotivo = $mFila ["MOTIVO_007"];
				}
				else
				{
					$msFecha = date('Y-m-d');
					$msMotivo = "";
				}
	?>
 <div class="container text-left">
    	<div id="DivContenido">
			<div class = "row">
				<div class="col-xs-12 col-md-11">
                    <div class="degradado"><strong>Dias feriados</strong></div>
                </div>
            </div>
			<div class = "row">
                <div class="col-sm-12 offset-sm-0 col-md-10 offset-md-2">
					<form id="hrrDiasNHablies" name="hrrDiasNHabiles" action="hrrDiasNHabiles.php" onsubmit="return verificarFormulario()" method="post">
						<div class = "form-group row">
							<label for="txtDias" class="col-sm-12 col-md-3 form-label">Codigo de cobro</label>
							<div class="col-sm-12 col-md-3">
								<?php
								echo('<input type="text" class="form-control" id="txtDias" name="txtDias" value="' . $msCodigo . '" readonly />'); 
								?>
							</div>
						</div>
	
						<div class = "form-group row">
							<label for="dtpFecha" class="col-sm-12 col-md-3 form-label">Fecha</label>
							<div class="col-sm-12 col-md-4">
								<?php echo('<input type="date" class="form-control" id="dtpFecha" name="dtpFecha" value="' . $msFecha . '" />'); ?>
							</div>
						</div>

						<div class = "form-group row">
							<label for="txtMotivo" class="col-sm-12 col-md-3 form-label">Motivo</label>
							<div class="col-sm-12 col-md-7">
								<?php echo('<input type="text" class="form-control" id="txtMotivo" name="txtMotivo" value="' . $msMotivo . '" />'); ?>
							</div>
						</div>
		
						<div class = "row">
							<div class="col-auto offset-sm-0 col-md-12 offset-md-3">
								<input type="submit" id="Guardar" name="Guardar" value="Guardar" class="btn btn-primary"/>
								<input type="button" id="Cancelar" name="Cancelar" value="Cancelar" class="btn btn-primary" onclick="location.href='gridDiasFeriados.php';"/>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
		<?php
		}
	}
}
?>
</div>
</div>
</div>
</body>
</html>
<script>
	function verificarFormulario()
	{
		if(document.getElementById('txtMotivo').value=="")
		{
			document.getElementById('txtMotivo').focus();
			$.messager.alert('UMOJN','Falta el motivo.','warning');
			return false;
		}
	}
</script>