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
	require_once ("funciones/fxCobros.php");
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
		$mbPermisoUsuario = fxPermisoUsuario("catCobros", $mbAgregar, $mbModificar, $mbBorrar, $mbAnular);
		
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
		if (isset($_POST["UMOJN"]))
            {
                fxBorrarCobros($_POST["UMOJN"]);
				fxAgregarBitacora($_SESSION["gsUsuario"], "UMO140A", $_POST["UMOJN"], "", "Borrar", "");
            }
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
						
						if ($mbBorrar == 1 or $mbAdministrador == 1)
							echo('<label id="borrar" data-toggle="tooltip" data-placement="top" title="Borrar"><img src="imagenes/btnLateralBorrar.png" height="80%" style="cursor:pointer" /></label>');
						else
							echo('<label id="borrarDis" data-toggle="tooltip" data-placement="top" title="Borrar"><img src="imagenes/btnLateralBorrarDis.png" height="80%" style="cursor:default" /></label>');
					?>
				</div>
				<div class="row">
					<div class="col-md-12">
						 <div class="easyui-tabs" style="width:100%; height:auto;">
                          
						<?php
							if ($mbAgregar == 1 or $mbAdministrador == 1)
								echo('<button id="append" type="button" class="btn btn-primary">Agregar</button>');
							else
								echo('<button id="append" type="button" class="btn btn-primary" disabled>Agregar</button>');
								
							if ($mbModificar == 1 or $mbAdministrador == 1)
								echo('<button id="edit" type="button" class="btn btn-primary">Editar</button>');
							else
								echo('<button id="edit" type="button" class="btn btn-primary" disabled>Editar</button>');
								
							if ($mbBorrar == 1 or $mbAdministrador == 1)
								echo('<button id="remove" type="button" class="btn btn-primary">Borrar</button>');
							else
								echo('<button id="remove" type="button" class="btn btn-primary" disabled>Borrar</button>');
						?>
						
						<table id="grid" class="table table-condensed table-hover table-striped" data-selection="true" data-multi-select="false" data-row-select="true" data-keep-selection="true"  style="font-size:small">
							<thead>
								<tr>
									<th data-column-id="COBRO_REL" data-identifier="true" data-visible="false" data-align="left" data-width="10%">Cobro</th>
									<th data-column-id="CARRERA_REL" data-align="left" data-width="28%" >Nombre</th>
									<th data-column-id="DESC_130" data-align="left" data-width="27%">Descripcion</th>
									<th data-column-id="VENCIMIENTO_130" data-align="left"  data-width="12%">Vencimiento</th>
									<th data-column-id="carrera_x" data-align="center" data-width="8%">Carrera</th>
									<th data-column-id="curso_x" data-align="center" data-width="8%">Curso</th>
									<th data-column-id="ACTIVO_130" data-align="left" data-width="10%">Activo</th>
								</tr>
							</thead>
							<tbody>
							<?php
								$mDatos = fxDevuelveCobros(1);
								while ($mFila = $mDatos->fetch())
								{
									echo ("<tr>");
								    echo ("<td>" . htmlspecialchars($mFila["COBRO_REL"]) . "</td>");
           							echo ("<td>" . htmlspecialchars($mFila["NOMBRE_PROGRAMA"]) . "</td>");
            					    echo ("<td>" . htmlspecialchars($mFila["DESC_130"]) . "</td>");
            						echo ("<td>" . htmlspecialchars($mFila["VENCIMIENTO_130"]) . "</td>");
									echo ("<td>" . ($mFila["TIPO_PROGRAMA"] === 'Carrera' ? 'X' : '') . "</td>");
									echo ("<td>" . ($mFila["TIPO_PROGRAMA"] === 'Curso' ? 'X' : '') . "</td>");
									echo ("<td>" . htmlspecialchars($mFila["ACTIVO_130"]) . "</td>");
									echo ("</tr>");
								}
							}
							?>
							</tbody>
						</table>
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
                $.redirect("catCobros.php", "POST");
			});
  
			$("#remove").on("click", function() {
                if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
                { 
                    var codCobros = $.trim($("#grid").bootgrid("getSelectedRows"));
                    $.redirect("gridCobros.php", {UMOJN: codCobros}, "POST");
                }
			});
      			
            $("#edit").on("click", function() {
				if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
                {
                    var codCobros = $.trim($("#grid").bootgrid("getSelectedRows"));
                    $.redirect("catCobros.php", {UMOJN: codCobros}, "POST");
                }
            });

			$("#agregar").on("click", function() {
                $.redirect("catCobros.php", "POST");
			});
  
			$("#borrar").on("click", function() {
                if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
                {
                    var codCobros = $.trim($("#grid").bootgrid("getSelectedRows"));
                    $.redirect("gridPagos.php", {UMOJN: codCobros}, "POST");
                }
			});
      			
            $("#modificar").on("click", function() {
				if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
                {
                    var codCobros = $.trim($("#grid").bootgrid("getSelectedRows"));
                    $.redirect("catCobros.php", {UMOJN: codCobros}, "POST");
                }
            });
        });
		$(document).ready(function(){
        $("#grid th[data-column-id='COBRO_REL'], #grid td:nth-child(1)").hide();
   });
    </script>
</body>
</html>