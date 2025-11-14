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
	require_once ("funciones/fxMatriculaPosgrado.php");
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
		$mbPermisoUsuario = fxPermisoUsuario("procMatriculaPos", $mbAgregar, $mbModificar, $mbBorrar, $mbAnular);
		
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
						echo('<label id="editar" data-toggle="tooltip" data-placement="top" title="Editar"><img src="imagenes/btnLateralEditar.png" height="80%" style="cursor:pointer" /></label>');
						else
						echo('<label id="editarDis" data-toggle="tooltip" data-placement="top" title="Editar"><img src="imagenes/btnLateralEditarDis.png" height="80%" style="cursor:default" /></label>');

						echo('<label id="imprimir" data-toggle="tooltip" data-placement="top" title="Hoja de matricula"><img src="imagenes/btnLateralImprimir.png" height="80%" style="cursor:pointer" /></label>');
					?>
				</div>
				<div class="row">
					<div class="col-md-12">
						<?php
							if ($mbAgregar == 1 or $mbAdministrador == 1)
								echo('<button id="append" type="button" class="btn btn-primary">Agregar</button>');
							else
								echo('<button id="appendDis" type="button" class="btn btn-primary" disabled>Agregar</button>');
								
							if ($mbModificar == 1 or $mbAdministrador == 1)
								echo('<button id="edit" type="button" class="btn btn-primary">Editar</button>');
							else
								echo('<button id="editDis" type="button" class="btn btn-primary" disabled>Editar</button>');

							echo('<button id="print" type="button" class="btn btn-primary">Hoja de Matrícula</button>');
						?>
						
						<table id="grid" class="table table-condensed table-hover table-striped" data-selection="true" data-multi-select="false" data-row-select="true" data-keep-selection="true" style="font-size:small">
							<thead>
								<tr>
									<th data-column-id="MATRICULAPOS_REL" data-order="desc" data-identifier="true" data-align="left" data-header-align="left" data-width="10%">Matrícula</th>
									<th data-column-id="ESTUDIANTE" data-order="desc" data-align="left" data-header-align="left" data-width="30%">Nombre del Estudiante</th>
									<th data-column-id="NOMBRE_040" data-align="left" data-header-align="left" data-width="36%">Carrera</th>
									<th data-column-id="FECHA_260" data-align="center" data-header-align="center" data-width="15%">Fecha</th>
									<th data-column-id="ESTADO_260" data-align="center" data-header-align="center" data-width="9%">Estado</th>
								</tr>
							</thead>
							<tbody>
							<?php
								$mDatos = fxDevuelveMatriculaPos(1);

								while ($mFila = $mDatos->fetch())
								{
									$msEstudiante = $mFila["APELLIDO1_250"];

									if (trim($mFila["APELLIDO2_250"]) != "")
										$msEstudiante .= ' ' . $mFila["APELLIDO2_250"];
									
									$msEstudiante .= ', ' . $mFila["NOMBRE1_250"];

									if (trim($mFila["NOMBRE2_250"]) != "")
										$msEstudiante .= ' ' . $mFila["NOMBRE2_250"];

									echo ("<tr>");
									echo ("<td>" . $mFila["MATRICULAPOS_REL"] . "</td>");
									echo ("<td>" . $msEstudiante . "</td>");
									echo ("<td>" . $mFila["NOMBRE_040"] . "</td>");
									$fecha = date_create_from_format('Y-m-d', $mFila["FECHA_260"]);
									echo ("<td>" . date_format($fecha, 'd-m-Y') . "</td>");
									if ($mFila["ESTADO_260"]==0)
										echo ("<td>Activo</td>");
									else{
										if ($mFila["ESTADO_260"]==1)
											echo ("<td>Inactivo</td>");
										else
											echo ("<td>Pre-matrícula</td>");
									}
									echo ("</tr>");
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
<script src="bootstrap/js/moderniz.2.8.1.js"></script>
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
			$.redirect("procMatriculaPos.php", {mAccion: 0, mEstudiante: ''}, "POST");
		});

		$("#agregar").on("click", function() {
			$.redirect("procMatriculaPos.php", {mAccion: 0, mEstudiante: ''}, "POST");
		});
		
		$("#edit").on("click", function() {
			if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
			{
				var msCodigo = $.trim($("#grid").bootgrid("getSelectedRows"));
				$.redirect("procMatriculaPos.php", {mAccion: 0, mCodigo: msCodigo}, "POST");
			}
		});

		$("#editar").on("click", function() {
			if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
			{
				var msCodigo = $.trim($("#grid").bootgrid("getSelectedRows"));
				$.redirect("procMatriculaPos.php", {mAccion: 0, mCodigo: msCodigo}, "POST");
			}
		});

		$("#print").on("click", function() {
			if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
			{
				var msCodigo = $.trim($("#grid").bootgrid("getSelectedRows"));
				$.redirect("repHojaMatriculaPos.php", {UMOJN: msCodigo}, "POST", "_blank");
			}
		});

		$("#imprimir").on("click", function() {
			if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
			{
				var msCodigo = $.trim($("#grid").bootgrid("getSelectedRows"));
				$.redirect("repHojaMatriculaPos.php", {UMOJN: msCodigo}, "POST", "_blank");
			}
		});
	});
</script>
</body>
</html>