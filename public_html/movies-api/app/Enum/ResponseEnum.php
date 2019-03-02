<?php

namespace App\Enum;

class ResponseEnum {

	public static $StatusCodes = [
		'OK' => array('id' => 200),
		'NotFound' => array('id' => 404),
		'ServerError' => array('id' => 500),
		'Created' => array('id' => 201),
		'BadRequest' => array('id' => 400),
    ];
}