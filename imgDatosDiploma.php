<?php
require_once ("funciones/fxGeneral.php");
require_once ("funciones/fxEstudiantes.php");
require_once ("funciones/fxNumerosLetras.php");

if (isset($_POST["UMOJN"]))
{
    $msExpediente = $_POST["UMOJN"];

    $m_cnx_MySQL = fxAbrirConexion();

    //Autoridades
    $msConsulta = "select * from UMO001A";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute();
    $mFila = $mDatos->fetch();
    $msRector = $mFila["RECTOR_001"];
    $msSecretario = $mFila["SECRETARIO_001"];
    $msRegistro = $mFila["REGISTRO_001"];
    $msRectorFM = $mFila["RECTOR_FM_001"];
    $msSecretarioFM = $mFila["SECRETARIO_FM_001"];
    $msRegistroFM = $mFila["REGISTRO_FM_001"];

    $msConsulta = "select CARNET_REL, REGISTRO_002, TOMO_002, FOLIO_002, FECHA_001, FECHADEFENSA_001 from UMO002B join UMO001B on UMO002B.EXPDIGITAL_REL = UMO001B.EXPDIGITAL_REL where UMO002B.EXPDIGITAL_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msExpediente]);
    
    while($mFila = $mDatos->fetch())
    {
        $msCarnet = $mFila["CARNET_REL"];
        $mnRegistro = $mFila["REGISTRO_002"];
        $mnTomo = $mFila["TOMO_002"];
        $mnFolio = $mFila["FOLIO_002"];
        $mdFechaRegistro = $mFila["FECHA_001"];
        $mdFechaDefensa = $mFila["FECHADEFENSA_001"];

        $msConsulta = "select ESTUDIANTE_REL, NOMBRE1_010, NOMBRE2_010, APELLIDO1_010, APELLIDO2_010, PAIS_010, CEDULA_010, NOMBRE_040, GRADO_050 ";
        $msConsulta .= "from UMO010A, UMO040A, UMO050A where UMO010A.CARRERA_REL = UMO040A.CARRERA_REL and UMO010A.CARRERA_REL = UMO050A.CARRERA_REL and CARNET_010 = ?";
        $mEstudiante = $m_cnx_MySQL->prepare($msConsulta);
        $mEstudiante->execute([$msCarnet]);
        $mnRegistros = $mEstudiante->rowCount();

        if ($mnRegistros > 0)
        {
            $mrEstudiante = $mEstudiante->fetch();
            $msEstudiante = $mrEstudiante["ESTUDIANTE_REL"];
            $msNombre = trim($mrEstudiante["NOMBRE1_010"]);
            if (trim($mrEstudiante["NOMBRE2_010"]) != "")
                $msNombre .= " " . trim($mrEstudiante["NOMBRE2_010"]);
            $msNombre .= " " . trim($mrEstudiante["APELLIDO1_010"]);
            if (trim($mrEstudiante["APELLIDO2_010"]) != "")
                $msNombre .= " " . trim($mrEstudiante["APELLIDO2_010"]);
            $msPais = $mrEstudiante["PAIS_010"];
            $msCedula = $mrEstudiante["CEDULA_010"];
            $msCarrera = $mrEstudiante["NOMBRE_040"];
            $msGrado = $mrEstudiante["GRADO_050"];

            // Define las dimensiones de la imagen en pixeles
            $ancho = 700;
            $alto = 700;
            $y = 60;

            // Crea una nueva imagen en blanco
            $imagen = imagecreatetruecolor($ancho, $alto);

            // Asigna colores
            $blanco = imagecolorallocate($imagen, 255, 255, 255);
            $negro = imagecolorallocate($imagen, 0, 0, 0);

            //Rectángulo
            imagefilledrectangle($imagen, 0, 0, 700, 700, $negro);
            imagefilledrectangle($imagen, 2, 2, 696, 696, $blanco);

            //Logotipo
            $msLogotipo = imagecreatefrompng("imagenes/logosinfondo.png");
            $mnAlto = imagesy($msLogotipo);
            $mnAncho = imagesx($msLogotipo);
            imagecopyresampled($imagen, $msLogotipo, 10, 20, 0, 0, 90, 90, $mnAncho, $mnAlto);

            // Define la fuente y el texto
            $arialNormal = 'fonts/arial.ttf'; // Reemplaza con la ruta a una fuente TTF válida
            $arialBold = 'fonts/arialbd.ttf';

            $texto = 'UNIVERSIDAD DE MEDICINA ORIENTAL';
            $bbox = imagettfbbox(15, 0, $arialNormal, $texto);
            $textWidth  = $bbox[2] - $bbox[0];
            $x = ($ancho - $textWidth) / 2;
            imagettftext($imagen, 15, 0, $x, $y, $negro, $arialNormal, $texto);

            $y += 20;
            $texto = 'JAPON-NICARAGUA';
            $bbox = imagettfbbox(15, 0, $arialNormal, $texto);
            $textWidth  = $bbox[2] - $bbox[0];
            $x = ($ancho - $textWidth) / 2;
            imagettftext($imagen, 15, 0, $x, $y, $negro, $arialNormal, $texto);

            $y += 70;
            $texto = 'POR CUANTO:';
            $bbox = imagettfbbox(12, 0, $arialBold, $texto);
            $textWidth  = $bbox[2] - $bbox[0];
            $x = ($ancho - $textWidth) / 2;
            imagettftext($imagen, 12, 0, $x, $y, $negro, $arialBold, $texto);

            $y += 30;
            $bbox = imagettfbbox(13, 0, $arialBold, $msNombre);
            $textWidth  = $bbox[2] - $bbox[0];
            $x = ($ancho - $textWidth) / 2;
            imagettftext($imagen, 13, 0, $x, $y, $negro, $arialBold, $msNombre);

            $y += 30;
            $msConcepto = "Natural de " . $msPais . ", con documento de identidad " . $msCedula . " ha aprobado en el mes de " . fxFechaLetras($mdFechaDefensa, 0);
            $msConcepto .= " los estudios y requisitos académicos, conforme el Plan de Estudio de la Carrera de " . $msCarrera . ".";
            imagettftext_multiline($imagen, 12, 0, 20, $y, $negro, $arialNormal, $msConcepto, 660);

            $y += 90;
            $texto = 'POR TANTO:';
            $bbox = imagettfbbox(12, 0, $arialBold, $texto);
            $textWidth  = $bbox[2] - $bbox[0];
            $x = ($ancho - $textWidth) / 2;
            imagettftext($imagen, 12, 0, $x, $y, $negro, $arialBold, $texto);

            $y += 20;
            $texto = 'Le extiende el título de';
            $bbox = imagettfbbox(12, 0, $arialNormal, $texto);
            $textWidth  = $bbox[2] - $bbox[0];
            $x = ($ancho - $textWidth) / 2;
            imagettftext($imagen, 12, 0, $x, $y, $negro, $arialNormal, $texto);

            $y += 20;
            $bbox = imagettfbbox(13, 0, $arialBold, $msGrado);
            $textWidth  = $bbox[2] - $bbox[0];
            $x = ($ancho - $textWidth) / 2;
            imagettftext($imagen, 13, 0, $x, $y, $negro, $arialBold, $msGrado);

            $y += 20;
            $texto = 'Con las facultades y prerrogativas que legalmente le corresponden.';
            $bbox = imagettfbbox(12, 0, $arialNormal, $texto);
            $textWidth  = $bbox[2] - $bbox[0];
            $x = ($ancho - $textWidth) / 2;
            imagettftext($imagen, 12, 0, $x, $y, $negro, $arialNormal, $texto);

            $y += 40;
            $texto = 'Dado en la ciudad de Managua, República de Nicaragua, a los ' . fxFechaLetras($mdFechaRegistro, 1) . '.';
            imagettftext_multiline($imagen, 12, 0, 20, $y, $negro, $arialNormal, $texto, 670);

            $y += 80;
            if ($msRectorFM == 'M')
                $texto = "Rector: " . $msRector;
            else
                $texto = "Rectora: " . $msRector;
            imagettftext($imagen, 12, 0, 20, $y, $negro, $arialNormal, $texto);

            $y += 20;
            if ($msSecretarioFM == 'M')
                $texto = "Secretario general: " . $msSecretario;
            else
                $texto = "Secretaria general: " . $msSecretario;
            imagettftext($imagen, 12, 0, 20, $y, $negro, $arialNormal, $texto);

            $y += 40;
            $texto = 'Registrado con el Número ' . $mnRegistro . ', Folio ' . $mnFolio . ', Tomo ' . $mnTomo . ' del registro de Títulos. En la ciudad de Managua, el día ' . fxFechaLarga($mdFechaRegistro) . '.';
            imagettftext_multiline($imagen, 12, 0, 20, $y, $negro, $arialNormal, $texto, 670);

            $y += 50;
            if ($msRegistroFM == 'M')
                $texto = "Director de Registro Académico: " . $msRegistro;
            else
                $texto = "Directora de Registro Académico: " . $msRegistro;
            imagettftext($imagen, 12, 0, 20, $y, $negro, $arialNormal, $texto);

            // Define el nombre del archivo donde se guardará la imagen
            $archivo = $msEstudiante . '.png';
            $ruta = 'estudiantes/' . $msEstudiante;
            $ruta_archivo = 'estudiantes/' . $msEstudiante . '/' . $msEstudiante . '.png';

            if (!file_exists($ruta)) {
                mkdir($ruta, 0777, true);
            }

            // Guarda la imagen como un archivo PNG en el servidor
            // La función imagepng() ahora acepta un segundo argumento: el nombre del archivo
            imagepng($imagen, $ruta_archivo);
            echo('<img src="' . $ruta_archivo . '" style="margin: 10px" width="500px" />');

            $msConsulta = "select ARCHIVO_REL from UMO011A where ESTUDIANTE_REL = ? and TIPO_011 = 11";
            $mAuxiliar = $m_cnx_MySQL->prepare($msConsulta);
            $mAuxiliar->execute([$msEstudiante]);
            $mnConteo = $mAuxiliar->rowCount();

            if ($mnConteo == 0)
                fxGuardarDetDocumento($msEstudiante, $archivo, 11, 'Datos generales del título', $ruta_archivo);

            // Libera la memoria
            imagedestroy($imagen);
        }
        else
        {
            echo("<h1>No hay datos para mostrar</h1>");
        }
    }
}

/**
 * Dibuja un texto multilínea con una fuente TrueType
 *
 * @param resource $image El recurso de la imagen
 * @param int $font_size El tamaño de la fuente en puntos
 * @param int $angle El ángulo de rotación (generalmente 0)
 * @param int $x La coordenada X inicial
 * @param int $y La coordenada Y inicial
 * @param int $color El color del texto
 * @param string $font_file La ruta al archivo de la fuente .ttf
 * @param string $text El texto a dibujar
 * @param int $max_width El ancho máximo permitido para el texto
 */
function imagettftext_multiline($image, $font_size, $angle, $x, $y, $color, $font_file, $text, $max_width) {
    // Dividir el texto en palabras
    $palabras = explode(' ', $text);
    $linea_actual = '';
    $lineas = [];

    foreach ($palabras as $palabra) {
        // Concatenar la palabra a la línea actual
        $test_linea = ($linea_actual ? $linea_actual . ' ' : '') . $palabra;

        // Obtener el ancho del cuadro delimitador de la línea de prueba
        $caja = imagettfbbox($font_size, $angle, $font_file, $test_linea);
        $ancho = abs($caja[2] - $caja[0]); // Ancho total del texto

        if ($ancho <= $max_width) {
            $linea_actual = $test_linea;
        } else {
            // Si la línea excede el ancho, la guardamos y empezamos una nueva
            $lineas[] = $linea_actual;
            $linea_actual = $palabra;
        }
    }
    // Agregar la última línea
    if ($linea_actual !== '') {
        $lineas[] = $linea_actual;
    }
    
    // Altura de una línea de texto
    $caja_altura = imagettfbbox($font_size, $angle, $font_file, 'A');
    $altura_linea = abs($caja_altura[1] - $caja_altura[7]);

    // Dibujar cada línea en la imagen
    foreach ($lineas as $i => $linea) {
        imagettftext($image, $font_size, $angle, $x, $y + ($i * $altura_linea * 1.5), $color, $font_file, $linea);
    }
}

function fxFechaLarga($mdFecha)
{
    $FechaDividida = explode("-", $mdFecha);
    
    $Anno = $FechaDividida[0];
    $Mes = $FechaDividida[1];
    $Dia = $FechaDividida[2];
    
    switch ($Mes)
    {
        case "01":
            $NombreMes = "enero";
            break;
        case "02":
            $NombreMes = "febrero";
            break;
        case "03":
            $NombreMes = "marzo";
            break;
        case "04":
            $NombreMes = "abril";
            break;
        case "05":
            $NombreMes = "mayo";
            break;
        case "06":
            $NombreMes = "junio";
            break;
        case "07":
            $NombreMes = "julio";
            break;
        case "08":
            $NombreMes = "agosto";
            break;
        case "09":
            $NombreMes = "septiembre";
            break;
        case "10":
            $NombreMes = "octubre";
            break;
        case "11":
            $NombreMes = "noviembre";
            break;
        case "12":
            $NombreMes = "diciembre";
            break;
    }
    return ($Dia . " de " . $NombreMes . " de " . $Anno);
}

function fxFechaLetras($mdFecha, $mbDia)
{
    $FechaDividida = explode("-", $mdFecha);
    
    $Anno = $FechaDividida[0];
    $Mes = $FechaDividida[1];
    $Dia = $FechaDividida[2];
    
    switch ($Mes)
    {
        case "01":
            $NombreMes = "enero";
            break;
        case "02":
            $NombreMes = "febrero";
            break;
        case "03":
            $NombreMes = "marzo";
            break;
        case "04":
            $NombreMes = "abril";
            break;
        case "05":
            $NombreMes = "mayo";
            break;
        case "06":
            $NombreMes = "junio";
            break;
        case "07":
            $NombreMes = "julio";
            break;
        case "08":
            $NombreMes = "agosto";
            break;
        case "09":
            $NombreMes = "septiembre";
            break;
        case "10":
            $NombreMes = "octubre";
            break;
        case "11":
            $NombreMes = "noviembre";
            break;
        case "12":
            $NombreMes = "diciembre";
            break;
    }
    if ($mbDia == 1)
        return (fxNumerosLetras($Dia) . " días de " . $NombreMes . " de " . fxNumerosLetras($Anno));
    else
        return ($NombreMes . " de " . fxNumerosLetras($Anno));
}
?>