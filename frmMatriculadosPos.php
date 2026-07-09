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
    $m_cnx_MySQL = fxAbrirConexion();
	$Registro = fxVerificaUsuario();
	
	if ($Registro == 0)
	{
?>
        <div class="container text-center">
            <div id="DivContenido">
                <img src="imagenes/errordeacceso.png" />
            </div>
        </div>
<?php 
    }
	else
	{
        $mbAdministrador = fxVerificaAdministrador();
        $mbSupervisor = fxVerificaSupervisor();
		$mbPermisoUsuario = fxPermisoUsuario("repMatriculadosPos");
		
		if ($mbAdministrador == 0 and $mbPermisoUsuario == 0 and $mbSupervisor == 0)
		{
        ?>
            <div class="container text-center">
                <div id="DivContenido">
                    <img src="imagenes/errordeacceso.png" />
                </div>
            </div>
        <?php   
        }
		else
		{
            $msCarrera = "";
            $mnCohorte = 0;
        }
    }
?>
<div class="container text-left">
    <div id="DivContenido">
        <div class = "row">
            <div class="col-xs-12 col-md-11">
                <div class="degradado"><strong>Estudiantes matriculados</strong></div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 offset-sm-none col-md-12 offset-md-1">
                <form id="frmMatriculados" name="frmMatriculados">
                    <div class="form-group row">
                        <label for="cboCarrera" class="col-sm-12 col-md-2 col-form-label">Carrera</label>
                        <div class="col-sm-12 col-md-6">
                            <?php
                                echo('<select class="form-control" id="cboCarrera" name="cboCarrera">');

                                $msConsulta = "select CARRERA_REL, NOMBRE_040 from UMO040A where POSGRADO_040 = 1";
                                $mDatos = $m_cnx_MySQL->prepare($msConsulta);
                                $mDatos->execute();

                                while ($mFila = $mDatos->fetch())
                                {
                                    $mValor = rtrim($mFila["CARRERA_REL"]);
                                    $mTexto = rtrim($mFila["NOMBRE_040"]);

                                    if ($msCarrera == "")
                                        $msCarrera = $mValor;
                                    
                                    echo("<option value='" . $mValor . "' selected>" . $mTexto . "</option>");
                                }
                                echo("</select>");
                            ?>
                        </div>
                    </div>
                    
                    <div class = "form-group row">
							<label for="txnCohorte" class="col-sm-12 col-md-2 col-form-label">Cohorte</label>
							<div class="col-sm-12 col-md-2">
								<?php
									echo('<input type="number" style="text-align:right" class="form-control" id="txnCohorte" name="txnCohorte" value="' . $mnCohorte . '" />');
								?>
							</div>
						</div>

                    <div class="row">
                        <div class="col-auto offset-sm-none col-md-8 offset-md-2">
                            <input type="button" id="Aceptar" name="Aceptar" value="Aceptar" class="btn btn-primary" onclick="imprimir()"/>
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
function imprimir() {
    var cohorte = document.getElementById('txnCohorte').value;
    var carrera = document.getElementById('cboCarrera').value;

    $.redirect("repMatriculadosPos.php", {msCarrera: carrera, mnCohorte: cohorte}, "POST", "_blank");
}
</script>