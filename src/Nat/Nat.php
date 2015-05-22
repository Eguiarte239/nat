<?php

namespace Nat;

class Nat {

    private $simple;
    private $compuesto;
    private $centenas;
    private $miles;
    private $millon;
    private $millones;

    private $numero;
    private $moneda;
    private $numero_entero;

    public function __construct($numero, $moneda)
    {
        $this->numero = $numero;
        $this->numero_entero = (integer)$numero;

        if(empty($moneda)){
            $this->moneda = "PESOS";
        } else {
            $this->moneda = strtoupper($moneda);
        }

        $this->set_variables ();
    }

    private function set_variables ()
    {
        $this->simple['1']='UN';
        $this->simple['2']='DOS';
        $this->simple['3']='TRES';
        $this->simple['4']='CUATRO';
        $this->simple['5']='CINCO';
        $this->simple['6']='SEIS';
        $this->simple['7']='SIETE';
        $this->simple['8']='OCHO';
        $this->simple['9']='NUEVE';

        $this->simple['11']='ONCE';
        $this->simple['12']='DOCE';
        $this->simple['13']='TRECE';
        $this->simple['14']='CATORCE';
        $this->simple['15']='QUINCE';
        $this->simple['16']='DIECISEIS';
        $this->simple['17']='DIECISIETE';
        $this->simple['18']='DIECIOCHO';
        $this->simple['19']='DIECINUEVE';

        $this->simple['10']='DIEZ';
        $this->simple['20']='VEINTE';
        $this->simple['30']='TREINTA';
        $this->simple['40']='CUARENTA';
        $this->simple['50']='CINCUENTA';
        $this->simple['60']='SESENTA';
        $this->simple['70']='SETENTA';
        $this->simple['80']='OCHENTA';
        $this->simple['90']='NOVENTA';

        $this->compuesto['2']='VEINTI';
        $this->compuesto['3']='TREINTA Y ';
        $this->compuesto['4']='CUARENTA Y ';
        $this->compuesto['5']='CINCUENTA Y ';
        $this->compuesto['6']='SESENTA Y ';
        $this->compuesto['7']='SETENTA Y ';
        $this->compuesto['8']='OCHENTA Y ';
        $this->compuesto['9']='NOVENTA Y ';

        $this->centenas['0']='CIEN';
        $this->centenas['1']='CIENTO ';
        $this->centenas['2']='DOSCIENTOS ';
        $this->centenas['3']='TRESCIENTOS ';
        $this->centenas['4']='CUATROCIENTOS ';
        $this->centenas['5']='QUINIENTOS ';
        $this->centenas['6']='SEISCIENTOS ';
        $this->centenas['7']='SETECIENTOS ';
        $this->centenas['8']='OCHOCIENTOS ';
        $this->centenas['9']='NOVECIENTOS ';

        $this->miles=' MIL ';
        $this->millon=' MILLON ';
        $this->millones=' MILLONES ';
    }

    //Convierte numeros < 100 (Unidades, Decenas)
    private function menor_cien($ud)
    {
        //elige menores a 20 y multiplos de 10
        if ( ($ud < 20) || ($ud%10 == 0) ) {
            $ud_txt = $this->simple[$ud];
        } else {
            $ud_txt = $this->compuesto[$ud/10].$this->simple[$ud%10];
        }
        return $ud_txt;
    }

    //Convierte numeros entre 99 y 100(Centenas)
    private function convertir_centenas($c)
    {
        if ( $c == 100 ) {
            $c_txt = $this->centenas['0'];
        } else {
            $c_txt = $this->centenas[$c/100];
        }
        return $c_txt;
    }

    private function menor_mil($n)
    {
        if( $n < 100 ) {
            $n_txt = $this->menor_cien($n);
        } elseif ( $n < 1000 ) {
            $ud = $n % 100;
            $ud_txt = $this->menor_cien($ud);
            $c_txt = $this->convertir_centenas($n);
            $n_txt = $c_txt.$ud_txt;
        }
        return $n_txt;
    }

    //Inicio de Conversión
    public function convertir()
    {
        if($this->numero_entero == 0){
            return "---";
        }
        $n_str = (string)$this->numero_entero;//Convierte el número entero a cadena

        //Numero < 1000
        if ( $this->numero_entero < 1000 )
        {
            $n = $this->numero_entero;
            $n_txt = $this->menor_mil($n);
            $letras = $n_txt." ".$this->moneda;
        }

        //Numero {1000 < n < 1,000,000}
        elseif ( $this->numero_entero < 1000000 )
        {
            $m = 0;
            $long_n = strlen ($n_str);//cuenta el número de caracteres
            $f = 1;

            //Separa los miles para ser evaluados con la función menor_mil
            for ($k = $long_n; $k > 3; $k--)
            {
                $m += ( $n_str[$k-4] * $f );	//recupera la posicion 0,1,2 y Multiplica por 1,10,y 100
                $f *= 10;
            }

            //Miles que pasan a funcion menor_mil para ser evaluadas
            $miles_txt = $this->menor_mil($m);

            $n = $this->numero_entero - $m * 1000;//Elimina los miles

            $n_txt = $this->menor_mil($n);//Unidades, Decenas y Centenas a ser evaluadas
            $letras = $miles_txt.$this->miles.$n_txt." ".$this->moneda;
        }

        //número mayor o igual a 1,000,000
        else
        {
            $long_n = strlen($n_str);//cuenta el número de caracteres
            $f = 1;
            $mm = 0;
            for ($k = $long_n; $k > 6; $k--)
            {
                $mm += ($n_str[$k-7] * $f);	//Recupera la posición 0,1,2 del array u la Multiplica por 1,10,y 100 respectivamente
                $f *= 10;
            }

            if ( $mm == 1 )
            {
                $this->millones = $this->millon;
            }
            $millones_txt = $this->menor_mil($mm);

            $f=1;
            $m = 0;
            //Separa los miles para ser evaluados con la función menor_mil
            for ($k = $long_n; $k > $long_n - 3; $k--)
            {
                $m += ($n_str[$k-4] * $f);	//Multiplica por 1,10,y 100
                $f *= 10;
            }

            //Miles que pasan a funcion menor_mil para ser evaluadas
            $miles_txt = $this->menor_mil($m);
            $n = $this->numero_entero - $m*1000 - $mm*1000000;//Elimina los miles y millones
            $n_txt = $this->menor_mil($n);//Unidades, Decenas y Centenas a ser evaluadas
            $letras = $millones_txt.$this->millones.$miles_txt.$this->miles.$n_txt." ".$this->moneda;
        }

        //Evaluación de decimales
        //Si no tiene decimal
        if ( strchr($this->numero,".") == "" )
        {
            return ("(".$letras." 00/100)");
        } else {
            $dec = strchr($this->numero,".");
            $d = $dec['1'].(integer)$dec['2'];
            return ("(".$letras." ".$d."/100)");
        }

    }
}
