<?php
function fxGuardarAvanceProg($msDocente, $msCarrera, $msAsignatura, $mdFecha, $mnAnno, $mnSemestre, $mnTurno)
{
    $m_cnx_MySQL = fxAbrirConexion();
    $msConsulta = "Select ifnull(mid(max(AVANCE_REL), 3), 0) as Ultimo from UMO170A";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute();
    $mFila = $mDatos->fetch();
    $mnNumero = intval($mFila["Ultimo"]);
    $mnNumero += 1;
    $mnLongitud = strlen($mnNumero);
    $msCodigo = "AP" . str_repeat("0", 8 - $mnLongitud) . trim($mnNumero);

    $msConsulta = "Select SYLLABUS_REL from UMO070A where DOCENTE_REL=? and ASIGNATURA_REL=? and ANNO_070=? and SEMESTRE_070=? and TURNO_070=?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msDocente, $msAsignatura, $mnAnno, $mnSemestre, $mnTurno]);
    $mFila = $mDatos->fetch();
    $msSyllabus = $mFila["SYLLABUS_REL"];

    $msConsulta = "insert into UMO170A (AVANCE_REL, DOCENTE_REL, CARRERA_REL, SYLLABUS_REL, ASIGNATURA_REL, FECHA_170, ANNO_170, SEMESTRE_170, TURNO_170) values(?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo, $msDocente, $msCarrera, $msSyllabus, $msAsignatura, $mdFecha, $mnAnno, $mnSemestre, $mnTurno]);
    return $msCodigo;
}

function fxModificarAvanceProg($msCodigo, $msDocente, $msCarrera, $msAsignatura, $mdFecha, $mnAnno, $mnSemestre, $mnTurno)
{
    $m_cnx_MySQL = fxAbrirConexion();
    $msConsulta = "update UMO170A set DOCENTE_REL = ?, CARRERA_REL = ?, ASIGNATURA_REL = ?, FECHA_170 = ?, ANNO_170 = ?, SEMESTRE_170 = ?, TURNO_170 = ? where AVANCE_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msDocente, $msCarrera, $msAsignatura, $mdFecha, $msCodigo, $mnAnno, $mnSemestre, $mnTurno]);
}

function fxBorrarAvanceProg($msCodigo)
{
    $m_cnx_MySQL = fxAbrirConexion();
    $msConsulta = "delete from UMO171A where AVANCE_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo]);

    $msConsulta = "delete from UMO170A where AVANCE_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo]);
}

function fxDevuelveAvanceProg($mbLlenaGrid, $msCodigo = "")
{
    $m_cnx_MySQL = fxAbrirConexion();

    if ($mbLlenaGrid == 1)
    {
        $msConsulta = "select AVANCE_REL, NOMBRE_100, NOMBRE_060, FECHA_170, ANNO_170, SEMESTRE_170, TURNO_170 from UMO170A, UMO100A, UMO060A ";
        $msConsulta .= "where UMO170A.DOCENTE_REL = UMO100A.DOCENTE_REL and UMO170A.ASIGNATURA_REL = UMO060A.ASIGNATURA_REL ";
        $msConsulta .= "order by AVANCE_REL desc";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute();
    }
    else
    {
        $msConsulta = "select AVANCE_REL, DOCENTE_REL, CARRERA_REL, SYLLABUS_REL, ASIGNATURA_REL, FECHA_170, ANNO_170, SEMESTRE_170, TURNO_170 from UMO170A where AVANCE_REL = ?";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$msCodigo]);
    }
    return $mDatos;
}

function fxGuardarDetAvance($msCodigo, $mnDetalle, $mdFechaP, $mdFechaE, $msUnidadP, $msUnidadE, $msContenidoP, $msContenidoE, $msObservaciones)
{
    $m_cnx_MySQL = fxAbrirConexion();
    $msConsulta = "insert into UMO171A (AVANCE_REL, DETAVANCE_REL, FECHAP_171, FECHAE_171, UNIDADP_171, UNIDADE_171, CONTENIDOP_171, CONTENIDOE_171, OBSERVACIONES_171) values (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo, $mnDetalle, $mdFechaP, $mdFechaE, $msUnidadP, $msUnidadE, $msContenidoP, $msContenidoE, $msObservaciones]);
}

function fxBorrarDetAvance($msCodigo)
{
    $m_cnx_MySQL = fxAbrirConexion();
    $msConsulta = "delete from UMO171A where AVANCE_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo]);
}

function fxDevuelveDetAvance($msCodigo, $msAsignatura="", $mnAnno=0, $mnSemestre=0, $mnTurno=0)
{
    $m_cnx_MySQL = fxAbrirConexion();

    if ($msCodigo != "")
    {
        $msConsulta = "select FECHAP_171 as FechaP, UNIDADP_171 as UnidadP, CONTENIDOP_171 as ContenidoP, FECHAE_171 as FechaE, UNIDADE_171 as UnidadE, CONTENIDOE_171 as ContenidoE, ";
        $msConsulta .= "OBSERVACIONES_171 from UMO171A where AVANCE_REL = ?";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$msCodigo]);
    }
    else
    {
        $msConsulta = "select FECHA_073 as FechaP, UNIDAD_073 as UnidadP, CONTENIDO_073 as ContenidoP, DATE(NOW()) as FechaE, '' as UnidadE, '' as ContenidoE, ";
        $msConsulta .= "'' as OBSERVACIONES_171 from UMO073A join UMO070A on UMO073A.SYLLABUS_REL = UMO070A.SYLLABUS_REL ";
        $msConsulta .= "where ASIGNATURA_REL = ? and ANNO_070 = ? and SEMESTRE_070 = ? and TURNO_070 = ?";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$msAsignatura, $mnAnno, $mnSemestre, $mnTurno]);
    }

    return $mDatos;
}
?>