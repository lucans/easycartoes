<?php

include('class/DaoBasico.php');
require_once('class/Cartao.php');
require_once('class/Generic.php');
require_once('class/Usuario.php');

session_start();


function toHtmlFormat ($data) 	{
	return implode('/', array_reverse(explode('-', $data)));
}

function toSQLFormat ($data) 	{
	return implode('-', array_reverse(explode('/', $data)));
}

function buildSet ($aDados) {

	$sSet = 'SET ';

	$i = 1;

	foreach ($aDados as $key => $value) {
		foreach ($value as $key2 => $value2) {

			$sSet .= $key2 . " = '" . utf8_decode($value2) . "' ";

				if ($i < sizeof((array)$value)) {
					$sSet .= ", ";
				}
				
			$i++; 
		}
	}

	return $sSet;
}


function ArrayEncode ($array) {
	if(is_object($array)){
		$array = (array) $array;
		$obj = true;
	}
	foreach ($array as $k => $v) {
		if(is_array($v))
			$array[$k] = utf8_encode($v);
		else 
			$array[$k] = utf8_encode($v);		
	}
	if(isset($obj) && $obj === true) $array = (object) $array;
	return $array;
}

function ArrayDecode ($array) {
	if(is_object($array)){
		$array = (array) $array;
		$obj = true;
	}
	foreach ($array as $k => $v) {
		if(is_array($v))
			$array[$k] = utf8_decode($v);
		else 
			$array[$k] = utf8_decode($v);		
	}
	if(isset($obj) && $obj === true) $array = (object) $array;
	return $array;
}


function stringEncode ($string){
	return utf8_encode($string);
}

function stringDecode ($string){
	return utf8_decode($string);
}


?>
