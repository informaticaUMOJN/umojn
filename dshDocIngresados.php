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
		$mbPermisoUsuario = fxPermisoUsuario("dshDocIngresados", $mbAgregar, $mbModificar, $mbBorrar, $mbAnular);
		
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
                        <div class="degradado"><strong>Documentos ingresados</strong></div>
                    </div>
                </div>

                <div class="col-sm-12 offset-sm-none col-md-12 offset-md-3">
                    <div class = "form-group row">
                        <label for="cboAnno" class="col-sm-12 col-md-2 col-form-label">Año de ingreso</label>
                        <div class="col-sm-12 col-md-2">
                            <select class="form-control" id="cboAnno" name="cboAnno">
                                <?php
                                    $msConsulta = "select distinct(datestamp_year) as ANNO from eprint order by datestamp_year desc";
                                    $mDatos = $m_cnx_Svr2->prepare($msConsulta);
                                    $mDatos->execute();
                                    while ($mFila = $mDatos->fetch())
                                    {
                                        $msValor = rtrim($mFila["ANNO"]);
                                        $msTexto = rtrim($mFila["ANNO"]);

                                        echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
                                    }
                                ?>
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

        $.redirect("http://37.60.233.136:3000/public/dashboard/264ad75c-1537-4960-b146-d185584f7147", {anno: anno}, "GET", "_blank");
    });
</script>
</body>
</html>