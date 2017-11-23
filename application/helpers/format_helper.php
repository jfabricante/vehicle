<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('mysql_date')){
	function mysql_date($date){
		return date('Y-m-d', strtotime($date));
	}
}

if ( ! function_exists('oracle_dff_date')){
	function oracle_dff_date($date){
		if($date == NULL OR $date == '0000-00-00'){
			return '-';
		}
		return date('Y/m/d', strtotime($date));
	}
}

if ( ! function_exists('oracle_date')){
	function oracle_date($date){
		if($date == NULL OR $date == '0000-00-00'){
			return '-';
		}
		return date('d-M-y', strtotime($date));
	}
}

if ( ! function_exists('date1')){
	function date1($date){
		if($date == NULL OR $date == '0000-00-00'){
			return '-';
		}
		return date('m/d/Y', strtotime($date));
	}
}

if ( ! function_exists('datetime1')){
	function datetime1($date){
		if($date == NULL OR $date == '0000-00-00'){
			return '-';
		}
		return date('m/d/Y g:i a', strtotime($date));
	}
}





