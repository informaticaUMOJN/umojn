<!DOCTYPE html>
<html lang="es-NI">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <script>
    function guardar()
    {
        objetivo = document.getElementById('txtObjetivo').value;
        formulario = opener.document.getElementById('procSyllabus');
        formulario.txtObjGral.value = objetivo;
        close();
    }
    </script>
</head>
<body>
    <input type="text" name="txtObjetivo" id="txtObjetivo" value="">
    <input type="button" value="Aceptar" onclick="guardar()">
</body>
</html>