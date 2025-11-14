<?php
	session_start();
	if (!isset($_SESSION["gnVerifica"]) or $_SESSION["gnVerifica"] != 1)
	{
		echo('<meta http-equiv="Refresh" content="0;url=index.php">');
		exit('');
    }
	
	include ("masterApp.php");
	require_once ("funciones/fxGeneral.php");
	require_once ("funciones/fxUsuarios.php");
	require_once ("funciones/fxExpDigital.php");
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
		$mbPermisoUsuario = fxPermisoUsuario("procExpDigital", $mbAgregar, $mbModificar, $mbBorrar, $mbAnular);
		
		if ($mbAdministrador == 0 and $mbPermisoUsuario == 0)
		{ ?>
        <div class="container text-center">
        	<div id="DivContenido">
				<img src="imagenes/errordeacceso.png"/>
            </div>
        </div>
		<?php }
		else
		{
		?>
    	<div class="container">
        	<div id="DivContenido">
				<div id="lateral">
					<?php
						if ($mbAgregar == 1 or $mbAdministrador == 1)
							echo('<label id="agregar" data-toggle="tooltip" data-placement="top" title="Agregar"><img src="imagenes/btnLateralAgregar.png" height="80%" style="cursor:pointer" /></label>');
						else
							echo('<label id="agregarDis" data-toggle="tooltip" data-placement="top" title="Agregar"><img src="imagenes/btnLateralAgregarDis.png" height="80%" style="cursor:default" /></label>');
							
						if ($mbModificar == 1 or $mbAdministrador == 1)
							echo('<label id="modificar" data-toggle="tooltip" data-placement="top" title="Editar"><img src="imagenes/btnLateralEditar.png" height="80%" style="cursor:pointer" /></label>');
						else
							echo('<label id="modificarDis" data-toggle="tooltip" data-placement="top" title="Editar"><img src="imagenes/btnLateralEditarDis.png" height="80%" style="cursor:default" /></label>');
						
						echo('<label id="borrar" data-toggle="tooltip" data-placement="top" title="Borrar"><img src="imagenes/btnLateralBorrar.png" height="80%" style="cursor:pointer" /></label>');
						echo('<label id="generarqr" data-toggle="tooltip" data-placement="top" title="Generar QR"><img src="imagenes/btnLateralQR.png" height="80%" style="cursor:pointer" /></label>');
					?>
				</div>

				<div class="row">
					<div class="col-md-12">
						<?php
							if ($mbAgregar == 1 or $mbAdministrador == 1)
								echo('<button id="append" type="button" class="btn btn-primary">Agregar</button>');
							else
								echo('<button id="append" type="button" class="btn btn-primary" disabled>Agregar</button>');
								
							if ($mbModificar == 1 or $mbAdministrador == 1)
								echo('<button id="edit" type="button" class="btn btn-primary">Editar</button>');
							else
								echo('<button id="edit" type="button" class="btn btn-primary" disabled>Editar</button>');
							
							echo('<button id="remove" type="button" class="btn btn-primary">Borrar</button>');
							echo('<button id="addqr" type="button" class="btn btn-primary">Generar QR</button>');
						?>
						
						<table id="grid" class="table table-condensed table-hover table-striped" data-selection="true" data-multi-select="false" data-row-select="true" data-keep-selection="true">
							<thead>
								<tr>
									<th data-column-id="EXPDIGITAL_REL" data-identifier="true" data-align="left" data-width="15%">Expediente</th>
									<th data-column-id="FECHA_001" data-header-align="left" data-width="15%">Fecha de registro</th>
									<th data-column-id="CARRERA_001" data-align="left" data-header-align="left" data-width="70%">Carrera</th>
								</tr>
							</thead>
							<tbody>
							<?php
								$mDatos = fxDevuelveExpDigital(1);
								while ($mFila = $mDatos->fetch())
								{
									echo ("<tr>");
									echo ("<td>" . $mFila["EXPDIGITAL_REL"] . "</td>");

									$fecha = date_create_from_format('Y-m-d', $mFila["FECHA_001"]);
									echo ("<td>" . date_format($fecha, 'd-m-Y') . "</td>");

									echo ("<td>" . $mFila["CARRERA_001"] . " " . "</td>");
								}
							}
							?>
							</tbody>
						</table>
					</div>
				</div>
            </div>
    	</div>
<?php }?>
<script src="bootstrap/lib/jquery-1.11.1.min.js"></script>
<script src="bootstrap/js/bootstrap.js"></script>
<script src="bootstrap/dist/jquery.bootgrid.js"></script>
<script src="bootstrap/dist/jquery.bootgrid.fa.js"></script>
<script src="js/jquery.redirect.js"></script>
<script>
	$(function() {
		$(window).scroll(function() {
			var scroll = $(window).scrollTop();
			if (scroll >= 100) {
			$("#lateral").addClass("entra");
			} else {
			$("#lateral").removeClass("entra");
			}
		});
	});

	$(function() {
		function init() {
			$("#grid").bootgrid({
				formatters: {
					"link": function(column, row) {
						return "<a href=\"#\">" + column.id + ": " + row.id + "</a>";
					}
				},
				rowCount: [-1, 10, 50, 75]
			});
		}

		init();

		$("#append").on("click", function() {
			$.redirect("procExpDigital.php", "POST");
		});

		$("#agregar").on("click", function() {
			$.redirect("procExpDigital.php", "POST");
		});
			
		$("#edit").on("click", function() {
			if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
			{
				var codExpediente = $.trim($("#grid").bootgrid("getSelectedRows"));
				$.redirect("procExpDigital.php", {UMOJN: codExpediente}, "POST");
			}
		});

		$("#modificar").on("click", function() {
			if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
			{
				var codExpediente = $.trim($("#grid").bootgrid("getSelectedRows"));
				$.redirect("procExpDigital.php", {UMOJN: codExpediente}, "POST");
			}
		});

		$("#generarqr").on("click", function() {
			if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
			{
				var codExpediente = $.trim($("#grid").bootgrid("getSelectedRows"));
				$.redirect("tskGenerarQR.php", {UMOJN: codExpediente}, "POST", "_blank");
			}
		});

		$("#remove").on("click", function() {
			if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
			{
				var codExpediente = $.trim($("#grid").bootgrid("getSelectedRows"));
				$.redirect("gridExpDigital.php", {UMOJN: codExpediente}, "POST");
			}
		});

		$("#borrar").on("click", function() {
			if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
			{
				var codExpediente = $.trim($("#grid").bootgrid("getSelectedRows"));
				$.redirect("gridExpDigital.php", {UMOJN: msEstudiante}, "POST");
			}
		});

		$("#addqr").on("click", function() {
			if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
			{
				var codExpediente = $.trim($("#grid").bootgrid("getSelectedRows"));
				$.redirect("tskGenerarQR.php", {UMOJN: codExpediente}, "POST", "_blank");
			}
		});
	});
</script>
</body>
</html>