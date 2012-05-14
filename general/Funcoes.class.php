<?php
class Funcoes {
	public static function formata_data_para_gravar($data) {
		$explodeData = explode('/', $data);
		
		return $explodeData[2] . '-' . $explodeData[1] . '-' . $explodeData[0];
	}

	public static function formata_data_para_exibir($data) {
		$data = explode(' ', $data);
		
		$explodeData = explode('-', $data[0]);
		
		return $explodeData[2] . '/' . $explodeData[1] . '/' . $explodeData[0];
	}
	
	public static function formata_moeda_para_exibir($valor) {
		// Sabendo que o separador e o ponto(.)
		$aPartes = explode(".", $valor);
		
		// Tratando os Centavos
		if($aPartes[1] == 0)
			$aPartes[1] = "00";
			
		if(strlen($aPartes[1]) == 1)
			$aPartes[1] = $aPartes[1] . "0";
			
		// Tratando o Milhar
		switch(strlen($aPartes[0])) {
			case 4: // 0.000
				$milhar = substr($aPartes[0], 0, 1) . "." . substr($aPartes[0], 1, 3);
				break;
			case 5: // 00.000
				$milhar = substr($aPartes[0], 0, 2) . "." . substr($aPartes[0], 2, 3);
				break;
			case 6: // 000.000
				$milhar = substr($aPartes[0], 0, 3) . "." . substr($aPartes[0], 3, 3);
				break;
			case 7: // 0.000.000
				$milhar = substr($aPartes[0], 0, 1) . "." . substr($aPartes[0], 1, 3) . "." . substr($aPartes[0], 4, 3);
				break;
			case 8: // 00.000.000
				$milhar = substr($aPartes[0], 0, 2) . "." . substr($aPartes[0], 2, 3) . "." . substr($aPartes[0], 5, 3);
				break;
			case 9: // 000.000.000
				$milhar = substr($aPartes[0], 0, 3) . "." . substr($aPartes[0], 3, 3) . "." . substr($aPartes[0], 6, 3);
				break;
			default:
				$milhar = $aPartes[0];
				break;
		}
		return $milhar . "," . $aPartes[1];
	}

	public static function formata_moeda_para_gravar($valor) {
		$valor = str_replace(',', '.', str_replace('.', '', $valor));

		return $valor;
	}
	
	public static function lista_estados() {
		$aEstados = array(
            "AC" => "Acre",
            "AL" => "Alagoas",
            "AP" => "Amap&aacute;",
            "AM" => "Amazonas",
            "BA" => "Bahia",
            "CE" => "Cear&aacute;",
            "DF" => "Distrito Federal",
            "ES" => "Espi&iacute;rito Santo",
            "GO" => "Goi&aacute;s",
            "MA" => "Maranh&atilde;o",
            "MS" => "Mato Grosso do Sul",
            "MT" => "Mato Grosso",
            "MG" => "Minas Gerais",
            "PA" => "Par&aacute;",
            "PB" => "Para&iacute;ba",
            "PR" => "Paran&aacute;",
            "PE" => "Pernambuco",
            "PI" => "Piau&iacute;",
            "RJ" => "Rio de Janeiro",
            "RN" => "Rio Grande do Norte",
            "RS" => "Rio Grande do Sul",
            "RO" => "Rond&ocirc;nia",
            "RR" => "Roraima",
            "SC" => "Santa Catarina",
            "SP" => "S&atilde;o Paulo",
            "SE" => "Sergipe",
            "TO" => "Tocantins"
		);
		
		return $aEstados;
	}

    public static function remove_acentos($texto) {
        $array1 = array( "á", "à", "â", "ã", "ä", "é", "è", "ê", "ë", "ẽ", "í", "ì", "î", "ï", "ĩ", "ó", "ò", "ô", "õ", "ö", "ú", "ù", "û", "ü", "ũ", "ç"
                , "Á", "À", "Â", "Ã", "Ä", "É", "È", "Ê", "Ë", "Ẽ", "Í", "Ì", "Î", "Ï", "Ĩ", "Ó", "Ò", "Ô", "Õ", "Ö", "Ú", "Ù", "Û", "Ü", "Ũ", "Ç" );
        $array2 = array( "a", "a", "a", "a", "a", "e", "e", "e", "e", "e", "i", "i", "i", "i", "i", "o", "o", "o", "o", "o", "u", "u", "u", "u", "u", "c"
                , "A", "A", "A", "A", "A", "E", "E", "E", "E", "E", "I", "I", "I", "I", "I", "O", "O", "O", "O", "O", "U", "U", "U", "U", "U", "C" );
        return str_replace($array1, $array2, $texto);
    }

    public static function checa_data($date) {
        if (!empty($date)) {
            $aData = explode('/', $date);

            if (empty($aData[0]) || empty($aData[1]) || empty($aData[2]))
                return false;

            $dia = $aData[0];
            $mes = $aData[1];
            $ano = $aData[2];

            if (is_numeric($dia) && is_numeric($mes) && is_numeric($ano))
                return checkdate($mes, $dia, $ano);
            else
                return false;
        } else
            return false;
    }
    
    public static function colocarDataFormatoMySQL($__data) {
        return implode(preg_match("~\/~", $__data) == 0 ? "/" : "-", array_reverse(explode(preg_match("~\/~", $__data) == 0 ? "-" : "/", $__data)));
    }
        
    public static function diferenca_entre_datas($d1, $d2, $type='D', $sep='/') {
        $d1 = explode($sep, $d1);
        $d2 = explode($sep, $d2);
        
        switch ($type) {
            case 'A':
                $X = 31536000;
                break;
            case 'M':
                $X = 2592000;
                break;
            case 'D':
                $X = 86400;
                break;
            case 'H':
                $X = 3600;
                break;
            case 'MI':
                $X = 60;
                break;
            default:
                $X = 1;
        }
        
        return floor( ( ( mktime(0, 0, 0, $d2[1], $d2[0], $d2[2]) - mktime(0, 0, 0, $d1[1], $d1[0], $d1[2] ) ) / $X ) );
    }
    
    public static function special_ucwords($string) {
        $retorno = array();
        $string = strtolower(trim(preg_replace("/\s+/", " ", $string)));
        $palavras = explode(" ", $string);

        foreach ($palavras as $palavra) {
            if (!preg_match("/^([dn]?[aeiou][s]?|em)$/i", $palavra)) {
                $palavra = ucfirst($palavra);
            }
            $retorno[] = strtr($palavra, "ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÜÚÞß",
                                         "àáâãäåæçèéêëìíîïðñòóôõö÷øùüúþÿ");
        }
        
        return implode(" ", $retorno);
    }
}
?>