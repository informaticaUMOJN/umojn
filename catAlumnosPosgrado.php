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
	require_once ("funciones/fxEstudiantesPosgrado.php");

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
		$mbPermisoUsuario = fxPermisoUsuario("catAlumnosPosgrado");
		
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
			if (isset($_POST["txtEstudiante"]))
			{
				$msCodigo = $_POST["txtEstudiante"];
				$msCarrera = $_POST["cboCarrera"];
				$msUniversidad = $_POST["cboUniversidad"];
				$msMunicipio = $_POST["cboMunicipio"];
				//$msUsuario = $_POST["txtUsuario"];
				$msUsuario = "";
				$mdFecha = $_POST["dtpFecha"];
				$msNombre1 = $_POST["txtNombre1"];
				$msNombre2 = $_POST["txtNombre2"];
				$msApellido1 = $_POST["txtApellido1"];
				$msApellido2 = $_POST["txtApellido2"];
				$mdFechaNac = $_POST["dtpFechaNac"];
				$msNacionalidad = $_POST["txtNacionalidad"];
				$mnAnnoAcademico = $_POST["txnAnnoAcademico"];
				$msGradoAcademico = $_POST["txtGradoAcademico"];
				$msCarnet = $_POST["txtCarnet"];
				$mnPeso = $_POST["txnPeso"];
				$mnTalla = $_POST["txnTalla"];
				$msTipoSangre = $_POST["cboTipoSangre"];
				$msCedula = $_POST["txtCedula"];
				$msSexo = $_POST["optSexo"];
				$mnEstadoCivil = $_POST["cboEstadoCivil"];
				$mnHijos = $_POST["txnHijos"];
				$msTelefono = $_POST["txtTelefono"];
				$msCelular = $_POST["txtCelular"];
				$msCorreoE = $_POST["txtCorreoE"];
				$msCorreoI = $_POST["txtCorreoI"];
				$msDireccion = $_POST["txtDireccion"];
				$mnMedio = $_POST["cboMedio"];
				$msEmergencia = $_POST["txtEmergencia"];
				$msTelEmergencia = $_POST["txtTelEmergencia"];
				$msCelEmergencia = $_POST["txtCelEmergencia"];
				$mbLaboral = $_POST["optLaboral"];
				$msOcupacion = $_POST["txtOcupacion"];
				$mnIngresoMensual = $_POST["txnIngreso"];
				$msCentroTrabajo = $_POST["txtCentroTrabajo"];
				$msDireccionTrabajo = $_POST["txtDireccionTrabajo"];
				$mbOtroIdioma = $_POST["optOtroIdioma"];
				$msIdioma = $_POST["txtIdioma"];

				try {
					if ($msCodigo == "") {
						$msCodigo = fxGuardarEstudiantePos($msCarrera, $msUniversidad, $msMunicipio, $msUsuario, $mdFecha, $mnAnnoAcademico, $msCarnet,
						$msNombre1, $msNombre2, $msApellido1, $msApellido2, $msGradoAcademico, $mdFechaNac, $msNacionalidad, $mnPeso, $mnTalla, $msTipoSangre, $msCedula,
						$msSexo, $mnEstadoCivil, $mnHijos, $msTelefono, $msCelular, $msCorreoE, $msCorreoI, $msDireccion, $mnMedio, $msEmergencia,
						$msTelEmergencia, $msCelEmergencia, $mbLaboral, $msOcupacion, $mnIngresoMensual, $msCentroTrabajo, $msDireccionTrabajo,
						$mbOtroIdioma, $msIdioma);
						$msBitacora = $msCodigo . "; " . $msUniversidad . "; " . $msMunicipio . "; " . $msUsuario . "; " . $mdFecha . "; " . $mnAnnoAcademico . "; " . $msCarnet . "; " . $msNombre1 . "; " . $msNombre2 . "; " . $msApellido1 . "; " . $msApellido2 . "; " . $msGradoAcademico . "; " . $mdFechaNac . "; " . $msNacionalidad . "; " . $mnPeso . "; " . $mnTalla . "; " . $msTipoSangre . "; " . $msCedula . "; " . $msSexo . "; " . $mnEstadoCivil . "; " . $mnHijos . "; " . $msTelefono . "; " . $msCelular . "; " . $msCorreoE . "; " . $msCorreoI . "; " . $msDireccion . "; " . $mnMedio . "; " . $msEmergencia . "; " . $msTelEmergencia . "; " . $msCelEmergencia . "; " . $mbLaboral . "; " . $msOcupacion . "; " . $mnIngresoMensual . "; " . $msCentroTrabajo . "; " . $msDireccionTrabajo . "; " . $mbOtroIdioma . "; " . $msIdioma;

						fxAgregarBitacora($_SESSION["gsUsuario"], "UMO250A", $msCodigo, "", "Agregar", $msBitacora);
						echo json_encode(["status"=>"ok","msg"=>"Alumno agregado","codigo"=>$msCodigo]);
					} else {
						fxModificarEstudiantePos($msCodigo, $msCarrera, $msUniversidad, $msMunicipio, $msUsuario, $mdFecha, $mnAnnoAcademico, $msCarnet,
						$msNombre1, $msNombre2, $msApellido1, $msApellido2, $msGradoAcademico, $mdFechaNac, $msNacionalidad, $mnPeso, $mnTalla, $msTipoSangre, $msCedula,
						$msSexo, $mnEstadoCivil, $mnHijos, $msTelefono, $msCelular, $msCorreoE, $msCorreoI, $msDireccion, $mnMedio, $msEmergencia,
						$msTelEmergencia, $msCelEmergencia, $mbLaboral, $msOcupacion, $mnIngresoMensual, $msCentroTrabajo, $msDireccionTrabajo,
						$mbOtroIdioma, $msIdioma);
						$msBitacora = $msCodigo . "; " . $msUniversidad . "; " . $msMunicipio . "; " . $msUsuario . "; " . $mdFecha . "; " . $mnAnnoAcademico . "; " . $msCarnet . "; " . $msNombre1 . "; " . $msNombre2 . "; " . $msApellido1 . "; " . $msApellido2 . "; " . $msGradoAcademico . "; " . $mdFechaNac . "; " . $msNacionalidad . "; " . $mnPeso . "; " . $mnTalla . "; " . $msTipoSangre . "; " . $msCedula . "; " . $msSexo . "; " . $mnEstadoCivil . "; " . $mnHijos . "; " . $msTelefono . "; " . $msCelular . "; " . $msCorreoE . "; " . $msCorreoI . "; " . $msDireccion . "; " . $mnMedio . "; " . $msEmergencia . "; " . $msTelEmergencia . "; " . $msCelEmergencia . "; " . $mbLaboral . "; " . $msOcupacion . "; " . $mnIngresoMensual . "; " . $msCentroTrabajo . "; " . $msDireccionTrabajo . "; " . $mbOtroIdioma . "; " . $msIdioma;

						echo json_encode(["status"=>"ok","msg"=>"Alumno actualizado","codigo"=>$msCodigo]);
						fxAgregarBitacora($_SESSION["gsUsuario"], "UMO250A", $msCodigo, "", "Modificar", $msBitacora);
					}
				} catch (Exception $e) {
					echo json_encode(["status"=>"error","msg"=>$e->getMessage()]);
				}
				exit;
				
			}
			else
			{
				if (isset($_POST["UMOJN"]))
					$msCodigo = $_POST["UMOJN"];
				else
					$msCodigo = "";

				if ($msCodigo != "")
				{
					$objRecordSet = fxDevuelveEstudiantePos(0, $msCodigo);
					$mFila = $objRecordSet->fetch();
					$msCarrera = $mFila["CARRERA_REL"];
					$msUniversidad = $mFila["UNIVERSIDAD_REL"];
					$msMunicipio = $mFila["MUNICIPIO_REL"];
					$msUsuario = $mFila["USUARIO_REL"];
					$mdFecha = $mFila["FECHA_250"];
					$msNombre1 = $mFila["NOMBRE1_250"];
					$msNombre2 = $mFila["NOMBRE2_250"];
					$msApellido1 = $mFila["APELLIDO1_250"];
					$msApellido2 = $mFila["APELLIDO2_250"];
					$mdFechaNac = $mFila["FECHANAC_250"];
					$msNacionalidad = $mFila["NACIONALIDAD_250"];
					$msGradoAcademico = $mFila["GRADOACADEMICO_250"];
					$mnAnnoAcademico = $mFila["ANNOACADEMICO_250"];
					$msCarnet = $mFila["CARNET_250"];
					$mnPeso = $mFila["PESO_250"];
					$mnTalla = $mFila["TALLA_250"];
					$msTipoSangre = $mFila["TIPOSANGRE_250"];
					$msCedula = $mFila["CEDULA_250"];
					$msSexo = $mFila["SEXO_250"];
					$mnEstadoCivil = $mFila["ESTADOCIVIL_250"];
					$mnHijos = $mFila["HIJOS_250"];
					$msTelefono = $mFila["TELEFONO_250"];
					$msCelular = $mFila["CELULAR_250"];
					$msCorreoE = $mFila["CORREOE_250"];
					$msCorreoI = $mFila["CORREOI_250"];
					$msDireccion = $mFila["DIRECCION_250"];
					$mnMedio = $mFila["MEDIO_250"];
					$msEmergencia = $mFila["EMERGENCIA_250"];
					$msTelEmergencia = $mFila["TEL_EMERGENCIA_250"];
					$msCelEmergencia = $mFila["CEL_EMERGENCIA_250"];
					$mbLaboral = $mFila["CONDICIONLABORAL_250"];
					$msOcupacion = $mFila["OCUPACION_250"];
					$mnIngresoMensual = $mFila["INGRESOMENSUAL_250"];
					$msCentroTrabajo = $mFila["CENTROTRABAJO_250"];
					$msDireccionTrabajo = $mFila["DIRECCIONTRABAJO_250"];
					$mbOtroIdioma = $mFila["OTROIDIOMA_250"];
					$msIdioma = $mFila["IDIOMA_250"];
				}
				else
				{
					$msCarrera = "";
					$msUniversidad = "";
					$msMunicipio = "";
					$msUsuario = "";
					$mdFecha = date('Y-m-d');
					$msNombre1 = "";
					$msNombre2 = "";
					$msApellido1 = "";
					$msApellido2 = "";
					$mdFechaNac = date('Y-m-d');
					$msNacionalidad = "Nicaragüense";
					$msGradoAcademico = "";
					$mnAnnoAcademico = date('Y');
					$msCarnet = "";
					$mnPeso = 0;
					$mnTalla = 0;
					$msTipoSangre = "N/A";
					$msCedula = "";
					$msSexo = "M";
					$mnEstadoCivil = "";
					$mnHijos = 0;
					$msTelefono = "";
					$msCelular = "";
					$msCorreoE = "";
					$msCorreoI = "";
					$msDireccion = "";
					$mnMedio = 0;
					$msEmergencia = "";
					$msTelEmergencia = "";
					$msCelEmergencia = "";
					$mbLaboral = 0;
					$msOcupacion = "";
					$mnIngresoMensual = 0;
					$msCentroTrabajo = "";
					$msDireccionTrabajo = "";
					$mbOtroIdioma = 0;
					$msIdioma = "";
				}
?>
    <div class="container text-left">
    	<div id="DivContenido">
			<div class = "row">
                <div class="col-xs-12 col-md-12">
					<form name="catEstudiantes" id="catEstudiantes">
						<div class = "row">
							<div class="col-auto col-md-11">
								<input type="submit" id="Guardar" name="Guardar" value="Guardar" class="btn btn-primary"/>
								<input type="button" id="Cancelar" name="Cancelar" value="Cancelar" class="btn btn-primary" onclick="location.href='gridAlumnosPosgrado.php';"/>
							</div>
						</div>

						<div class="easyui-tabs tabs-narrow" style="width:100%;height:auto">
							<!--Inicio del DIV de Tabs-->
							<div title="Generales" style="padding-left: 20px; padding-top: 10px">
								<div class="col-sm-auto col-md-12">
									<div class = "form-group row">
										<label for="txtEstudiante" class="col-sm-12 col-md-3 form-label">Estudiante</label>
										<div class="col-sm-12 col-md-3">
										<?php
											echo('<input type="text" class="form-control" id="txtEstudiante" name="txtEstudiante" value="' . $msCodigo . '" readonly />'); 
										?>
										</div>
									</div>

									<div class = "form-group row">
										<label for="dtpFecha" class="col-sm-12 col-md-3 form-label">Fecha de inscripción</label>
										<div class="col-sm-12 col-md-2">
										<?php echo('<input type="date" class="form-control" id="dtpFecha" name="dtpFecha" value="' . $mdFecha . '" />'); ?>
										</div>
									</div>

									<div class = "form-group row">
										<label for="txnAnnoAcademico" class="col-sm-12 col-md-3 form-label">Año de ingreso</label>
										<div class="col-sm-12 col-md-2">
										<?php 
											echo('<input type="number" class="form-control" id="txnAnnoAcademico" name="txnAnnoAcademico" value="' . $mnAnnoAcademico . '" />');
										?>
										</div>
									</div>
							
									<div class="form-group row">
										<label for="cboCarrera" class="col-sm-12 col-md-3 col-form-label">Carrera</label>
										<div class="col-sm-12 col-md-7">
											<select class="form-control" id="cboCarrera" name="cboCarrera" onchange="obtenerCarnet()">
												<?php
													$msConsulta = "SELECT CARRERA_REL, NOMBRE_040 FROM UMO040A WHERE POSGRADO_040 = 1 ORDER BY NOMBRE_040";
													$mDatos = $m_cnx_MySQL->prepare($msConsulta);
													$mDatos->execute();
													while ($mFila = $mDatos->fetch())
													{
														$msValor = rtrim($mFila["CARRERA_REL"]);
														$msTexto = rtrim($mFila["NOMBRE_040"]);
														if ($msCarrera == ""){
															$selected = ($msValor == $msValor) ? "selected" : "";
															$msCarrera = $msValor; // asignamos el valor iniciaL	
														} else {
															$selected = ($msCarrera == $msValor) ? "selected" : "";
														}
														echo "<option value='$msValor' $selected>$msTexto</option>";										
													}
												?>
											</select>
										</div>
									</div>

									<div class = "form-group row">
										<label for="txtNombre1" class="col-sm-12 col-md-3 form-label">Primer nombre</label>
										<div class="col-sm-12 col-md-4">
										<?php echo('<input type="text" class="form-control" id="txtNombre1" name="txtNombre1" value="' . $msNombre1 . '" onchange="fxEscribeCorreo()" />'); ?>
										</div>
									</div>

									<div class = "form-group row">
										<label for="txtNombre2" class="col-sm-12 col-md-3 form-label">Segundo nombre</label>
										<div class="col-sm-12 col-md-4">
										<?php echo('<input type="text" class="form-control" id="txtNombre2" name="txtNombre2" value="' . $msNombre2 . '" />'); ?>
										</div>
									</div>

									<div class = "form-group row">
										<label for="txtApellido1" class="col-sm-12 col-md-3 form-label">Primer apellido</label>
										<div class="col-sm-12 col-md-4">
										<?php echo('<input type="text" class="form-control" id="txtApellido1" name="txtApellido1" value="' . $msApellido1 . '" onchange="fxEscribeCorreo()" />'); ?>
										</div>
									</div>

									<div class = "form-group row">
										<label for="txtApellido2" class="col-sm-12 col-md-3 form-label">Segundo apellido</label>
										<div class="col-sm-12 col-md-4">
										<?php echo('<input type="text" class="form-control" id="txtApellido2" name="txtApellido2" value="' . $msApellido2 . '" />'); ?>
										</div>
									</div>

									<div class = "form-group row">
										<label for="dtpFechaNac" class="col-sm-12 col-md-3 form-label">Fecha de nacimiento</label>
										<div class="col-sm-12 col-md-2">
										<?php echo('<input type="date" class="form-control" id="dtpFechaNac" name="dtpFechaNac" value="' . $mdFechaNac . '" onchange="calcularEdad()" />'); ?>
										</div>
									</div>
									
									<div class = "form-group row">
										<label for="txtEdad" class="col-sm-12 col-md-3 form-label">Edad</label>
										<div class="col-sm-12 col-md-2">
											<input type="text" class="form-control" id="txtEdad" name="txtEdad" value="" disabled />
										</div>
									</div>

									<div class = "form-group row">
										<label for="txtCarnet" class="col-sm-12 col-md-3 form-label">Carnet</label>
										<div class="col-sm-12 col-md-3">
										<?php 
											echo('<input type="text" class="form-control" id="txtCarnet" name="txtCarnet" value="' . $msCarnet . '" />');
										?>
										</div>
									</div>

									<div class = "form-group row">
										<label for="txtNacionalidad" class="col-sm-12 col-md-3 form-label">Nacionalidad</label>
										<div class="col-sm-12 col-md-4">
										<?php echo('<input type="text" class="form-control" id="txtNacionalidad" name="txtNacionalidad" value="' . $msNacionalidad . '" />'); ?>
										</div>
									</div>

									<div class="form-group row">
										<label for="cboDepartamento" class="col-sm-12 col-md-3 col-form-label">Departamento</label>
										<div class="col-sm-12 col-md-4">
											<select class="form-control" id="cboDepartamento" name="cboDepartamento" onchange="llenaMunicipios(this.value)">
												<?php
												if ($msCodigo!="")
													{
														$msConsulta = "select DEPARTAMENTO_REL from UMO120A where MUNICIPIO_REL = ?";
														$mDatos = $m_cnx_MySQL->prepare($msConsulta);
														$mDatos->execute([$msMunicipio]);
														$mFila = $mDatos->fetch();
														$msDepartamento = rtrim($mFila["DEPARTAMENTO_REL"]);
													}
													else
														$msDepartamento = "";
														$msConsulta = "select DEPARTAMENTO_REL, NOMBRE_110 from UMO110A order by NOMBRE_110";
														$mDatos = $m_cnx_MySQL->prepare($msConsulta);
														$mDatos->execute();
														while ($mFila = $mDatos->fetch())
														{
															$msValor = rtrim($mFila["DEPARTAMENTO_REL"]);
															$msTexto = rtrim($mFila["NOMBRE_110"]);
															if ($msDepartamento == "")
															{
																echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
																$msDepartamento = $msValor;
															}
															else
															{
																if ($msDepartamento == $msValor)
																	echo("<option value='" . $msValor . "' selected>" . $msTexto . "</option>");
																else
																	echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
															}
														}
												?>
											</select>
										</div>
									</div>
						
									<div class="form-group row">
										<label for="cboMunicipio" class="col-sm-12 col-md-3 col-form-label">Municipio</label>
										<div class="col-sm-12 col-md-4">
											<select class="form-control" id="cboMunicipio" name="cboMunicipio">
												<?php
													$msConsulta = "select MUNICIPIO_REL, NOMBRE_120 from UMO120A where DEPARTAMENTO_REL = ? order by NOMBRE_120";
													$mDatos = $m_cnx_MySQL->prepare($msConsulta);
													$mDatos->execute([$msDepartamento]);
													while ($mFila = $mDatos->fetch())
													{
														$msValor = rtrim($mFila["MUNICIPIO_REL"]);
														$msTexto = rtrim($mFila["NOMBRE_120"]);
														if ($msCodigo == "")
															echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
														else
														{
															if ($msMunicipio == "")
															{
																echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
																$msMunicipio = $msValor;
															}
															else
															{
																if ($msMunicipio == $msValor)
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
										<label for="txtCedula" class="col-sm-12 col-md-3 form-label">Cédula</label>
										<div class="col-sm-12 col-md-4">
										<?php echo('<input type="text" class="form-control" id="txtCedula" name="txtCedula" maxlength="20" value="' . $msCedula . '" />'); ?>
										</div>
									</div>
									
									<div class = "form-group row">
										<label for="optSexo" class="col-sm-12 col-md-3 form-label">Sexo</label>
										<div class="col-sm-12 col-md-3">
											<div class = "radio">
											<?php
												if ($msSexo == "M")
													echo('<input type="radio" id="optSexo1" name="optSexo" value="M" checked="checked" /> Masculino &emsp;');
												else
													echo('<input type="radio" id="optSexo1" name="optSexo" value="M" /> Masculino &emsp;');

												if ($msSexo == "F")
													echo('<input type="radio" id="optSexo2" name="optSexo" value="F" checked="checked" /> Femenino');
												else
													echo('<input type="radio" id="optSexo2" name="optSexo" value="F" /> Femenino');
											?>
											</div>
										</div>
									</div>

									<div class="form-group row">
										<label for="cboEstadoCivil" class="col-sm-12 col-md-3 col-form-label">Estado civil</label>
										<div class="col-sm-12 col-md-3">
											<select class="form-control" id="cboEstadoCivil" name="cboEstadoCivil">
												<?php
													if ($mnEstadoCivil == 0)
														echo("<option value='0' selected>Soltero</option>");
													else
														echo("<option value='0'>Soltero</option>");

													if ($mnEstadoCivil == 1)
														echo("<option value='1' selected>Casado</option>");
													else
														echo("<option value='1'>Casado</option>");

													if ($mnEstadoCivil == 2)
														echo("<option value='2' selected>Unión de hecho</option>");
													else
														echo("<option value='2'>Unión de hecho</option>");

													if ($mnEstadoCivil == 3)
														echo("<option value='3' selected>Viudo</option>");
													else
														echo("<option value='3'>Viudo</option>");
												?>
											</select>
										</div>
									</div>
									
									<div class = "form-group row">
										<label for="txnHijos" class="col-sm-12 col-md-3 form-label">Hijos</label>
										<div class="col-sm-12 col-md-3">
										<?php echo('<input type="number" class="form-control" id="txnHijos" name="txnHijos" value="' . $mnHijos . '" />'); ?>
										</div>
									</div>

									<div class="form-group row">
										<label for="cboUniversidad" class="col-sm-12 col-md-3 col-form-label">Universidad de procedencia</label>
										<div class="col-sm-12 col-md-7">
											<select class="form-control" id="cboUniversidad" name="cboUniveridad">
												<?php
													$msConsulta = "select UNIVERSIDAD_REL, NOMBRE_180 from UMO180A order by NOMBRE_180";
													$mDatos = $m_cnx_MySQL->prepare($msConsulta);
													$mDatos->execute();
													while ($mFila = $mDatos->fetch())
													{
														$msValor = rtrim($mFila["UNIVERSIDAD_REL"]);
														$msTexto = rtrim($mFila["NOMBRE_180"]);
														if ($msCodigo == "")
															echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
														else
														{
															if ($msUniversidad == "")
															{
																echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
																$msUniversidad = $msValor;
															}
															else
															{
																if ($msUniversidad == $msValor)
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
										<label for="txtGradoAcademico" class="col-sm-12 col-md-3 form-label">Grado académico</label>
										<div class="col-sm-12 col-md-7">
										<?php echo('<input type="text" class="form-control" id="txtGradoAcademico" name="txtGradoAcademico" maxlength="100" value="' . $msGradoAcademico . '" placeholder="Licenciado, Ingeniero, Doctor..." />'); ?>
										</div>
									</div>

									<div class = "form-group row">
										<label for="txtTelefono" class="col-sm-12 col-md-3 form-label">Teléfono</label>
										<div class="col-sm-12 col-md-3">
										<?php echo('<input type="text" class="form-control" id="txtTelefono" name="txtTelefono" maxlength="20" value="' . $msTelefono . '" />'); ?>
										</div>
									</div>

									<div class = "form-group row">
										<label for="txtCelular" class="col-sm-12 col-md-3 form-label">Celular</label>
										<div class="col-sm-12 col-md-3">
										<?php echo('<input type="text" class="form-control" id="txtCelular" name="txtCelular" maxlength="20" value="' . $msCelular . '" />'); ?>
										</div>
									</div>

									<div class = "form-group row">
										<label for="txtCorreoE" class="col-sm-12 col-md-3 form-label">Correo electrónico</label>
										<div class="col-sm-12 col-md-4">
										<?php echo('<input type="text" class="form-control" id="txtCorreoE" name="txtCorreoE" maxlength="100" value="' . $msCorreoE . '" />'); ?>
										</div>
									</div>

									<div class = "form-group row">
										<label for="txtCorreoI" class="col-sm-12 col-md-3 form-label">Correo institucional</label>
										<div class="col-sm-12 col-md-4">
										<?php echo('<input type="text" class="form-control" id="txtCorreoI" name="txtCorreoI" maxlength="100" value="' . $msCorreoI . '" />'); ?>
										</div>
									</div>

									<div class = "form-group row">
										<label for="optOtroIdioma" class="col-sm-12 col-md-3 form-label">Habla otro idioma</label>
										<div class="col-sm-12 col-md-3">
											<div class = "radio">
											<?php
												if ($mbOtroIdioma == 1)
													echo('<input type="radio" id="optOtroIdioma1" name="optOtroIdioma" value="1" checked="checked" onchange="fxOptIdioma()" /> Si &emsp;');
												else
													echo('<input type="radio" id="optOtroIdioma1" name="optOtroIdioma" value="1" onchange="fxOptIdioma()" /> Si &emsp;');

												if ($mbOtroIdioma == 0)
													echo('<input type="radio" id="optOtroIdioma2" name="optOtroIdioma" value="0" checked="checked" onchange="fxOptIdioma()" /> No');
												else
													echo('<input type="radio" id="optOtroIdioma2" name="optOtroIdioma" value="0" onchange="fxOptIdioma()" /> No');
											?>
											</div>
										</div>
									</div>

									<div class = "form-group row">
										<label for="txtIdioma" class="col-sm-12 col-md-3 form-label">Idioma(s) que habla</label>
										<div class="col-sm-12 col-md-4">
										<?php
											if ($mbOtroIdioma == 1)
												echo('<input type="text" class="form-control" id="txtIdioma" name="txtIdioma" value="' . $msIdioma . '" />');
											else
												echo('<input type="text" class="form-control" id="txtIdioma" name="txtIdioma" value="" disabled />');
										?>
										</div>
									</div>

									<div class = "form-group row">
										<label for="txtDireccion" class="col-sm-12 col-md-3 form-label">Dirección</label>
										<div class="col-sm-12 col-md-7">
										<?php echo('<textarea class="form-control" id="txtDireccion" name="txtDireccion" rows="3">' . $msDireccion . '</textarea>'); ?>
										</div>
									</div>

									<div class = "form-group row">
										<label for="cboMedio" class="col-sm-12 col-md-3 form-label">Medio por el cual se enteró</label>
										<div class="col-sm-12 col-md-4">
											<select class="form-control" id="cboMedio" name="cboMedio">
												<?php
													if ($mnMedio == 1)
														echo("<option value='1' selected>Visita al colegio</option>");
													else
														echo("<option value='1'>Visita al colegio</option>");

													if ($mnMedio == 2)
														echo("<option value='2' selected>Facebook</option>");
													else
														echo("<option value='2'>Facebook</option>");

													if ($mnMedio == 3)
														echo("<option value='3' selected>Instagram</option>");
													else
														echo("<option value='3'>Instagram</option>");

													if ($mnMedio == 4)
														echo("<option value='4' selected>Clínica ODM</option>");
													else
														echo("<option value='4'>Clínica ODM</option>");

													if ($mnMedio == 5)
														echo("<option value='5' selected>Radio</option>");
													else
														echo("<option value='5'>Radio</option>");

													if ($mnMedio == 6)
														echo("<option value='6' selected>Estudiante UMO-JN</option>");
													else
														echo("<option value='6'>Estudiante UMO-JN</option>");

													if ($mnMedio == 7)
														echo("<option value='7' selected>Publicidad en la Calle</option>");
													else
														echo("<option value='7'>Publicidad en la Calle</option>");

													if ($mnMedio == 8)
														echo("<option value='8' selected>Por un amigo o familiar</option>");
													else
														echo("<option value='8'>Por un amigo o familiar</option>");

													if ($mnMedio == 9)
														echo("<option value='9' selected>Feria de Salud</option>");
													else
														echo("<option value='9'>Feria de Salud</option>");

													if ($mnMedio == 10)
														echo("<option value='10' selected>Tik Tok</option>");
													else
														echo("<option value='10'>Tik Tok</option>");

													if ($mnMedio == 11)
														echo("<option value='11' selected>Clínica PAMIC</option>");
													else
														echo("<option value='11'>Clínica PAMIC</option>");

													if ($mnMedio == 12)
														echo("<option value='12' selected>Televisión</option>");
													else
														echo("<option value='12'>Televisión</option>");

													if ($mnMedio == 13)
														echo("<option value='13' selected>Búsqueda en la web</option>");
													else
														echo("<option value='13'>Búsqueda en la web</option>");

													if ($mnMedio == 14)
														echo("<option value='14' selected>Feria universitaria</option>");
													else
														echo("<option value='14'>Feria universitaria</option>");

													if ($mnMedio == 15)
														echo("<option value='15' selected>Sitio web UMO-JN</option>");
													else
														echo("<option value='15'>Sitio web UMO-JN</option>");

													if ($mnMedio == 16)
														echo("<option value='16' selected>Funcionario UMO-JN</option>");
													else
														echo("<option value='16'>Funcionario UMO-JN</option>");

													if ($mnMedio == 17)
														echo("<option value='17' selected>WhatsApp</option>");
													else
														echo("<option value='17'>WhatsApp</option>");

													if ($mnMedio == 18)
														echo("<option value='18' selected>Cursos libres</option>");
													else
														echo("<option value='18'>Cursos libres</option>");

													if ($mnMedio == 19)
														echo("<option value='19' selected>Otros</option>");
													else
														echo("<option value='19'>Otros</option>");
												?>
											</select>
										</div>
									</div>

						
								</div>
							</div>
							<!--Fin del DIV de Tab GENERALES-->

							<!--Inicio del DIV de Tab LABORAL-->
							<div title="Situación laboral" style="padding-left: 20px; padding-top: 10px">
								<div class = "form-group row">
									<label for="optLaboral" class="col-sm-12 col-md-3 form-label">¿Está laborando?</label>
									<div class="col-sm-12 col-md-4">
										<div class = "radio">
										<?php
											if ($mbLaboral == 1)
												echo('<input type="radio" id="optLaboral1" name="optLaboral" value="1" checked="checked" onchange="fxOptLaboral()" /> Empleado &emsp;');
											else
												echo('<input type="radio" id="optLaboral1" name="optLaboral" value="1" onchange="fxOptLaboral()" /> Empleado &emsp;');

											if ($mbLaboral == 0)
												echo('<input type="radio" id="optLaboral2" name="optLaboral" value="0" checked="checked" onchange="fxOptLaboral()" /> Desempleado');
											else
												echo('<input type="radio" id="optLaboral2" name="optLaboral" value="0" onchange="fxOptLaboral()" /> Desempleado');
										?>
										</div>
									</div>
								</div>

								<div class = "form-group row">
									<label for="txtOcupacion" class="col-sm-12 col-md-3 form-label">Ocupación/Profesión</label>
									<div class="col-sm-10 col-md-6">
									<?php
										if ($mbLaboral == 1)
											echo('<input type="text" class="form-control" id="txtOcupacion" name="txtOcupacion" value="' . $msOcupacion . '" />');
										else
											echo('<input type="text" class="form-control" id="txtOcupacion" name="txtOcupacion" value="" disabled />');
									?>
									</div>
								</div>

								<div class = "form-group row">
									<label for="txnSalario" class="col-sm-12 col-md-3 form-label">Ingreso mensual</label>
									<div class="col-sm-10 col-md-2">
									<?php
										if ($mbLaboral == 1)
											echo('<input type="number" class="form-control" id="txnSalario" name="txnSalario" value="' . $mnIngresoMensual . '" />');
										else
											echo('<input type="number" class="form-control" id="txnSalario" name="txnSalario" value="0" disabled />');
									?>
									</div>	
								</div>

								<div class = "form-group row">
									<label for="txtCentroTrabajo" class="col-sm-12 col-md-3 form-label">Centro de trabajo</label>
									<div class="col-sm-10 col-md-6">
									<?php
										if ($mbLaboral == 1)
											echo('<input type="text" class="form-control" id="txtCentroTrabajo" name="txtCentroTrabajo" value="' . $msCentroTrabajo . '" />');
										else
											echo('<input type="text" class="form-control" id="txtCentroTrabajo" name="txtCentroTrabajo" value="" disabled />');
									?>
									</div>
								</div>

								<div class = "form-group row">
									<label for="txtDireccionTrabajo" class="col-sm-12 col-md-3 form-label">Dirección del trabajo</label>
									<div class="col-sm-12 col-md-7">
									<?php
										if ($mbLaboral == 1) 
											echo('<textarea class="form-control" id="txtDireccionTrabajo" name="txtDireccionTrabajo" rows="3">' . $msDireccionTrabajo . '</textarea>');
										else
											echo('<textarea class="form-control" id="txtDireccionTrabajo" name="txtDireccionTrabajo" rows="3" disabled></textarea>');
									?>
									</div>
								</div>
							</div>
							<!--Fin del DIV de Tab LABORAL-->
							
							<!--Inicio del DIV de Tab EMERGENCIA-->
							<div title="Datos para emergencias" style="padding-left: 20px; padding-top: 10px"">
								<div class = "form-group row">
									<label for="txtEmergencia" class="col-sm-12 col-md-3 form-label">En caso de emergencia avisar a</label>
									<div class="col-sm-12 col-md-6">
									<?php echo('<input type="text" class="form-control" id="txtEmergencia" name="txtEmergencia" value="' . $msEmergencia . '" />'); ?>
									</div>
								</div>

								<div class = "form-group row">
									<label for="txtTelEmergencia" class="col-sm-12 col-md-3 form-label">Telefono de emergencia</label>
									<div class="col-sm-12 col-md-3">
									<?php echo('<input type="text" class="form-control" id="txtTelEmergencia" name="txtTelEmergencia" maxlength="20" value="' . $msTelEmergencia . '" />'); ?>
									</div>
								</div>
								
								<div class = "form-group row">
									<label for="txtCelEmergencia" class="col-sm-12 col-md-3 form-label">Celular de emergencia</label>
									<div class="col-sm-12 col-md-3">
									<?php echo('<input type="text" class="form-control" id="txtCelEmergencia" name="txtCelEmergencia" maxlength="20" value="' . $msCelEmergencia . '" />'); ?>
									</div>
								</div>

								<div class="form-group row">
									<label for="cboTipoSangre" class="col-sm-12 col-md-3 col-form-label">Tipo de sangre</label>
									<div class="col-sm-12 col-md-2">
										<select class="form-control" id="cboTipoSangre" name="cboTipoSangre">
											<?php
												if ($msTipoSangre == "O-")
													echo("<option value='O-' selected>O-</option>");
												else
													echo("<option value='O-'>O-</option>");

												if ($msTipoSangre == "O+")
													echo("<option value='O+' selected>O+</option>");
												else
													echo("<option value='O+'>O+</option>");

												if ($msTipoSangre == "A-")
													echo("<option value='A-' selected>A-</option>");
												else
													echo("<option value='A-'>A-</option>");

												if ($msTipoSangre == "A+")
													echo("<option value='A+' selected>A+</option>");
												else
													echo("<option value='A+'>A+</option>");

												if ($msTipoSangre == "B-")
													echo("<option value='B-' selected>B-</option>");
												else
													echo("<option value='B-'>B-</option>");

												if ($msTipoSangre == "B+")
													echo("<option value='B+' selected>B+</option>");
												else
													echo("<option value='B+'>B+</option>");

												if ($msTipoSangre == "AB-")
													echo("<option value='AB-' selected>AB-</option>");
												else
													echo("<option value='AB-'>AB-</option>");

												if ($msTipoSangre == "AB+")
													echo("<option value='AB+' selected>AB+</option>");
												else
													echo("<option value='AB+'>AB+</option>");

												if ($msTipoSangre == "N/A")
													echo("<option value='N/A' selected>N/A</option>");
												else
													echo("<option value='N/A'>N/A</option>");
											?>
										</select>
									</div>
								</div>

								<div class = "form-group row">
									<label for="txnPeso" class="col-sm-12 col-md-3 form-label">Peso (en libras)</label>
									<div class="col-sm-12 col-md-2">
										<?php echo('<input type="number" class="form-control" id="txnPeso" name="txnPeso" value="' . $mnPeso . '" />'); ?>
									</div>
								</div>

								<div class = "form-group row">
									<label for="txnTalla" class="col-sm-12 col-md-3 form-label">Talla</label>
									<div class="col-sm-12 col-md-2">
										<?php echo('<input type="number" step="0.01" class="form-control" id="txnTalla" name="txnTalla" value="' . $mnTalla . '" />'); ?>
									</div>
								</div>
							</div>
							<!--Fin del DIV de Tab EMERGENCIA-->

							<!--Inicio del DIV de Tab DOCUMENTOS-->
							<div title="Documentos" style="padding-left: 20px; padding-top: 10px"">
								<!--Inicio del DIV de Tab SOPORTE-->
								<div class="col-xs-auto col-md-12">
									<!--Inicio del DIV Columna SOPORTE-->
									<div style="height:auto; padding-top:1%; padding-bottom:2%">
										<table width="100%">
											<tr>
												<td valign="top" style="width: 15%;">Tipo de documento</td>
												<td style="width:70%">
													<select class="form-control" id="cboTipoDoc" name="cboTipoDoc">
														<option value="0">Curriculum vitae</option>
														<option value="1">Calificaciones de grado</option>
														<option value="2">Cédula de identidad</option>
														<option value="3">Título de grado</option>
														<option value="4">Fotografía</option>
														<option value="5">Plan de estudio</option>
														<option value="6">Acta de aprobación monográfica</option>
														<option value="7">Calificaciones universitarias</option>
														<option value="8">Certificación del título universitario</option>
														<option value="9">Datos generales del título</option>
														<option value="10">Publicación en la gaceta</option>
														<option value="11">Título de posgrado</option>
													</select>
												</td>
												<td></td>
											</tr>
											<tr>
												<td valign="top" style="width: 15%;">Imagen</td>
												<td style="width:70%">
													<input id="txtRutaLocal" class="form-control" readonly>
												</td>
												<td>
													<label for="archivo" style="margin-left:1%; padding:0.5%" data-toggle="tooltip" data-placement="top" title="Agregar imagen">
													<img src="imagenes/imageAdd.png" height="100%" style="cursor:pointer" /></label>
													<input type="file" accept=".pdf, image/*" id="archivo" style="display:none"	onchange="llenaArchivo()" />
													<label id="cmdSubir" data-toggle="tooltip" data-placement="top"	title="Subir imagen"><img src="imagenes/imageUp.png" height="100%" style="cursor:pointer" /></label>
												</td>
											</tr>
											<tr>
												<td></td>
												<td style="width:70%">
													<label style="font-size:small; font-style:italic; color:rgb(130,130,130)">El nombre del archivo no debe contener espacios en blanco.</label>
												</td>
												<td></td>
											</tr>
										</table>
									</div>
									<div id="dvDOC" style="height:300px; padding-top:1%; padding-bottom:2%">
										<?php
											$mnCuenta = 0;
											$texto = '<table width="100%">';
											
											$mDatos = fxDevuelveDetDocumentoPos($msCodigo);
											while ($mFila = $mDatos->fetch())
											{
												$extensionImg = strtoupper(substr($mFila["ARCHIVO_251"], -3));
												if ($mnCuenta == 0) {
													$texto .= '<tr>';
												}
												$texto .= '<td width="23%" valign="top" style="margin-left:1%; margin-right:1%">';
												$texto .= '<img src="imagenes/imageDel.png"  id="' . trim($mFila["ARCHIVO_251"]) . '" style="cursor:pointer" onclick="borrarImagen(this)"/><label style="font-size: small"> Borrar ' . trim($mFila["ARCHIVO_251"]) . '</label>';
												if ($extensionImg != 'PDF')
													$texto .= '<br/><a href="' . trim($mFila["RUTA_251"]) . '" target="_blank"><img src="' . trim($mFila["RUTA_251"]) . '" style="width:100%"/></a>';
												else
													$texto .= '<br/><a href="' . trim($mFila["RUTA_251"]) . '" target="_blank"><img src="imagenes/pdf.png" style="width:80%"/></a>';
												$texto .= '<br/><div>' . trim($mFila["DESC_011"]) . '</div';
												$texto .= '</td>';
												$mnCuenta++;
												if ($mnCuenta == 4) {
													$texto .= '</tr>';
													$mnCuenta = 0;
												}
											}
											if ($mnCuenta == 1) {
												$texto .= '<td></td><td></td><td></td></tr>';
											}
											if ($mnCuenta == 2) {
												$texto .= '<td></td><td></td></tr>';
											}
											if ($mnCuenta == 3) {
												$texto .= '<td></td></tr>';
											}
											
											$texto .= '</table>';
											
											echo($texto);
										?>
									</div>
								</div>
							</div>
							<!--Fin del DIV de Tab DOCUMENTOS-->
						</div>
					</form>
                </div>
	<?php	}
		}
	}
?>
			</div>
		</div>
	</div>
</body>
</html>
<script>
	var mCedula;
	var msResultado;
	var codEstudiante;
	var existeCedula;
	var parametros;
	var datosJson;

	function verificarFormulario()
	{
		var msCorreoI = document.getElementById('txtCorreoI').value

		if(document.getElementById('txtNombre1').value=="")
		{
			document.getElementById('txtNombre1').focus();
			$.messager.alert('UMOJN','Falta el primer nombre.','warning');
			return false;
		}

		if(document.getElementById('txtApellido1').value=="")
		{
			document.getElementById('txtApellido1').focus();
			$.messager.alert('UMOJN','Falta el primer apellido.','warning');
			return false;
		}

		if (document.getElementById('txtCedula').value=="")
		{
			document.getElementById('txtCedula').focus();
			$.messager.alert('UMOJN','Falta la Cédula.','warning');
			return false;
		}

		if(mCedula.indexOf("-") > -1)
		{
			document.getElementById('txtCedula').focus();
			$.messager.alert('UMOJN','Escriba la Cédula sin guiones.','warning');
			return false;
		}

		if (existeCedula == true)
		{
			document.getElementById('txtCedula').focus();
			$.messager.alert('UMOJN','La Cédula ya fue registrada con el estudiante ' + codEstudiante,'warning');
			return false;
		}

		if (msCorreoI.indexOf(" ") > -1)
		{
			document.getElementById('txtCorreoI').focus();
			$.messager.alert('UMOJN','Escriba el correo sin espacios en blanco.','warning');
			return false;
		}
/*
		if (document.getElementById('txtUsuario').value.length > 20)
		{
			$.messager.alert('UMOJN','La longitud del usuario sobrepasa los 20 caracteres', 'warning');
			return false;
		}
*/
		if (document.getElementById('optOtroIdioma1').checked)
		{
			if (document.getElementById('txtIdioma').value=="")
			{
				document.getElementById('txtIdioma').focus();
				$.messager.alert('UMOJN','Falta el idioma.','warning');
				return false;
			}
		}

		if (document.getElementById('optLaboral1').checked)
		{
			if (document.getElementById('txtOcupacion').value=="")
			{
				document.getElementById('txtOcupacion').focus();
				$.messager.alert('UMOJN','Falta la ocupación.','warning');
				return false;
			}
		}

		return true;
	}

	function obtenerCedula()
	{
		mCedula = document.getElementById('txtCedula').value;
		parametros = '{"cedulaEstudiante":"' + document.getElementById("txtCedula").value + '", "codEstudiante":"' + document.getElementById("txtEstudiante").value + '", "carreraEstudiante":"' + document.getElementById("cboCarrera").value + '"}';
		datosJson = JSON.parse(parametros);
	
		return $.ajax({
			url:'funciones/fxDatosEstudiantesPos.php',
			type:'post',
			async: false,
			data:datosJson,
			beforeSend: function(){console.log(datosJson)}
		})
	}

	function obtenerCarnet()
	{
		if (document.getElementById("txtEstudiante").value=="")
		{
			parametros = '{"fechaNac":"'+document.getElementById("dtpFechaNac").value+'", "codCarrera":"' +document.getElementById("cboCarrera").value+ '", "annoAcademico":"'+document.getElementById("txnAnnoAcademico").value+'"}';
			datosJson = JSON.parse(parametros);
		
			return $.ajax({
				url:'funciones/fxDatosEstudiantesPos.php',
				type:'post',
				async: false,
				data:datosJson,
				beforeSend: function(){console.log(datosJson)},
				success: function(response){document.getElementById('txtCarnet').value = response;}
			})
		}
	}

	window.onload=function()
	{
		if (document.getElementById("dtpFechaNac").value != "")
			calcularEdad();
	}

	function fxOptLaboral()
	{
		var mbLaboral = document.getElementById('optLaboral1').checked;

		if (mbLaboral)
		{
			document.getElementById('txtOcupacion').disabled = false;
			document.getElementById('txnSalario').disabled = false;
			document.getElementById('txtCentroTrabajo').disabled = false;
			document.getElementById('txtDireccionTrabajo').disabled = false;
		}
		else
		{
			document.getElementById('txtOcupacion').disabled = true;
			document.getElementById('txnSalario').disabled = true;
			document.getElementById('txtCentroTrabajo').disabled = true;
			document.getElementById('txtDireccionTrabajo').disabled = true;
		}
	}

	function fxOptIdioma()
	{
		var mbIdioma = document.getElementById('optOtroIdioma1').checked;

		if (mbIdioma)
			document.getElementById('txtIdioma').disabled = false;
		else
			document.getElementById('txtIdioma').disabled = true;
	}
		
	
	function calcularEdad()
	{
		var today_date = new Date();
		var today_year = today_date.getFullYear();
		var today_month = today_date.getMonth();
		var today_day = today_date.getDate();
		var birth_date = document.getElementById("dtpFechaNac").value;
		var birth_year = parseInt(birth_date.substr(0,4));
		var birth_month = parseInt(birth_date.substr(5,2));
		var birth_day = parseInt(birth_date.substr(7,2));

		var age = today_year - birth_year;

		if (today_month < (birth_month - 1)) {
		age--;
		}
		if (((birth_month - 1) == today_month) && (today_day < birth_day)) {
		age--;
		}
		document.getElementById("txtEdad").value = age + " años";

		obtenerCarnet();
	}

	function llenaMunicipios (departamento)
	{
		var datos = new FormData();
		datos.append('departamento', departamento);

		$.ajax({
			url: 'funciones/fxDatosColegios.php',
			type: 'post',
			data: datos,
			contentType: false,
			processData: false,
			success: function(response){
				document.getElementById('cboMunicipio').innerHTML = response;
			}
		})
	}

	function fxEscribeCorreo()
	{
		var msNombre = document.getElementById('txtNombre1').value;
		var msApellido = document.getElementById('txtApellido1').value;
		var msCodigo = document.getElementById('txtEstudiante').value;

		if (msCodigo == "")
		{
			if (msNombre != "" && msApellido != "")
			{
				document.getElementById('txtCorreoI').value = msNombre.toLowerCase() + "." + msApellido.toLowerCase() + "@umojn.edu.ni"
				//document.getElementById('txtUsuario').value = msNombre.toLowerCase() + "." + msApellido.toLowerCase()
			}
		}
	}

	function llenaArchivo() {
		$('#txtRutaLocal').val($('#archivo')[0].files[0].name);
	}

	function borrarImagen(objeto) {
		var objId = objeto.id;
		var datos = new FormData();
		var estudiante = $('#txtEstudiante').val();
		datos.append('CodEstudiante', estudiante);
		datos.append('CodImagen', objId);

		$.ajax({
			url: 'funciones/fxEstudiantesPosImagenes.php',
			type: 'post',
			data: datos,
			contentType: false,
			processData: false,
			success: function(response) {
				if (response != 0) {
					document.getElementById('dvDOC').innerHTML = response;
				} else {
					$.messager.alert('UMOJN', 'Error en la eliminación de la imagen.', 'warning');
				}
			}
		});
	}

	$(document).ready(function() {
		$('#cmdSubir').on('click', function() {
			if ($('#txtEstudiante').val() == '') {
				$.messager.alert('UMOJN', 'Debe guardar la Información General antes de subir los Documentos de soporte.', 'warning');
				return false;
			}

			if ($('#txtRutaLocal').val() == '') {
				$.messager.alert('UMOJN', 'Falta el archivo de la imagen.', 'warning');
				return false;
			} else {
				var datos = new FormData();
				var files = $('#archivo')[0].files[0];
				var estudiante = $('#txtEstudiante').val();
				var tipo = $('#cboTipoDoc').val();
				var descripcion = $('select[name="cboTipoDoc"] option:selected').text();
				datos.append('archivo', files);
				datos.append('cboTipoDoc', tipo);
				datos.append('txtEstudiante', estudiante);
				datos.append('txtDescripcion', descripcion);

				$.ajax({
					url: 'funciones/fxEstudiantesPosImagenes.php',
					type: 'post',
					data: datos,
					contentType: false,
					processData: false,
					success: function(response) {
						if (response != 0) {
							document.getElementById('dvDOC').innerHTML = response;
							$('#txtRutaLocal').val('');
						} else {
							$.messager.alert('UMOJN', 'Error en la subida de la imagen.', 'warning');
						}
					}
				});
				return false;
			}
		});
	});
	
	$('form').submit(function(e){ 
		e.preventDefault();

		$.when(obtenerCedula()).done(function(respuesta)
		{
			if (respuesta != "")
				existeCedula = true;
			else
				existeCedula = false;

			codEstudiante = respuesta;
		})

		if (verificarFormulario() == true)
		{
			let datos = {
				txtEstudiante: document.getElementById("txtEstudiante").value,
				cboCarrera: document.getElementById("cboCarrera").value,
				cboUniversidad: document.getElementById("cboUniversidad").value,
				cboMunicipio: document.getElementById("cboMunicipio").value,
				//txtUsuario: document.getElementById("txtUsuario").value,
				dtpFecha: document.getElementById("dtpFecha").value,
				dtpFechaNac: document.getElementById("dtpFechaNac").value,
				txtNombre1: document.getElementById("txtNombre1").value,
				txtNombre2: document.getElementById("txtNombre2").value,
				txtApellido1: document.getElementById("txtApellido1").value,
				txtApellido2: document.getElementById("txtApellido2").value,
				txtNacionalidad: document.getElementById("txtNacionalidad").value,
				txnAnnoAcademico: document.getElementById("txnAnnoAcademico").value,
				txtGradoAcademico: document.getElementById("txtGradoAcademico").value,
				txtCarnet: document.getElementById("txtCarnet").value,
				txnPeso: document.getElementById("txnPeso").value,
				txnTalla: document.getElementById("txnTalla").value,
				cboTipoSangre: document.getElementById("cboTipoSangre").value,
				txtCedula: document.getElementById("txtCedula").value,
				optSexo: document.getElementById("optSexo1").checked ? "M" : "F",			
				cboEstadoCivil: document.getElementById("cboEstadoCivil").value,
				txnHijos: document.getElementById("txnHijos").value,
				txtTelefono: document.getElementById("txtTelefono").value,
				txtCelular: document.getElementById("txtCelular").value,
				txtCorreoE: document.getElementById("txtCorreoE").value,
				txtCorreoI: document.getElementById("txtCorreoI").value,
				txtDireccion: document.getElementById("txtDireccion").value,
				cboMedio: document.getElementById("cboMedio").value,
				txtEmergencia: document.getElementById("txtEmergencia").value,
				txtTelEmergencia: document.getElementById("txtTelEmergencia").value,
				txtCelEmergencia: document.getElementById("txtCelEmergencia").value,
				optLaboral: document.getElementById("optLaboral1").checked ? "1" : "0",
				txtOcupacion: document.getElementById("optLaboral1").checked ? document.getElementById("txtOcupacion").value : "",
				txnIngreso: document.getElementById("optLaboral1").checked ? document.getElementById("txnSalario").value : "0",
				txtCentroTrabajo: document.getElementById("optLaboral1").checked ? document.getElementById("txtCentroTrabajo").value : "",
				txtDireccionTrabajo: document.getElementById("optLaboral1").checked ? document.getElementById("txtDireccionTrabajo").value : "",
				optOtroIdioma: document.getElementById("optOtroIdioma1").checked ? "1" : "0",
				txtIdioma: document.getElementById("optOtroIdioma1").checked ? document.getElementById("txtIdioma").value : ""
			};

			$.ajax({
				url: 'catAlumnosPosgrado.php',
				type: 'POST',
				data: datos,
				beforeSend: function(){console.log(datos)}
			})
			.done(function(){location.href="gridAlumnosPosgrado.php"})
			.fail(function(){console.log('Error')});
		}
	});
</script>