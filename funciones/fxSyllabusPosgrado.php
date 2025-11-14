<?php
function fxGuardarSyllabusPos($msCursoPosgrado, $msDocente, $msPlanPosgrado, $mdFecha, $msCohorte, $mnTurno, $mnRegimen, $msMediacion, $msEjesValores)
{
    $m_cnx_MySQL = fxAbrirConexion();
    $msConsulta = "Select ifnull(mid(max(SYLLABUSPOS_REL), 4), 0) as Ultimo from UMO290A";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute();
    $mFila = $mDatos->fetch();
    $mnNumero = intval($mFila["Ultimo"]);
    $mnNumero += 1;
    $mnLongitud = strlen($mnNumero);
    $msCodigo = "SYP" . str_repeat("0", 7 - $mnLongitud) . trim($mnNumero);
    //La variable $msMediacion se relaciona con el campo RECOMENDACIONES_290 con el fin de no modificar la tabla
    $msConsulta = "insert into UMO290A (SYLLABUSPOS_REL, CURSOPOSGRADO_REL, DOCENTE_REL, PLANPOSGRADO_REL, FECHA_290, ";
    $msConsulta .= "COHORTE_290, TURNO_290, REGIMEN_290, RECOMENDACIONES_290, EJESVALORES_290, APROBADO_290, ACTIVO_290) ";
    $msConsulta .= "values(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo, $msCursoPosgrado, $msDocente, $msPlanPosgrado, $mdFecha, $msCohorte, $mnTurno, $mnRegimen, $msMediacion, $msEjesValores, 0, 1]);
    return ($msCodigo);
}

function fxModificarSyllabusPos($msCodigo, $msCursoPosgrado, $msDocente, $msPlanPosgrado, $mdFecha, $msCohorte, $mnTurno, $mnRegimen, $msMediacion, $msEjesValores)
{
    $m_cnx_MySQL = fxAbrirConexion();

    $msConsulta = "update UMO290A set CURSOPOSGRADO_REL = ?, DOCENTE_REL = ?, PLANPOSGRADO_REL = ?, FECHA_290 = ?, ";
    $msConsulta .= "COHORTE_290 = ?, TURNO_290 = ?, REGIMEN_290 = ?, RECOMENDACIONES_290 = ?, EJESVALORES_290 = ? where SYLLABUSPOS_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCursoPosgrado, $msDocente, $msPlanPosgrado, $mdFecha, $msCohorte, $mnTurno, $mnRegimen, $msMediacion, $msEjesValores, $msCodigo]);
}

function fxBorrarSyllabusPos($msCodigo)
{
    $m_cnx_MySQL = fxAbrirConexion();

    $msConsulta = "delete from UMO295A where SYLLABUSPOS_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo]);

    $msConsulta = "delete from UMO294A where SYLLABUSPOS_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo]);

    $msConsulta = "delete from UMO293A where SYLLABUSPOS_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo]);

    $msConsulta = "delete from UMO292A where SYLLABUSPOS_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo]);

    $msConsulta = "delete from UMO291A where SYLLABUSPOS_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo]);

    $msConsulta = "delete from UMO290A where SYLLABUSPOS_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo]);
}

function fxDevuelveSyllabusPos($mbLlenaGrid, $msDocente, $msCodigo)
{
    $m_cnx_MySQL = fxAbrirConexion();
    
    if ($mbLlenaGrid == 1)
    {
        if ($msDocente == "")
        {
            $msConsulta = "select SYLLABUSPOS_REL, NOMBRE_100, NOMBRE_240, FECHA_290, COHORTE_290, TURNO_290, REGIMEN_290, ACTIVO_290 ";
            $msConsulta .= "from UMO290A, UMO100A, UMO240A where UMO290A.DOCENTE_REL = UMO100A.DOCENTE_REL and ";
            $msConsulta .= "UMO290A.CURSOPOSGRADO_REL = UMO240A.CURSOPOSGRADO_REL order by SYLLABUSPOS_REL desc";
            $mDatos = $m_cnx_MySQL->prepare($msConsulta);
            $mDatos->execute();
        }
        else
        {
            $msConsulta = "select SYLLABUSPOS_REL, NOMBRE_100, NOMBRE_240, FECHA_290, COHORTE_290, TURNO_290, REGIMEN_290, ACTIVO_290 ";
            $msConsulta .= "from UMO290A, UMO100A, UMO240A where UMO290A.DOCENTE_REL = UMO100A.DOCENTE_REL and ";
            $msConsulta .= "UMO290A.CURSOPOSGRADO_REL = UMO240A.CURSOPOSGRADO_REL and UMO070A.DOCENTE_REL = ? order by SYLLABUSPOS_REL desc";
            $mDatos = $m_cnx_MySQL->prepare($msConsulta);
            $mDatos->execute([$msDocente]);
        }
    }
    else
    {
        $msConsulta = "select SYLLABUSPOS_REL, CURSOPOSGRADO_REL, DOCENTE_REL, PLANPOSGRADO_REL, FECHA_290, ";
        $msConsulta .= "COHORTE_290, TURNO_290, REGIMEN_290, RECOMENDACIONES_290, EJESVALORES_290, APROBADO_290, ACTIVO_290 ";
        $msConsulta .= "from UMO290A where SYLLABUSPOS_REL = ?";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$msCodigo]);
    }
    
    return $mDatos;
}

function fxBorrarDetObjGralPos($msCodigo)
{
    $m_cnx_MySQL = fxAbrirConexion();

    $msConsulta = "delete from UMO291A where SYLLABUSPOS_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo]);
}

function fxGuardarDetObjGralPos($msCodigo, $mnConsecutivo, $msObjetivo)
{
    $m_cnx_MySQL = fxAbrirConexion();

    $msConsulta = "insert into UMO291A (SYLLABUSPOS_REL, OBJETIVOGPOS_REL, TEXTO_291) values (?, ?, ?)";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo, $mnConsecutivo, $msObjetivo]);
}

function fxObtenerDetObjGralPos($msCodigo)
{
    $m_cnx_MySQL = fxAbrirConexion();

    $msConsulta = "select SYLLABUSPOS_REL, OBJETIVOGPOS_REL, TEXTO_291 from UMO291A where SYLLABUSPOS_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo]);
    return $mDatos;
}

function fxBorrarDetObjModPos($msCodigo)
{
    $m_cnx_MySQL = fxAbrirConexion();

    $msConsulta = "delete from UMO292A where SYLLABUSPOS_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo]);
}

function fxGuardarDetObjModPos($msCodigo, $mnConsecutivo, $msModulo, $msObjetivo)
{
    $m_cnx_MySQL = fxAbrirConexion();

    $msConsulta = "insert into UMO292A (SYLLABUSPOS_REL, OBJETIVOM_REL, MODULO_292, TEXTO_292) values (?, ?, ?, ?)";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo, $mnConsecutivo, $msModulo, $msObjetivo]);
}

function fxObtenerDetObjModPos($msCodigo)
{
    $m_cnx_MySQL = fxAbrirConexion();

    $msConsulta = "select SYLLABUSPOS_REL, OBJETIVOM_REL, MODULO_292, TEXTO_292 from UMO292A where SYLLABUSPOS_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo]);
    return $mDatos;
}

function fxBorrarDetObsDocentePos($msCodigo)
{
    $m_cnx_MySQL = fxAbrirConexion();

    $msConsulta = "delete from UMO294A where SYLLABUSPOS_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo]);
}

function fxGuardarDetObsDocentePos($msCodigo, $mnConsecutivo, $msObservacion)
{
    $m_cnx_MySQL = fxAbrirConexion();

    $msConsulta = "insert into UMO294A (SYLLABUSPOS_REL, OBSDOCENTEPOS_REL, TEXTO_294) values (?, ?, ?)";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo, $mnConsecutivo, $msObservacion]);
}

function fxObtenerDetObsDocentePos($msCodigo)
{
    $m_cnx_MySQL = fxAbrirConexion();

    $msConsulta = "select SYLLABUSPOS_REL, OBSDOCENTEPOS_REL, TEXTO_294 from UMO294A where SYLLABUSPOS_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo]);
    return $mDatos;
}

function fxBorrarDetObsAcademicaPos($msCodigo)
{
    $m_cnx_MySQL = fxAbrirConexion();

    $msConsulta = "delete from UMO295A where SYLLABUSPOS_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo]);
}

function fxGuardarDetObsAcademicaPos($msCodigo, $mnConsecutivo, $msObservacion)
{
    $m_cnx_MySQL = fxAbrirConexion();

    $msConsulta = "insert into UMO295A (SYLLABUSPOS_REL, OBSACADEMICAPOS_REL, TEXTO_295) values (?, ?, ?)";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo, $mnConsecutivo, $msObservacion]);
}

function fxObtenerDetObsAcademicaPos($msCodigo)
{
    $m_cnx_MySQL = fxAbrirConexion();

    $msConsulta = "select SYLLABUSPOS_REL, OBSACADEMICAPOS_REL, TEXTO_295 from UMO295A where SYLLABUSPOS_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo]);
    return $mDatos;
}

function fxBorrarDetSyllabusPos($msCodigo)
{
    $m_cnx_MySQL = fxAbrirConexion();

    $msConsulta = "delete from UMO293A where SYLLABUSPOS_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo]);
}

function fxGuardarDetSyllabusPos($msCodigo, $mnConsecutivo, $mdFecha, $msModulo, $msContenido, $msObjetivoEsp, $msForma, $msMedios, $msEvaluacion)
{
    $m_cnx_MySQL = fxAbrirConexion();

    $msConsulta = "insert into UMO073A (SYLLABUSPOS_REL, DETSYLLABUSPOS_REL, FECHA_293, MODULO_293, CONTENIDO_293, OBJETIVOESP_293, FORMA_293, ";
    $msConsulta .= "MEDIOS_293, EVALUACION_293) values (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo, $mnConsecutivo, $mdFecha, $msModulo, $msContenido, $msObjetivoEsp, $msForma, $msMedios, $msEvaluacion]);
}

function fxObtenerDetSyllabusPos($msCodigo)
{
    $m_cnx_MySQL = fxAbrirConexion();

    $msConsulta = "select SYLLABUSPOS_REL, DETSYLLABUSPOS_REL, FECHA_293, MODULO_293, CONTENIDO_293, OBJETIVOESP_293, FORMA_293, ";
    $msConsulta .= "MEDIOS_293, EVALUACION_293 from UMO293A where SYLLABUSPOS_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo]);
    return $mDatos;
}
?>