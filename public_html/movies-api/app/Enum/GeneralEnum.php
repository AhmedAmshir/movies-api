<?php

namespace App\Enum;

class GeneralEnum {

	public static $API_MOVIES_LIMIT = 5;
	
	public static $API_KEY = 'rdoqs2xdfsm-xsw';

	public static $_locale = ['ar', 'en'];

	public static $greater_or_lower = [
		'greater' => '+', 
		'lower' => '-'
	];

	public static $desc_or_asc = [
		'desc' => '+', 
		'asc' => '-'
	];
}