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
    $m_cnx_MySQL = fxAbrirConexion();
	$mnRegistro = fxVerificaUsuario();
	
	if ($mnRegistro == 0)
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
		$mbPermisoUsuario = fxPermisoUsuario("dshMatricula", $mbAgregar, $mbModificar, $mbBorrar, $mbAnular);
		
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
		?>
    	<div class="container">
        	<div id="DivContenido">
                <div class = "row">
                    <div class="col-xs-12 col-md-11">
                        <div class="degradado"><strong>Matriculados</strong></div>
                    </div>
                </div>

                <div class="col-sm-12 offset-sm-none col-md-12 offset-md-3">
                    <div class = "form-group row">
                        <label for="cboAnno" class="col-sm-12 col-md-2 col-form-label">Año académico</label>
                        <div class="col-sm-12 col-md-2">
                            <select class="form-control" id="cboAnno" name="cboAnno">
                                <?php
                                    $msConsulta = "select distinct(ANNOLECTIVO_030) as ANNOLECTIVO_030 from UMO030A order by ANNOLECTIVO_030 desc";
                                    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
                                    $mDatos->execute();
                                    while ($mFila = $mDatos->fetch())
                                    {
                                        $msValor = rtrim($mFila["ANNOLECTIVO_030"]);
                                        $msTexto = rtrim($mFila["ANNOLECTIVO_030"]);

                                        echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
                                    }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class = "form-group row">
                        <label for="cboSemestre" class="col-sm-12 col-md-2 col-form-label">Semestre académico</label>
                        <div class="col-sm-12 col-md-2">
                            <select class="form-control" id="cboSemestre" name="cboSemestre">
                                <option value='1'>1</option>
                                <option value='2'>2</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-auto offset-sm-none col-md-8 offset-md-2">
                            <button id="print" type="button" class="btn btn-primary">Aceptar</button>
                        </div>
                    </div>
                </div>
            </div>
    	</div>
<?php }} ?>
<script src="bootstrap/lib/jquery-1.11.1.min.js"></script>
<script src="js/jquery.redirect.js"></script>
<script>
    $("#print").on("click", function() {
        var anno = $("#cboAnno").val();
        var semestre = $("#cboSemestre").val();

        $.redirect("https://metabase.umojn.edu.ni/public/dashboard/403a981f-6b4b-44ed-a162-45919377ca2c", {annolectivo: anno, semestreacademico: semestre}, "GET", "_blank");
    });
</script>
</body>
</html>