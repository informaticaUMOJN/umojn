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
	require_once ("funciones/fxAvanceProg.php");
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
		$mbSupervisor = fxVerificaSupervisor();
		$mbPermisoUsuario = fxPermisoUsuario("procAvanceProg", $mbAgregar, $mbModificar, $mbBorrar, $mbAnular);
		
		if ($mbAdministrador == 0 and $mbPermisoUsuario == 0 and $mbSupervisor == 0)
		{ ?>
        <div class="container text-center">
        	<div id="DivContenido">
				<img src="imagenes/errordeacceso.png"/>
            </div>
        </div>
		<?php }
		else
		{
		if (isset($_POST["UMOJN"]))
            {
                fxBorrarAvanceProg($_POST["UMOJN"]);
				fxAgregarBitacora($_SESSION["gsUsuario"], "UMO170A", $_POST["UMOJN"], "", "Borrar", "");
            }
		?>
    	<div class="container">
        	<div id="DivContenido">
				<div id="lateral">
					<?php
						if ($mbAgregar == 1 or $mbAdministrador == 1 or $mbSupervisor == 1)
							echo('<label id="agregar" data-toggle="tooltip" data-placement="top" title="Agregar"><img src="imagenes/btnLateralAgregar.png" height="80%" style="cursor:pointer" /></label>');
						else
							echo('<label id="agregarDis" data-toggle="tooltip" data-placement="top" title="Agregar"><img src="imagenes/btnLateralAgregarDis.png" height="80%" style="cursor:default" /></label>');
							
						if ($mbModificar == 1 or $mbAdministrador == 1 or $mbSupervisor == 1)
							echo('<label id="modificar" data-toggle="tooltip" data-placement="top" title="Editar"><img src="imagenes/btnLateralEditar.png" height="80%" style="cursor:pointer" /></label>');
						else
							echo('<label id="modificarDis" data-toggle="tooltip" data-placement="top" title="Editar"><img src="imagenes/btnLateralEditarDis.png" height="80%" style="cursor:default" /></label>');
						
						if ($mbBorrar == 1 or $mbAdministrador == 1 or $mbSupervisor == 1)
							echo('<label id="borrar" data-toggle="tooltip" data-placement="top" title="Borrar"><img src="imagenes/btnLateralBorrar.png" height="80%" style="cursor:pointer" /></label>');
						else
							echo('<label id="borrarDis" data-toggle="tooltip" data-placement="top" title="Borrar"><img src="imagenes/btnLateralBorrarDis.png" height="80%" style="cursor:default" /></label>');

						echo('<label id="imprimir" data-toggle="tooltip" data-placement="top" title="Imprimir"><img src="imagenes/btnLateralImprimir.png" height="80%" style="cursor:pointer" /></label>');
					?>
				</div>

				<div class="row">
					<div class="col-md-12">
						<?php
							if ($mbAgregar == 1 or $mbAdministrador == 1 or $mbSupervisor == 1)
								echo('<button id="append" type="button" class="btn btn-primary">Agregar</button>');
							else
								echo('<button id="append" type="button" class="btn btn-primary" disabled>Agregar</button>');
								
							if ($mbModificar == 1 or $mbAdministrador == 1 or $mbSupervisor == 1)
								echo('<button id="edit" type="button" class="btn btn-primary">Editar</button>');
							else
								echo('<button id="edit" type="button" class="btn btn-primary" disabled>Editar</button>');
								
							if ($mbBorrar == 1 or $mbAdministrador == 1 or $mbSupervisor == 1)
								echo('<button id="remove" type="button" class="btn btn-primary">Borrar</button>');
							else
								echo('<button id="remove" type="button" class="btn btn-primary" disabled>Borrar</button>');

							echo('<button id="print" type="button" class="btn btn-primary">Imprimir</button>');
						?>
						
						<table id="grid" class="table table-condensed table-hover table-striped" data-selection="true" data-multi-select="false" data-row-select="true" data-keep-selection="true" style="font-size:small">
							<thead>
								<tr>
								<th data-column-id="AVANCE_REL" data-identifier="true" data-align="left" data-width="10%">Avance</th>
									<th data-column-id="NOMBRE_100" data-align="left" data-width="27%">Docente</th>
									<th data-column-id="NOMBRE_060" data-align="left" data-width="27%">Asignatura</th>
									<th data-column-id="FECHA_170" data-align="left" data-width="10%">Fecha</th>
									<th data-column-id="ANNO_170" data-header-align="center" data-align="center" data-width="8%">Año</th>
									<th data-column-id="SEMESTRE_170" data-header-align="center" data-align="center" data-width="10%">Semestre</th>
									<th data-column-id="TURNO_170" data-header-align="center" data-align="center" data-width="8%">Turno</th>
								</tr>
							</thead>
							<tbody>
							<?php
								$mDatos = fxDevuelveAvanceProg(1);
								
								while ($mFila = $mDatos->fetch())
								{
									echo ("<tr>");
									echo ("<td>" . $mFila["AVANCE_REL"] . "</td>");
									echo ("<td>" . $mFila["NOMBRE_100"] . "</td>");
									echo ("<td>" . $mFila["NOMBRE_060"] . "</td>");
									$fecha = date_create_from_format('Y-m-d', $mFila["FECHA_170"]);
									echo ("<td>" . date_format($fecha, 'd-m-Y') . "</td>");
									echo ("<td>" . $mFila["ANNO_170"] . "</td>");
									echo ("<td>" . $mFila["SEMESTRE_170"] . "</td>");
									switch(intval($mFila["TURNO_170"]))
									{
										case 1:
											$msTurno = 'Diurno';
											break;
                                        case 2:
											$msTurno = 'Matutino';
											break;
                                        case 3:
											$msTurno = 'Vespertino';
											break;
                                        case 4:
											$msTurno = 'Nocturno';
											break;
                                        case 5:
											$msTurno = 'Sabatino';
											break;
                                        case 6:
											$msTurno = 'Dominical';
											break;
									}
									echo ("<td>" . $msTurno . "</td>");
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
<script src="bootstrap/dist/jquery.bootgrid.js"></script>
<script src="bootstrap/dist/jquery.bootgrid.fa.js"></script>
<script src="js/jquery.redirect.js"></script>
<script>
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
                $.redirect("procAvanceProg.php", "POST");
			});
  
			$("#remove").on("click", function() {
                if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
                {
                    var codAvance = $.trim($("#grid").bootgrid("getSelectedRows"));
                    $.redirect("gridAvanceProg.php", {UMOJN: codAvance}, "POST");
                }
			});
      			
            $("#edit").on("click", function() {
				if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
                {
                    var codAvance = $.trim($("#grid").bootgrid("getSelectedRows"));
                    $.redirect("procAvanceProg.php", {UMOJN: codAvance}, "POST");
                }
            });

			$("#print").on("click", function() {
				if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
				{
					var codAvance = $.trim($("#grid").bootgrid("getSelectedRows"));
					$.redirect("repAvance.php", {UMOJN: codAvance}, "POST", "_blank");
				}
			});

			$("#agregar").on("click", function() {
                $.redirect("procAvanceProg.php", "POST");
			});
  
			$("#borrar").on("click", function() {
                if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
                {
                    var codAvance = $.trim($("#grid").bootgrid("getSelectedRows"));
                    $.redirect("gridAvanceProg.php", {UMOJN: codAvance}, "POST");
                }
			});
      			
            $("#modificar").on("click", function() {
				if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
                {
                    var codAvance = $.trim($("#grid").bootgrid("getSelectedRows"));
                    $.redirect("procAvanceProg.php", {UMOJN: codAvance}, "POST");
                }
            });

			$("#imprimir").on("click", function() {
			if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
			{
				var codAvance = $.trim($("#grid").bootgrid("getSelectedRows"));
				$.redirect("repAvance.php", {UMOJN: codAvance}, "POST", "_blank");
			}
		});
        });
    </script>
</body>
</html>