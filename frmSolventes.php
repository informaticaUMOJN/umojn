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
	require_once ("funciones/fxCarreras.php");
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
		$Administrador = fxVerificaAdministrador();
		$mbPermisoUsuario = fxPermisoUsuario("repSolventes", $mbAgregar, $mbModificar, $mbBorrar, $mbAnular);
		
		if ($Administrador == 0 and $mbPermisoUsuario == 0)
		{?>
        <div class="container text-center">
            <div id="DivContenido">
                <img src="imagenes/errordeacceso.png"/>
            </div>
        </div>
		<?php }
		else
			{
				$FechaFin = date("Y-m-d", time());
			?>
			<div class="container">
            <div id="DivContenido">
			<div class = "row">
				<div class="col-xs-12 col-md-11">
					<div class="degradado"><strong>Estudiantes solventes</strong></div>
				</div>
			</div>

            <div class="row">
            	<div class="col-sm-12 offset-sm-0 col-md-10 offset-md-2">
                    <form name="frmSolventes" action="repSolventes.php" target="_blank" method="post">
                        <div class="form-group row">
                            <label for="cboCarrera" class="col-sm-12 col-md-2 col-form-label">Carrera</label>
                            <div class="col-sm-12 col-md-6">
                                <select class="form-control" id="cboCarrera" name="cboCarrera">
                                    <?php
				    					$mDatos = fxDevuelveCarrera(1);
                                        while ($Fila = $mDatos->fetch())
                                        {
                                            $Valor = rtrim($Fila["CARRERA_REL"]);
                                            $Texto = rtrim($Fila["NOMBRE_040"]);

                                            echo("<option value='" . $Valor . "'>" . $Texto . "</option>");
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>

						<div class = "form-group row">
							<label for="dtpFecha" class="col-sm-12 col-md-2 form-label">Fecha de corte</label>
							<div class="col-sm-12 col-md-3">
							<?php echo('<input type="date" class="form-control" id="dtpFecha" name="dtpFecha" value="' . date('Y-m-d') . '" />'); ?>
							</div>
						</div>
                                               
                        <div class = "row">
                            <div class="col-auto offset-sm-0 col-md-2 offset-md-2">
                                <input type="submit" id="Imprimir" name="Imprimir" value="Imprimir" class="btn btn-primary" />
                            </div>
                        </div>
                    </form>			
			<?php	}
			}
		?>
        	</div>
        </div>
		</div>
        </div>
</body>
</html>