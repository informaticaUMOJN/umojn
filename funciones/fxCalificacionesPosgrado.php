<?php
function fxGuardarCalificacionPos($msDocente, $msCursoPosgrado, $mdFecha, $msCohorte, $mnTurno, $mnRegimen)
{
    $m_cnx_MySQL = fxAbrirConexion();
    $msConsulta = "Select ifnull(mid(max(CALIFICACIONPOS_REL), 4), 0) as Ultimo from UMO310A";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute();
    $mFila = $mDatos->fetch();
    $mnNumero = intval($mFila["Ultimo"]);
    $mnNumero += 1;
    $mnLongitud = strlen($mnNumero);
    $msCodigo = "CLP" . str_repeat("0", 7 - $mnLongitud) . trim($mnNumero);
    $msConsulta = "insert into UMO310A (CALIFICACIONPOS_REL, DOCENTE_REL, CURSOPOSGRADO_REL, FECHA_310, COHORTE_310, TURNO_310, REGIMEN_310) values(?, ?, ?, ?, ?, ?, ?)";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo, $msDocente, $msCursoPosgrado, $mdFecha, $msCohorte, $mnTurno, $mnRegimen]);
    return $msCodigo;
}

function fxModificarCalificacionPos($msCodigo, $msDocente, $msCursoPosgrado, $mdFecha, $msCohorte, $mnTurno, $mnRegimen)
{
    $m_cnx_MySQL = fxAbrirConexion();
    $msConsulta = "update UMO310A set DOCENTE_REL = ?, CURSOPOSGRADO_REL = ?, FECHA_310 = ?, COHORTE_310 = ?, TURNO_310 = ?, REGIMEN_310 = ? where CALIFICACIONPOS_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msDocente, $msCursoPosgrado, $mdFecha, $msCohorte, $mnTurno, $mnRegimen, $msCodigo]);
}

function fxBorrarCalificacionPos($msCodigo)
{
    $m_cnx_MySQL = fxAbrirConexion();
    $msConsulta = "delete from UMO310A where CALIFICACIONPOS_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo]);
}

function fxDevuelveCalificacionPos($mbLlenaGrid, $msDocente, $msCodigo)
{
    $m_cnx_MySQL = fxAbrirConexion();

    if ($mbLlenaGrid == 1)
    {
        if ($msDocente == "")
        {
            $msConsulta = "select CALIFICACIONPOS_REL, NOMBRE_100, NOMBRE_240, COHORTE_310, TURNO_310 from UMO310A, UMO100A, UMO240A ";
            $msConsulta .= "where UMO310A.DOCENTE_REL = UMO100A.DOCENTE_REL and UMO310A.CURSOPOSGRADO_REL = UMO240A.CURSOPOSGRADO_REL ";
            $msConsulta .= "order by CALIFICACIONPOS_REL desc";
            $mDatos = $m_cnx_MySQL->prepare($msConsulta);
            $mDatos->execute();
        }
        else
        {
            $msConsulta = "select CALIFICACIONPOS_REL, NOMBRE_100, NOMBRE_240, COHORTE_310, TURNO_310 from UMO310A, UMO100A, UMO240A ";
            $msConsulta .= "where UMO310A.DOCENTE_REL = UMO100A.DOCENTE_REL and UMO310A.CURSOPOSGRADO_REL = UMO240A.CURSOPOSGRADO_REL ";
            $msConsulta .= "and UMO310A.DOCENTE_REL = ? order by CALIFICACIONPOS_REL desc";
            $mDatos = $m_cnx_MySQL->prepare($msConsulta);
            $mDatos->execute([$msDocente]);
        }
    }
    else
    {
        $msConsulta = "select CALIFICACIONPOS_REL, DOCENTE_REL, CURSOPOSGRADO_REL, FECHA_310, COHORTE_310, TURNO_310, REGIMEN_310 from UMO310A where CALIFICACIONPOS_REL = ?";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$msCodigo]);
    }
    return $mDatos;
}

function fxGuardarDetCalificacionPos($msCodigo, $msMatricula, $mnAsistencia, $mnAcumulado, $mnTrabajo)
{
    $m_cnx_MySQL = fxAbrirConexion();
    $mnNota = $mnAsistencia + $mnAcumulado + $mnTrabajo;
    $msConsulta = "insert into UMO311A (CALIFICACIONPOS_REL, MATRICULAPOS_REL, ASISTENCIA_311, ACUMULADO_311, TRABAJO_311, NOTA_311) values (?, ?, ?, ?, ?, ?)";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo, $msMatricula, $mnAsistencia, $mnAcumulado, $mnTrabajo]);
}

function fxDevuelveDetCalificacionPos($msCodigo, $msCurso="", $msCohorte=0)
{
    $m_cnx_MySQL = fxAbrirConexion();

    if ($msCodigo != "")
    {
        $msConsulta = "select CALIFICACIONPOS_REL, UMO260A.MATRICULAPOS_REL, NOMBRE1_250, NOMBRE2_250, APELLIDO1_250, APELLIDO2_250, ASISTENCIA_311, ACUMULADO_311, TRABAJO_311, NOTA_311 ";
        $msConsulta .= "from UMO311A, UMO260A, UMO250A where UMO311A.MATRICULAPOS_REL = UMO260A.MATRICULAPOS_REL and ";
        $msConsulta .= "UMO260A.ESTUDIANTEPOS_REL = UMO250A.ESTUDIANTEPOS_REL and UMO311A.CALIFICACIONPOS_REL = ? ";
        $msConsulta .= "order by APELLIDO1_250, APELLIDO2_250, NOMBRE1_250, NOMBRE2_250";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$msCodigo]);
    }
    else
    {
        $msConsulta = "select '' as CALIFICACIONPOS_REL, UMO260A.MATRICULAPOS_REL, NOMBRE1_250, NOMBRE2_250, APELLIDO1_250, APELLIDO2_250, 0 as ASISTENCIA_311, 0 as ACUMULADO_311, 0 as TRABAJO_311, 0 as NOTA_311 ";
        $msConsulta .= "from UMO260A, UMO250A where UMO260A.ESTUDIANTEPOS_REL = UMO250A.ESTUDIANTEPOS_REL ";
        $msConsulta .= "and CURSOPOSGRADO_REL = ? and COHORTE_260 = ? ";
        $msConsulta .= "order by APELLIDO1_250, APELLIDO2_250, NOMBRE1_250, NOMBRE2_250";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$msCurso, $msCohorte]);
    }

    return $mDatos;
}

function fxBorrarDetCalificacionPos($msCodigo)
{
    $m_cnx_MySQL = fxAbrirConexion();
    $msConsulta = "delete from UMO311A where CALIFICACIONPOS_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo]);
}
?>