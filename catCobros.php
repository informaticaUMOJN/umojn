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
<?php }
	else
	{
		$mbAdministrador = fxVerificaAdministrador();
		$mbPermisoUsuario = fxPermisoUsuario("catCobros");
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
			if (isset($_POST["txtCobros"]))
			{
				$msCodigo = $_POST["txtCobros"];
				$msCarrera = null;
				$msCurso = null;
				if ($_POST["optCarreraCurso"] == "carrera") 
					{
						$msCarrera = $_POST["lstCarrera"];
					} else if 
					($_POST["optCarreraCurso"] == "curso") 
					{
						$msCurso = $_POST["lstCarrera"];
					}
					$msDescripcion = $_POST["txtDescripcion"];	$mnTipo = $_POST["optTipo"];
					$mnModalidad = $_POST["optModalidad"];		$mnRegimen = $_POST["optRegimen"];
					$mnTurno = $_POST["optTurno"];				$msMora = $_POST["lstMora"];
					$mfValor = $_POST["txtValor"];				$mnMoneda = $_POST["optMoneda"];
					$msFechaVenc = $_POST["dtpFechaVenc"];		$mbActivo = $_POST["optActivo"];
					
					if (isset($_POST["gridDocumentos"]))
						$gridDocumentos = $_POST["gridDocumentos"];
					if ($msCodigo == "")
						{
							$msCodigo = fxGuardarCobros ( $msCarrera, $msCurso, $msDescripcion, $mnTipo, $msMora, $mnModalidad,$mnRegimen ,$mnTurno,  $mfValor, $mnMoneda, $msFechaVenc, $mbActivo);
							$msBitacora = $msCodigo . "; " . $msCarrera . "; " .$msCurso. "; " . $msDescripcion . "; " . $mnTipo . "; " .$mnModalidad.";". $mnTurno . ";".$msMora.";" .$mnRegimen .";". $mfValor . "; " . $mnMoneda . ";" . $msFechaVenc . "; " . "; " . $mbActivo;
							fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO130A", $msCodigo, "", "Agregar", $msBitacora);
						}
						else
							{
								fxModificarCobros ($msCodigo, $msCarrera, $msCurso, $msDescripcion, $mnTipo, $msMora,$mnModalidad, $mnTurno, $mnRegimen, $mfValor, $mnMoneda, $msFechaVenc, $mbActivo);
								$msBitacora = $msCodigo . "; " . $msCarrera . "; " .$msCurso. "; " . $msDescripcion . "; " . $mnTipo . "; ".$mnModalidad.";" . $mnTurno .";". $msMora.";" .$mnRegimen .";". $mfValor . "; " . $mnMoneda . ";" . $msFechaVenc . "; " . "; " . $mbActivo;
								fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO130A", $msCodigo, "", "Modificar", $msBitacora);
							}
							?><meta http-equiv="Refresh" content="0;url=gridCobros.php"/><?php
						}
						else
							{
								if (isset($_POST["UMOJN"]))
									$msCodigo = $_POST["UMOJN"];
								else
									$msCodigo = "";
								if ($msCodigo != "")
									{
										$objRecordSet = fxDevuelveCobros(0, $msCodigo);
										$mFila = $objRecordSet->fetch();
										$msCarrera = $mFila["CARRERA_REL"];	$msCurso =$mFila["CURSOS_REL"];
										$msDescripcion = $mFila["DESC_130"];$mnTipo = $mFila["TIPO_130"];
										$mnModalidad = $mFila["MODALIDAD_130"];$mnRegimen = $mFila["REGIMEN_130"];
										$mnTurno = $mFila["TURNO_130"];$msMora = $mFila["UMO_COBRO_REL"];
										$mfValor = $mFila["VALOR_130"];$mnMoneda = $mFila["MONEDA_130"];
										$msFechaVenc = $mFila["VENCIMIENTO_130"];$mbActivo = $mFila ["ACTIVO_130"];
									}
									else
										{
											$msCarrera ="";$msCurso = "";$msDescripcion = "";$mnTipo = "0";
											$mnModalidad ="1";$mnRegimen = "1";$mnTurno = "1";$msMora = "";
											$mfValor= "";$mnMoneda ="";$msFechaVenc = date('Y-m-d');$mbActivo = 0; 	
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
									<div class="col-sm-13 offset-sm-0 col-md-9 offset-md-2">
										<form id="catCobros" name="catCobros" action="catCobros.php" onsubmit="return verificarFormulario()" method="post">
											<div class = "form-group row">
												<label for="txtCobros" class="col-sm-12 col-md-2 form-label">Codigo de cobro</label>
												<div class="col-sm-12 col-md-3">
													<?php
													echo('<input type="text" class="form-control" id="txtCobros" name="txtCobros" value="' . $msCodigo . '" readonly />'); 
													?>	
												</div>
											</div>
											<div class="form-group row">
												<label for="optCarreraCurso" class="col-sm-auto col-md-2 form-label">Cobro a</label>
												<div class="col-sm-12 col-md-7">
													<div class="radio">
														<?php
														if ($msCarrera  == 0 )
															echo('<input type="radio" id="optCarreraCurso" name="optCarreraCurso" value="carrera" checked/>Carrera de grado &nbsp');
														else
															echo('<input type="radio" id="optCarreraCurso" name="optCarreraCurso" value="carrera" />Carrera de grado &nbsp');

														if ($msCurso == 1)
															echo('<input type="radio" id="optCarreraCurso" name="optCarreraCurso" value="curso"/>Cursos Libres');
														else
															echo(' <input type="radio" id="optCarreraCurso" name="optCarreraCurso" value="curso"  checked/>Cursos Libres &nbsp ');
														?>
													</div>
												</div>
											</div>
											<div class="form-group row">
												<label for="lstDestinoCobro" class="col-sm-12 col-md-2 col-form-label">Carrera / Curso</label>
												<div class="col-sm-12 col-md-7">
													<select class="form-control" id="lstDestinoCobro" name="lstCarrera">
														<?php
														$msConsulta = "select CARRERA_REL, NOMBRE_040 from UMO040A order by NOMBRE_040";
														$mDatos = $m_cnx_MySQL->prepare($msConsulta);
														$mDatos->execute();
														while ($mFila = $mDatos->fetch()) {
														$msValor = rtrim($mFila["CARRERA_REL"]);
														$msTexto = htmlspecialchars(rtrim($mFila["NOMBRE_040"]));
														if ($msCarrera == ""){
															echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
															$msCarrera = $msValor;
														}else {
															if ($msCarrera == $msValor)
																echo("<option value='" . $msValor . "' selected>" . $msTexto . "</option>");
															else
																echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
															}
															}
														?>
													</select>
												</div>
											</div>

											<div class = "form-group row">
												<label for="txtDescripcion" class="col-sm-12 col-md-2 form-label">Descripcion</label>
												<div class="col-sm-12 col-md-7">
													<?php echo('<input type="text" class="form-control" id="txtDescripcion" name="txtDescripcion" value="' . $msDescripcion . '" />'); ?>
												</div>
											</div>
						
											<div class="form-group row">
												<label for="optTipo" class="col-sm-auto col-md-2 form-label">Tipo</label>
												<div class="col-sm-12 col-md-7">
													<div class="radio">
														<?php
														if ($mnTipo == 0 )
															echo('<input type="radio" id="optTipo" name="optTipo" value="0" checked/>Matricula &nbsp');
														else
															echo('<input type="radio" id="optTipo" name="optTipo" value="0" />Matricula &nbsp');

														if ($mnTipo == 1)
															echo('<input type="radio" id="optTipo" name="optTipo" value="1"  checked/>Mensualidad');
														else
															echo(' <input type="radio" id="optTipo" name="optTipo" value="1"  />Mensualidad &nbsp ');

														if ($mnTipo == 2)
															echo('<input type="radio" id="optTipo" name="optTipo" value="2"  checked/>Mora');
														else
															echo(' <input type="radio" id="optTipo" name="optTipo" value="2"  />Mora &nbsp ');
														if ($mnTipo == 3)
															echo('<input type="radio" id="optTipo" name="optTipo" value="3"  checked/>Servicios academicos');
														else
															echo(' <input type="radio" id="optTipo" name="optTipo" value="3"  />Servicios academicos &nbsp ');
														?>
													</div>
												</div>
                        					</div>
						
											<div class="form-group row" id="moraSection" style="display:none;">
												<label for="lstMora" class="col-sm-12 col-md-2 col-form-label">Asignar Mora a un Cobro</label>
												<div class="col-sm-12 col-md-7">
													<select class="form-control" id="lstMora" name="lstMora">
														<option value=""></option> 
														<?php 
															if ($_POST["optCarreraCurso"] == "curso") 
																	{
																	$msConsulta = "select u.COBRO_REL, u.DESC_130 as DESCRIPCION from UMO130A u where u.CURSOS_REL = ?
											 						and u.ACTIVO_130 = 1 and u.TIPO_130 not in (0, 2) order by u.COBRO_REL;";
																	$param = $msCurso;
																	} else {
											 						$msConsulta = "select u.COBRO_REL, u.DESC_130 as DESCRIPCION from UMO130A u where u.CARRERA_REL = ? 
											  						and u.ACTIVO_130 = 1 and u.TIPO_130 not in (0, 2) order by u.COBRO_REL;";
											  						$param = $msCarrera; 
											 						}
											 						$mDatos = $m_cnx_MySQL->prepare($msConsulta);
											 						$mDatos->execute([$param]); 
											 						while ($mFila = $mDatos->fetch()) {
																	$msValor = trim($mFila["COBRO_REL"]);
																	$msTexto = trim($mFila["DESCRIPCION"]);
																	$selected = ($msMora == $msValor) ? 'selected' : '';
																	echo "<option value='$msValor' $selected>$msTexto</option>";
																	}
														?>
													</select>
												</div>
											</div>
							
											<div class="form-group row">
												<label for="optModalidad" class="col-sm-auto col-md-2 form-label">Modalidad</label>
												<div class="col-sm-12 col-md-8">
													<div class="radio">
														<?php
															if($mnModalidad==1) 
																echo('<input type="radio" id="optModalidad1" name="optModalidad" value="1" checked /> Presencial');
															else
																echo('<input type="radio" id="optModalidad1" name="optModalidad" value="1" /> Presencial');

															if($mnModalidad==2)
																echo('&emsp;<input type="radio" id="optModalidad2" name="optModalidad" value="2" checked /> Por encuentro');
															else
																echo('&emsp;<input type="radio" id="optModalidad2" name="optModalidad" value="2" /> Por encuentro');

															if($mnModalidad==3)
																echo('&emsp;<input type="radio" id="optModalidad3" name="optModalidad" value="3" checked /> Virtual');
															else
																echo('&emsp;<input type="radio" id="optModalidad3" name="optModalidad" value="3" /> Virtual');

															if($mnModalidad==4)
																echo('&emsp;<input type="radio" id="optModalidad4" name="optModalidad" value="4" checked /> Mixta');
															else
																echo('&emsp;<input type="radio" id="optModalidad4" name="optModalidad" value="4" /> Mixta');
														?>
													</div>
												</div>
											</div>
							
											<div class="form-group row">
												<label for="optRegimen" class="col-sm-auto col-md-2 form-label">Regimen</label>
												<div class="col-sm-12 col-md-10">
													<div class="radio">
														<?php
															if ($mnRegimen == 1)
																echo('<input type="radio" id="optRegimen" name="optRegimen" value="1" checked/>Mensualidad &nbsp');
															else
																echo('<input type="radio" id="optRegimen" name="optRegimen" value="1" />Mensualidad &nbsp');

															if ($mnRegimen == 2)
																echo('<input type="radio" id="optRegimen" name="optRegimen" value="2"  checked/>Bimestral');
															else
																echo(' <input type="radio" id="optRegimen" name="optRegimen" value="2"  />Bimestral &nbsp ');

															if ($mnRegimen == 3)
																echo('<input type="radio" id="optRegimen" name="optRegimen" value="3"  checked/>Trimestral');
															else
																echo(' <input type="radio" id="optRegimen" name="optRegimen" value="3"  />Trimestral &nbsp ');

															if ($mnRegimen == 4)
																echo('<input type="radio" id="optRegimen" name="optRegimen" value="4"  checked/>Cuatrimestral');
															else
																echo(' <input type="radio" id="optRegimen" name="optRegimen" value="4"  />Cuatrimestral &nbsp ');

															if ($mnRegimen == 5)
																echo('<input type="radio" id="optRegimen" name="optRegimen" value="5"  checked/>Semestral');
															else
																echo(' <input type="radio" id="optRegimen" name="optRegimen" value="5"  />Semestral &nbsp ');

															if ($mnRegimen == 6)
																echo('<input type="radio" id="optRegimen" name="optRegimen" value="6"  checked/>Intensivo');
															else
																echo(' <input type="radio" id="optRegimen" name="optRegimen" value="6"  />Intensivo &nbsp ');
														?>	
													</div>
												</div>
											</div>
						
											<div class="form-group row">
												<label for="optTurno" class="col-sm-auto col-md-2 form-label">Turno</label>
												<div class="col-sm-12 col-md-10">
													<div class="radio">
														<?php
															if ($mnTurno == 1)
																echo('<input type="radio" id="optTurno" name="optTurno" value="1" checked/>Diurno &nbsp');
															else
																echo('<input type="radio" id="optTurno" name="optTurno" value="1" />Diurno &nbsp');

															if ($mnTurno == 2)
																echo('<input type="radio" id="optTurno" name="optTurno" value="2"  checked/>Matutino');
															else
																echo(' <input type="radio" id="optTurno" name="optTurno" value="2"  />Matutino &nbsp ');

															if ($mnTurno == 3)
																echo('<input type="radio" id="optTurno" name="optTurno" value="3"  checked/>Vespertino');
															else
																echo(' <input type="radio" id="optTurno" name="optTurno" value="3"  />Vespertino &nbsp ');

															if ($mnTurno == 4)
																echo('<input type="radio" id="optTurno" name="optTurno" value="4"  checked/>Nocturno');
															else
																echo(' <input type="radio" id="optTurno" name="optTurno" value="4"  />Nocturno &nbsp ');

															if ($mnTurno == 5)
																echo('<input type="radio" id="optTurno" name="optTurno" value="5"  checked/>Sabatino');
															else
																echo(' <input type="radio" id="optTurno" name="optTurno" value="5"  />Sabatino &nbsp ');

															if ($mnTurno == 6)
																echo('<input type="radio" id="optTurno" name="optTurno" value="6"  checked/>Dominical ');
															else
																echo(' <input type="radio" id="optTurno" name="optTurno" value="6"  />Dominical  &nbsp ');
														?>	
													</div>
												</div>
											</div>
							
											<div class = "form-group row">
												<label for="txtValor" class="col-sm-12 col-md-2 form-label">Valor</label>
												<div class="col-sm-12 col-md-3">
													<?php echo('<input type="text" class="form-control" id="txtValor" name="txtValor" value="' . $mfValor . '" />'); ?>
												</div>
											</div>
							
											<div class="form-group row">
												<label for="optMoneda" class="col-sm-auto col-md-2 form-label">Moneda</label>
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
												<label for="dtpFechaVenc" class="col-sm-12 col-md-2 form-label">Fecha</label>
												<div class="col-sm-12 col-md-3">
													<?php echo('<input type="date" class="form-control" id="dtpFechaVenc" name="dtpFechaVenc" value="' . $msFechaVenc . '" />'); ?>
												</div>
											</div>
							
											<div class="form-group row">
												<label for="optActivo" class="col-sm-auto col-md-2 form-label">Activo</label>
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
						<?php
						}
	}
}
?>
</body>
</html>
<script>
document.addEventListener("DOMContentLoaded", function () {
    function moraVista() {
        const tipoCobroRadio = document.querySelector('input[name="optTipo"]:checked');
        if (!tipoCobroRadio) return; // No hay radio seleccionado aún
        const tipoCobro = tipoCobroRadio.value;

        const moraSection = document.getElementById('moraSection');
        moraSection.style.display = (tipoCobro === "2") ? "flex" : "none";
    }
    moraVista();
    document.querySelectorAll("input[name='optTipo']").forEach(radio => {
        radio.addEventListener("change", moraVista);
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
    function actualizarSelectDestino(tipo) {
        $.ajax({
            type: "POST",
            url: "funciones/fxDatosCarrerasOCursos.php",
            data: { tipo: tipo },
            success: function (response) {
                const lstDestino = document.getElementById("lstDestinoCobro");
                if (lstDestino) lstDestino.innerHTML = response;

                const primerValor = lstDestino ? lstDestino.value : null;
                if (primerValor) {
                    llenarCobros(primerValor, tipo);
                } else {
                    $("#lstMora").html("<option value=''>Seleccione un valor</option>");
                }
            },
            error: function () {
                alert("Error al cargar datos.");
            }
        });
    }
	
    function llenarCobros(valor, tipo) {
        $.ajax({
            type: "POST",
            url: "funciones/fxDatosC.php",
            data: { carrera: valor, tipo: tipo },
            success: function(response) {
                $("#lstMora").html(response);
            },
            error: function() {
                alert("Error al cargar los cobros.");
            }
        });
    }
    const tipoRadioSeleccionado = document.querySelector('input[name="optCarreraCurso"]:checked');
    const tipoSeleccionado = tipoRadioSeleccionado ? tipoRadioSeleccionado.value : 'carrera';
    actualizarSelectDestino(tipoSeleccionado);
    document.querySelectorAll('input[name="optCarreraCurso"]').forEach(radio => {
        radio.addEventListener("change", function () {
            actualizarSelectDestino(this.value);
        });
    });
    const lstDestino = document.getElementById("lstDestinoCobro");
    if (lstDestino) {
        lstDestino.addEventListener("change", function() {
            const tipoActualRadio = document.querySelector('input[name="optCarreraCurso"]:checked');
            const tipoActual = tipoActualRadio ? tipoActualRadio.value : 'carrera';
            const valorSeleccionado = this.value;
            if (valorSeleccionado) {
                llenarCobros(valorSeleccionado, tipoActual);
            } else {
                $("#lstMora").html("<option value=''>Seleccione un valor</option>");
            }
        });
    }
});
</script>