<?php
	function fxNumerosLetras($vnNumero)
	{
        $msNumLetras = '';
        $mnCuentaDigitos = strlen(trim($vnNumero));
        $mnCaracterLong = $mnCuentaDigitos % 3;
	    $mnCaracterInicio = 0;
	    $mnCuentaCero = 0;
        $mbEntroEnElIf = 0;
        $msDigitoAnterior = '';
        $msDigitoPrevioAnterior = '';

        While ($mnCuentaDigitos > 0)
        {
            $msTriada = substr($vnNumero, $mnCaracterInicio, $mnCaracterLong);
		    $mnCuentaTriada = strlen($msTriada);
		    $mnCuenta = $mnCuentaTriada;

            While ($mnCuentaTriada > 0)
            {
                if ($mbEntroEnElIf == 0)
                {
                    $mnControlDigitos = strlen($vnNumero) - (strlen($vnNumero) - $mnCuentaDigitos);
                    if ($mnControlDigitos == 3 or $mnControlDigitos == 9 or $mnControlDigitos == 15)
                    {
                        if ($mnCuentaCero < 3)
                        {
                            $msNumLetras .= 'mil ';
						    $mbEntroEnElIf = 1;
						    $mnCuentaCero = 0;
                        }
                    }
                    else
                    {
                        if ($mnControlDigitos == 6)
                        {
                            if ($mnCuentaCero < 3)
                            {
                                if (trim($msNumLetras) == 'un')
								    $msNumLetras .= 'millón ';
                                else
								    $msNumLetras .= 'millones ';
	
							    $mbEntroEnElIf = 1;
						    	$mnCuentaCero = 0;
                            }
                        }

                        if ($mnControlDigitos == 12)
                        {
                            if ($mnCuentaCero < 3)
                            {
                                if (trim($msNumLetras) == 'un')
								    $msNumLetras .= 'billón ';
                                else
								    $msNumLetras .= 'billones ';
	
							    $mbEntroEnElIf = 1;
						    	$mnCuentaCero = 0;
                            }
                        }
                    }
                }

                switch (strlen($msTriada))
                {
                    case 3:
                        $msDigito = trim(substr($msTriada, 3 - $mnCuentaTriada, 1));
                        $msDigitoSiguiente = trim(substr($msTriada, 2 - $mnCuentaTriada, 1));
                        break;
                    case 2:
                        $msDigito = trim(substr($msTriada, 2 - $mnCuentaTriada, 1));
                        $msDigitoSiguiente = trim(substr($msTriada, 1 - $mnCuentaTriada, 1));
                        break;
                    case 1:
                        $msDigito = trim(substr($msTriada, 1 - $mnCuentaTriada, 1));
                        $msDigitoSiguiente = "";
                        break;
                }

                If ($mnCuentaTriada == 3)
                {
                    switch(intval($msDigito))
                    {
                        case 0:
                            $mnCuentaCero += 1;
                            break;
                        case 1:
                            $msNumLetras .= 'ciento ';
                            break;
                        case 2:
                            $msNumLetras .= 'doscientos ';
                            break;
                        case 3:
                            $msNumLetras .= 'trescientos ';
                            break;
                        case 4:
                            $msNumLetras .= 'cuatrocientos ';
                            break;
                        case 5:
                            $msNumLetras .= 'quinientos ';
                            break;
                        case 6:
                            $msNumLetras .= 'seiscientos ';
                            break;
                        case 7:
                            $msNumLetras .= 'setecientos ';
                            break;
                        case 8:
                            $msNumLetras .= 'ochocientos ';
                            break;
                        case 9:
                            $msNumLetras .= 'novecientos ';
                            break;
                    }
                    $msDigitoPrevioAnterior = $msDigitoAnterior;
                }

                If ($mnCuentaTriada == 2)
                {
                    switch (intval($msDigito))
                    {
                        case 0:
                            $mnCuentaCero +=1;
                            break;
                        case 3:
                            if ($msDigitoSiguiente == "0")
                                $msNumLetras .= 'treinta ';
                            else
                                $msNumLetras .= 'treinta y ';
                            break;
                        case 4:
                            if ($msDigitoSiguiente == "0")
                                $msNumLetras .= 'cuarenta ';
                            else
                                $msNumLetras .= 'cuarenta y ';
                            break;
                        case 5:
                            if ($msDigitoSiguiente == "0")
                                $msNumLetras .= 'cincuenta ';
                            else
                                $msNumLetras .= 'cincuenta y ';
                            break;
                        case 6:
                            if ($msDigitoSiguiente == "0")
                                $msNumLetras .= 'sesenta ';
                            else
                                $msNumLetras .= 'sesenta y ';
                            break;
                        case 7:    
                            if ($msDigitoSiguiente == "0")
                                $msNumLetras .= 'setenta ';
                            else
                                $msNumLetras .= 'setenta y ';
                            break;
                        case 8:
                            if ($msDigitoSiguiente == "0")
                                $msNumLetras .= 'ochenta ';
                            else
                                $msNumLetras .= 'ochenta y ';
                            break;
                        case 9:
                            if ($msDigitoSiguiente == "0")
                                $msNumLetras .= 'noventa ';
                            else
                                $msNumLetras .= 'noventa y ';
                            break;
                    }
                    $msDigitoAnterior = $msDigito;
                }

                If ($mnCuentaTriada == 1)
                {
                    $mbEntroEnElIf = 0;

                    switch (intval($msDigito))
                    {
                        case 0:
                            $mnCuentaCero += 1;
                            If ($msDigitoAnterior == '' or $msDigitoAnterior == '0')
                            {
                                If ($msDigitoPrevioAnterior == '1')
                                {
                                    $msNumLetras = substr($msNumLetras, 0, strlen($msNumLetras) - 2) + ' ';
                                }
                                else
                                {
                                    If (strlen(trim($vnNumero)) == 1)
                                        $msNumLetras .= 'Cero ';
                                }
                            }
                            else
                            {
                                If ($msDigitoAnterior == '1')
                                    $msNumLetras .= 'diez ';
                                else
                                {
                                    If ($msDigitoAnterior == '2')
                                        $msNumLetras .= 'veinte ';
                                    else
                                        $msNumLetras = substr($msNumLetras, 0, strlen($msNumLetras) - 1);
                                }
                            }
                            break;
                        case 1:
                            If ($msDigitoAnterior == '0' or $msDigitoAnterior == '3' or $msDigitoAnterior == '4' or $msDigitoAnterior == '5' or $msDigitoAnterior == '6' or $msDigitoAnterior == '7' or $msDigitoAnterior == '8' or $msDigitoAnterior == '9')
                                $msNumLetras .= 'un ';
                            If ($msDigitoAnterior == '1')
                                $msNumLetras .= 'once ';
                            If ($msDigitoAnterior == '2')
                                $msNumLetras .= 'veintiun ';
                            break;
                        case 2:
                            If ($msDigitoAnterior == '' or $msDigitoAnterior == '0' or $msDigitoAnterior == '3' or $msDigitoAnterior == '4' or $msDigitoAnterior == '5' or $msDigitoAnterior == '6' or $msDigitoAnterior == '7' or $msDigitoAnterior == '8' or $msDigitoAnterior == '9')
                                $msNumLetras .= 'dos ';
                            If ($msDigitoAnterior == '1')
                                $msNumLetras .= 'doce ';
                            If ($msDigitoAnterior == '2')
                                $msNumLetras .= 'veintidos ';
                            break;
                        case 3:
                            If ($msDigitoAnterior == '' or $msDigitoAnterior == '0' or $msDigitoAnterior == '3' or $msDigitoAnterior == '4' or $msDigitoAnterior == '5' or $msDigitoAnterior == '6' or $msDigitoAnterior == '7' or $msDigitoAnterior == '8' or $msDigitoAnterior == '9')
                                $msNumLetras .= 'tres ';
                            If ($msDigitoAnterior == '1')
                                $msNumLetras .= 'trece ';
                            If ($msDigitoAnterior == '2')
                                $msNumLetras .= 'veintitres ';
                            break;
                        case 4:
                            If ($msDigitoAnterior == '' or $msDigitoAnterior == '0' or $msDigitoAnterior == '3' or $msDigitoAnterior == '4' or $msDigitoAnterior == '5' or $msDigitoAnterior == '6' or $msDigitoAnterior == '7' or $msDigitoAnterior == '8' or $msDigitoAnterior == '9')
                                $msNumLetras .= 'cuatro ';
                            If ($msDigitoAnterior == '1')
                                $msNumLetras .= 'catorce ';
                            If ($msDigitoAnterior == '2')
                                $msNumLetras .= 'veinticuatro ';
                            break;
                        case 5:
                            If ($msDigitoAnterior == '' or $msDigitoAnterior == '0' or $msDigitoAnterior == '3' or $msDigitoAnterior == '4' or $msDigitoAnterior == '5' or $msDigitoAnterior == '6' or $msDigitoAnterior == '7' or $msDigitoAnterior == '8' or $msDigitoAnterior == '9')
                                $msNumLetras .= 'cinco ';
                            If ($msDigitoAnterior == '1')
                                $msNumLetras .= 'quince ';
                            If ($msDigitoAnterior == '2')
                                $msNumLetras .= 'veinticinco ';
                            break;
                        case 6:
                            If ($msDigitoAnterior == '' or $msDigitoAnterior == '0' or $msDigitoAnterior == '3' or $msDigitoAnterior == '4' or $msDigitoAnterior == '5' or $msDigitoAnterior == '6' or $msDigitoAnterior == '7' or $msDigitoAnterior == '8' or $msDigitoAnterior == '9')
                                $msNumLetras .= 'seis ';
                            If ($msDigitoAnterior == '1')
                                $msNumLetras .= 'dieciseis ';
                            If ($msDigitoAnterior == '2')
                                $msNumLetras .= 'veintiseis ';
                            break;
                        case 7:
                            If ($msDigitoAnterior == '' or $msDigitoAnterior == '0' or $msDigitoAnterior == '3' or $msDigitoAnterior == '4' or $msDigitoAnterior == '5' or $msDigitoAnterior == '6' or $msDigitoAnterior == '7' or $msDigitoAnterior == '8' or $msDigitoAnterior == '9')
                                $msNumLetras .= 'siete ';
                            If ($msDigitoAnterior == '1')
                                $msNumLetras .= 'diecisiete ';
                            If ($msDigitoAnterior == '2')
                                $msNumLetras .= 'veintisiete ';
                            break;
                        case 8:
                            If ($msDigitoAnterior == '' or $msDigitoAnterior == '0' or $msDigitoAnterior == '3' or $msDigitoAnterior == '4' or $msDigitoAnterior == '5' or $msDigitoAnterior == '6' or $msDigitoAnterior == '7' or $msDigitoAnterior == '8' or $msDigitoAnterior == '9')
                                $msNumLetras .= 'ocho ';
                            If ($msDigitoAnterior == '1')
                                $msNumLetras .= 'dieciocho ';
                            If ($msDigitoAnterior == '2')
                                $msNumLetras .= 'veintiocho ';
                            break;
                        case 9:
                            If ($msDigitoAnterior == '' or $msDigitoAnterior == '0' or $msDigitoAnterior == '3' or $msDigitoAnterior == '4' or $msDigitoAnterior == '5' or $msDigitoAnterior == '6' or $msDigitoAnterior == '7' or $msDigitoAnterior == '8' or $msDigitoAnterior == '9')
                                $msNumLetras .= 'nueve ';
                            If ($msDigitoAnterior == '1')
                                $msNumLetras .= 'diecinueve ';
                            If ($msDigitoAnterior == '2')
                                $msNumLetras .= 'veintinueve ';
                            break;
                    }
                }
                $msDigitoAnterior = $msDigito;
                $mnCuentaTriada -= 1;
            }

            $mnCaracterInicio += $mnCaracterLong;
            $mnCuentaDigitos -= $mnCuenta;
            $mnCaracterLong = 3 - ($mnCuentaDigitos % 3);
        }

        $msResultado = trim($msNumLetras);
        return $msResultado;
    }
?>