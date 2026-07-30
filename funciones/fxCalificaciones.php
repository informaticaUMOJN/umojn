<?php
function fxGuardarCalificacion($msDocente, $msAsignatura, $msCarrera, $mdFecha, $mnAnno, $mnSemestre, $mnParcial, $mnTurno)
{
    $m_cnx_MySQL = fxAbrirConexion();
    $msConsulta = "Select ifnull(mid(max(CALIFICACION_REL), 4), 0) as Ultimo from UMO160A";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute();
    $mFila = $mDatos->fetch();
    $mnNumero = intval($mFila["Ultimo"]);
    $mnNumero += 1;
    $mnLongitud = strlen($mnNumero);
    $msCodigo = "CLF" . str_repeat("0", 7 - $mnLongitud) . trim($mnNumero);

    $msConsulta = "insert into UMO160A (CALIFICACION_REL, DOCENTE_REL, ASIGNATURA_REL, CARRERA_REL, FECHA_160, ANNO_160, SEMESTRE_160, PARCIAL_160, TURNO_160) values(?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo, $msDocente, $msAsignatura, $msCarrera, $mdFecha, $mnAnno, $mnSemestre, $mnParcial, $mnTurno]);
    return $msCodigo;
}

function fxModificarCalificacion($msCodigo, $msDocente, $msAsignatura, $msCarrera, $mdFecha, $mnAnno, $mnSemestre, $mnParcial, $mnTurno)
{
    $m_cnx_MySQL = fxAbrirConexion();
    $msConsulta = "update UMO160A set DOCENTE_REL = ?, ASIGNATURA_REL = ?, CARRERA_REL = ?, FECHA_160 = ?, ANNO_160 = ?, SEMESTRE_160 = ?, PARCIAL_160 = ?, TURNO_160 = ? where CALIFICACION_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msDocente, $msAsignatura, $msCarrera, $mdFecha, $mnAnno, $mnSemestre, $mnParcial, $mnTurno, $msCodigo]);
}

function fxBorrarCalificacion($msCodigo)
{
    $m_cnx_MySQL = fxAbrirConexion();
    $msConsulta = "delete from UMO160A where CALIFICACION_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo]);
}

function fxDevuelveCalificacion($mbLlenaGrid, $msDocente, $msCodigo)
{
    $m_cnx_MySQL = fxAbrirConexion();

    if ($mbLlenaGrid == 1)
    {
        if ($msDocente == "")
        {
            $msConsulta = "select CALIFICACION_REL, NOMBRE_100, NOMBRE_060, ANNO_160, SEMESTRE_160, PARCIAL_160, TURNO_160 from UMO160A, UMO100A, UMO060A ";
            $msConsulta .= "where UMO160A.DOCENTE_REL = UMO100A.DOCENTE_REL and UMO160A.ASIGNATURA_REL = UMO060A.ASIGNATURA_REL ";
            $msConsulta .= "order by CALIFICACION_REL desc";
            $mDatos = $m_cnx_MySQL->prepare($msConsulta);
            $mDatos->execute();
        }
        else
        {
            $msConsulta = "select CALIFICACION_REL, NOMBRE_100, NOMBRE_060, ANNO_160, SEMESTRE_160, PARCIAL_160, TURNO_160 from UMO160A, UMO100A, UMO060A ";
            $msConsulta .= "where UMO160A.DOCENTE_REL = UMO100A.DOCENTE_REL and UMO160A.ASIGNATURA_REL = UMO060A.ASIGNATURA_REL ";
            $msConsulta .= "and UMO160A.DOCENTE_REL = ? order by CALIFICACION_REL desc";
            $mDatos = $m_cnx_MySQL->prepare($msConsulta);
            $mDatos->execute([$msDocente]);
        }
    }
    else
    {
        $msConsulta = "select CALIFICACION_REL, DOCENTE_REL, ASIGNATURA_REL, CARRERA_REL, FECHA_160, ANNO_160, SEMESTRE_160, PARCIAL_160, TURNO_160 from UMO160A where CALIFICACION_REL = ?";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$msCodigo]);
    }
    return $mDatos;
}

function fxCierreCalificacion($msUsuario, $mnAnno, $mnSemestre, $mnParcial, $mnTurno)
{
    $m_cnx_MySQL = fxAbrirConexion();

    $mdFechaHoy = date('Y-m-d H:i:s');
    $msConsulta = "select DOCENTE_REL, ASIGNATURA_REL from UMO070A where ANNO_070 = ? and SEMESTRE_070 = ? and TURNO_070 = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$mnAnno, $mnSemestre, $mnTurno]);

    while($mFila = $mDatos->fetch())
    {
        $msAsignatura = $mFila["ASIGNATURA_REL"];
        $msDocente = $mFila["DOCENTE_REL"];
        $msConsulta = "insert into UMO162A (USUARIO_162, ANNO_162, SEMESTRE_162, PARCIAL_162, TURNO_162, FECHA_162, ASIGNATURA_REL, DOCENTE_REL, ESTADO_162) values (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $mAuxiliar = $m_cnx_MySQL->prepare($msConsulta);
        $mAuxiliar->execute([$msUsuario, $mnAnno, $mnSemestre, $mnParcial, $mnTurno, $mdFechaHoy, $msAsignatura, $msDocente, 1]);
    }
}

function fxEstadoCierreCalificacion($mbEstado, $mnAnno, $mnSemestre, $mnParcial, $mnTurno, $msAsignatura, $msDocente)
{
    $m_cnx_MySQL = fxAbrirConexion();

    $msConsulta = "update UMO162A set ESTADO = ? where ANNO_162 = ? and SEMESTRE_162 = ? and PARCIAL_162 = ? and TURNO_162 = ? and ASIGNATURA_REL = ? and DOCENTE_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$mbEstado, $mnAnno, $mnSemestre, $mnParcial, $mnTurno, $msAsignatura, $msDocente]);
}

function fxDevuelveCierreCalificacion($mnAnno, $mnSemestre, $mnParcial, $mnTurno)
{
    $m_cnx_MySQL = fxAbrirConexion();
    $msConsulta = "select * from UMO162A where ANNO_162 = ? and SEMESTRE_162 = ? and PARCIAL_162 = ? and TURNO_162 = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$mnAnno, $mnSemestre, $mnParcial, $mnTurno]);
    $mnRegistros = $mDatos->rowCount();
    return $mnRegistros;
}

function fxGuardarDetCalificacion($msCodigo, $msMatricula, $mnNota)
{
    $m_cnx_MySQL = fxAbrirConexion();
    $msConsulta = "insert into UMO161A (CALIFICACION_REL, MATRICULA_REL, NOTA_161) values (?, ?, ?)";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo, $msMatricula, $mnNota]);
}

function fxDevuelveDetCalificacion($msCodigo, $msAsignatura="", $mnTurno=0, $mnAnno=0, $mnSemestre=0)
{
    $m_cnx_MySQL = fxAbrirConexion();

    if ($msCodigo != "")
    {
        $msConsulta = "select CALIFICACION_REL, UMO030A.MATRICULA_REL, NOMBRE1_010, NOMBRE2_010, APELLIDO1_010, APELLIDO2_010, NOTA_161 ";
        $msConsulta .= "from UMO161A, UMO030A, UMO010A where UMO161A.MATRICULA_REL = UMO030A.MATRICULA_REL and ";
        $msConsulta .= "UMO030A.ESTUDIANTE_REL = UMO010A.ESTUDIANTE_REL and UMO161A.CALIFICACION_REL = ? ";
        $msConsulta .= "order by APELLIDO1_010, APELLIDO2_010, NOMBRE1_010, NOMBRE2_010";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$msCodigo]);
    }
    else
    {
        $msConsulta = "select '' as CALIFICACION_REL, UMO030A.MATRICULA_REL, NOMBRE1_010, NOMBRE2_010, APELLIDO1_010, APELLIDO2_010, 0 as NOTA_161 ";
        $msConsulta .= "from UMO031A, UMO030A, UMO010A, UMO050A where UMO031A.MATRICULA_REL = UMO030A.MATRICULA_REL and UMO030A.ESTUDIANTE_REL = UMO010A.ESTUDIANTE_REL ";
        $msConsulta .= "and UMO030A.PLANESTUDIO_REL = UMO050A.PLANESTUDIO_REL and UMO031A.ASIGNATURA_REL = ? and TURNO_050 = ? and ESTADO_030 = 0 and ANNOACADEMICO_030 = ? and SEMESTREACADEMICO_030 = ? ";
        $msConsulta .= "order by APELLIDO1_010, APELLIDO2_010, NOMBRE1_010, NOMBRE2_010";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$msAsignatura, $mnTurno, $mnAnno, $mnSemestre]);
    }

    return $mDatos;
}

function fxBorrarDetCalificacion($msCodigo)
{
    $m_cnx_MySQL = fxAbrirConexion();
    $msConsulta = "delete from UMO161A where CALIFICACION_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo]);
}
?>