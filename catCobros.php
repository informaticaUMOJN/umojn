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
	require_once ("funciones/fxCobros.php");

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
<?php 
	}
	else
	{
		$mbAdministrador = fxVerificaAdministrador();
		$mbPermisoUsuario = fxPermisoUsuario("catCobros");
		if ($mbAdministrador == 0 and $mbPermisoUsuario == 0)
		{
		?>
			<div class="container text-center">
				<div id="DivContenido">
					<img src="imagenes/errordeacceso.png"/>
				</div>
			</div>
		<?php 
		}
		else
		{
			if (isset($_POST["txtCobros"]))
			{
				$msCodigo = $_POST["txtCobros"];
				$msMora = $_POST["cboMora"];
				$msDescripcion = $_POST["txtDescripcion"];
				$mnTipoCobro = $_POST["optTipoCobro"];
				$mnTipoEstudio = $_POST["optTipoEstudio"];
				$mnValor = $_POST["txtValor"];
				$mnMoneda = $_POST["optMoneda"];
				$msFechaVenc = $_POST["dtpFechaVenc"];
				$mbActivo = $_POST["optActivo"];

				if ($msCodigo == "")
				{
					$msCodigo = fxGuardarCobros($msMora, $msDescripcion, $mnTipoCobro, $mnTipoEstudio, $mnValor, $mnMoneda, $msFechaVenc, $mbActivo);
					$msBitacora = $msCodigo . "; " . $msMora . "; " . $msDescripcion . "; " . $mnTipoCobro . "; " .$mnTipoEstudio.";". $mnValor . "; " . $mnMoneda . ";" . $msFechaVenc . "; " . $mbActivo;
					fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO130A", $msCodigo, "", "Agregar", $msBitacora);
				}
				else
				{
					fxModificarCobros ($msCodigo, $msMora, $msDescripcion, $mnTipoCobro, $mnTipoEstudio, $mnValor, $mnMoneda, $msFechaVenc, $mbActivo);
					$msBitacora = $msCodigo . "; " . $msMora . "; " . $msDescripcion . "; " . $mnTipoCobro . "; " .$mnTipoEstudio.";". $mnValor . "; " . $mnMoneda . ";" . $msFechaVenc . "; " . $mbActivo;
					fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO130A", $msCodigo, "", "Modificar", $msBitacora);
				}
				?><meta http-equiv="Refresh" content="0;url=gridCobros.php"/><?php
			}
		}
	}

	if (isset($_POST["UMOJN"]))
		$msCodigo = $_POST["UMOJN"];
	else
		$msCodigo = "";

	if ($msCodigo != "")
	{
		$objRecordSet = fxDevuelveCobros(0, $msCodigo);
		$mFila = $objRecordSet->fetch();
		$msMora = $mFila["UMO_COBRO_REL"];
		$msDescripcion = $mFila["DESC_130"];
		$mnTipoCobro = $mFila["TIPOCOBRO_130"];
		$mnTipoEstudio = $mFila["TIPOESTUDIO_130"];
		$mnValor = floatval($mFila["VALOR_130"]);
		$mnMoneda = $mFila["MONEDA_130"];
		$msFechaVenc = $mFila["VENCIMIENTO_130"];
		$mbActivo = $mFila ["ACTIVO_130"];
	}
	else
	{
		$msMora = "";
		$msDescripcion = "";
		$mnTipoCobro = "0";
		$mnTipoEstudio ="0";
		$mnValor= 0;
		$mnMoneda ="";
		$msFechaVenc = date('Y-m-d');
		$mbActivo = 0; 	
	}
?>
			
	<div class="container text-left">
		<div id="DivContenido">
			<div class = "row">
				<div class="col-xs-12 col-md-11">
					<div class="degradado"><strong>Catálogo de cobros</strong></div>
				</div>
			</div>
			<div class = "row">
				<div class="col-sm-13 offset-sm-0 col-md-9 offset-md-1">
					<form id="catCobros" name="catCobros" action="catCobros.php" onsubmit="return verificarFormulario()" method="post">
						<div class = "form-group row">
							<label for="txtCobros" class="col-sm-12 col-md-3 form-label">Codigo de cobro</label>
							<div class="col-sm-12 col-md-3">
								<?php
									echo('<input type="text" class="form-control" id="txtCobros" name="txtCobros" value="' . $msCodigo . '" readonly />'); 
								?>	
							</div>
						</div>

						<div class = "form-group row">
							<label for="txtDescripcion" class="col-sm-12 col-md-3 form-label">Descripcion</label>
							<div class="col-sm-12 col-md-9">
								<?php echo('<input type="text" class="form-control" id="txtDescripcion" name="txtDescripcion" value="' . $msDescripcion . '" />'); ?>
							</div>
						</div>
	
						<div class="form-group row">
							<label for="optTipoCobro" class="col-sm-auto col-md-3 form-label">Tipo de cobro</label>
							<div class="col-sm-12 col-md-7">
								<div class="radio">
									<?php
									if ($mnTipoCobro == 0 )
										echo('<input type="radio" id="optTipoCobro1" name="optTipoCobro" value="0" checked/>Matricula &nbsp;');
									else
										echo('<input type="radio" id="optTipoCobro1" name="optTipoCobro" value="0" />Matricula &nbsp;');

									if ($mnTipoCobro == 1)
										echo('<input type="radio" id="optTipoCobro2" name="optTipoCobro" value="1" checked/>Mensualidad &nbsp;');
									else
										echo(' <input type="radio" id="optTipoCobro2" name="optTipoCobro" value="1" />Mensualidad &nbsp;');

									if ($mnTipoCobro == 2)
										echo('<input type="radio" id="optTipoCobro3" name="optTipoCobro" value="2" checked/>Mora &nbsp;');
									else
										echo(' <input type="radio" id="optTipoCobro3" name="optTipoCobro" value="2" />Mora &nbsp;');
									if ($mnTipoCobro == 3)
										echo('<input type="radio" id="optTipoCobro4" name="optTipoCobro" value="3" checked/>Servicios academicos');
									else
										echo(' <input type="radio" id="optTipoCobro4" name="optTipoCobro" value="3" />Servicios academicos');
									?>
								</div>
							</div>
						</div>

						<div class="form-group row">
							<label for="optTipoEstudio" class="col-sm-auto col-md-3 form-label">Tipo de estudio</label>
							<div class="col-sm-12 col-md-9">
								<div class="radio">
									<?php
									if ($mnTipoEstudio == 0 )
										echo('<input type="radio" id="optTipoEstudio1" name="optTipoEstudio" value="0" checked/>Grado regular&nbsp;');
									else
										echo('<input type="radio" id="optTipoEstudio1" name="optTipoEstudio" value="0" />Grado regular&nbsp;');

									if ($mnTipoEstudio == 1)
										echo('<input type="radio" id="optTipoEstudio2" name="optTipoEstudio" value="1" checked/>Grado sabatino&nbsp;');
									else
										echo(' <input type="radio" id="optTipoEstudio2" name="optTipoEstudio" value="1" />Grado sabatino&nbsp;');

									if ($mnTipoEstudio == 2)
										echo('<input type="radio" id="optTipoEstudio3" name="optTipoEstudio" value="2" checked/>Posgrado&nbsp;');
									else
										echo(' <input type="radio" id="optTipoEstudio3" name="optTipoEstudio" value="2" />Posgrado&nbsp;');
									if ($mnTipoEstudio == 3)
										echo('<input type="radio" id="optTipoEstudio4" name="optTipoEstudio" value="3" checked/>Curso libre');
									else
										echo(' <input type="radio" id="optTipoEstudio4" name="optTipoEstudio" value="3" />Curso libre');
									?>
								</div>
							</div>
						</div>
	
						<div class="form-group row" id="moraSection" style="display:none;">
							<label for="cboMora" class="col-sm-12 col-md-3 col-form-label">Asignar Mora a un Cobro</label>
							<div class="col-sm-12 col-md-9">
								<select class="form-control" id="cboMora" name="cboMora">
									<option value="">Seleccione un valor</option>
									<?php 
										$msConsulta = "select COBRO_REL, DESC_130 from UMO130A where ACTIVO_130 = 1 and TIPOCOBRO_130 = 1 and TIPOESTUDIO_130 = ? order by COBRO_REL;";
										$mDatos = $m_cnx_MySQL->prepare($msConsulta);
										$mDatos->execute([$mnTipoEstudio]); 
										while ($mFila = $mDatos->fetch()) 
										{
											$msValor = trim($mFila["COBRO_REL"]);
											$msTexto = trim($mFila["DESC_130"]);
											$selected = ($msMora == $msValor) ? 'selected' : '';
											echo "<option value='$msValor' $selected>$msTexto</option>";
										}
									?>
								</select>
							</div>
						</div>
		
						<div class = "form-group row">
							<label for="txtValor" class="col-sm-12 col-md-3 form-label">Valor</label>
							<div class="col-sm-12 col-md-3">
								<?php echo('<input type="number" style="text-align: right;" class="form-control" id="txtValor" name="txtValor" step="0.01" placeholder="0.00" value="' . $mnValor . '" />'); ?>
							</div>
						</div>
		
						<div class="form-group row">
							<label for="optMoneda" class="col-sm-auto col-md-3 form-label">Moneda</label>
							<div class="col-sm-11 col-md-3">
								<div class="radio">
										<?php
										if ($mnMoneda == 1)
											echo('<input type="radio" id="optMoneda" name="optMoneda" value="0"/>Córdobas &nbsp
											<input type="radio" id="optMoneda" name="optMoneda" value="1" checked/>Dólares');
										else
											echo('<input type="radio" id="optMoneda" name="optMoneda" value="0" checked/>Córdobas &nbsp
											<input type="radio" id="optMoneda" name="optMoneda" value="1" />Dólares');
										?>
								</div>
							</div>
						</div>
						
						<div class = "form-group row">
							<label for="dtpFechaVenc" class="col-sm-12 col-md-3 form-label">Fecha de vencimiento</label>
							<div class="col-sm-12 col-md-3">
								<?php echo('<input type="date" class="form-control" id="dtpFechaVenc" name="dtpFechaVenc" value="' . $msFechaVenc . '" />'); ?>
							</div>
						</div>
		
						<div class="form-group row">
							<label for="optActivo" class="col-sm-auto col-md-3 form-label">Activo</label>
							<div class="col-sm-12 col-md-3">
								<div class="radio">
									<?php
										if ($mbActivo == 1)
											echo('<input type="radio" id="optActivo1" name="optActivo" value="0" /> No &nbsp <input type="radio" id="optActivo2" name="optActivo" value="1" checked/> Si &nbsp');
										else
											echo('<input type="radio" id="optActivo1" name="optActivo" value="0" checked/> No  &nbsp <input type="radio" id="optActivo2" name="optActivo" value="1" /> Si &nbsp');
									?>
								</div>
							</div>
						</div>

						<div class = "row">
							<div class="col-auto offset-sm-0 col-md-12 offset-md-3">
								<input type="submit" id="Guardar" name="Guardar" value="Guardar" class="btn btn-primary"/>
								<input type="button" id="Cancelar" name="Cancelar" value="Cancelar" class="btn btn-primary" onclick="location.href='gridCobros.php';"/>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</body>
</html>
<script>
document.addEventListener("DOMContentLoaded", function () {
    function moraVista()
	{
        const tipoCobroRadio = document.querySelector('input[name="optTipoCobro"]:checked');
        if (!tipoCobroRadio) return; // No hay radio seleccionado aún
        const tipoCobro = tipoCobroRadio.value;

        const moraSection = document.getElementById('moraSection');
        moraSection.style.display = (tipoCobro === "2") ? "flex" : "none";
    }

	function comboMora()
	{
		var datos = new FormData();
		var tipoEstudioRadio = document.querySelector('input[name="optTipoEstudio"]:checked');
		var tipoEstudio = tipoEstudioRadio.value;
		datos.append('tipoEstudio', tipoEstudio);

		$.ajax({
			url: 'funciones/fxDatosCobros.php',
			type: 'post',
			data: datos,
			contentType: false,
			processData: false,
			success: function(response){
				document.getElementById('cboMora').innerHTML = response;
			}
		})
	}

	document.querySelectorAll("input[name='optTipoCobro']").forEach(radio => {
        radio.addEventListener("change", moraVista);
    });

	document.querySelectorAll("input[name='optTipoEstudio']").forEach(radio => {
        radio.addEventListener("change", comboMora);
    });

    window.verificarFormulario = function() {
        if(document.getElementById('txtDescripcion').value.trim() === "") {
            document.getElementById('txtDescripcion').focus();
            $.messager.alert('UMOJN','Falta la descripcion.','warning');
            return false;
        }
        if (document.getElementById('txtValor').value.trim() === "") {
            document.getElementById('txtValor').focus();
            $.messager.alert('UMOJN','Falta la cantidad.','warning');
            return false;
        }
        return true;
    }
});
</script>