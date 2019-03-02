<?php

namespace App\Handlers;

use Illuminate\Http\Response;
use App\Enum\ResponseEnum;

class ResponseHandler {

    protected $data;

	public function __construct(){
		$this->data = [];
	}

	public function AddParameter($Value){
		if (isset($Value) && !empty($Value)){
			$this->data = $Value;
		}
	}

	public function OK($Message = NULL){
		return $this->CreateResponse(ResponseEnum::$StatusCodes['OK']['id'], $Message);	
	}

	public function NotFound($Message = NULL){
		return $this->CreateResponse(ResponseEnum::$StatusCodes['NotFound']['id'], $Message);
	}
	
	public function ServerError($Message = NULL){
		return $this->CreateResponse(ResponseEnum::$StatusCodes['ServerError']['id'], $Message);
	}
	
	public function Created($Message = NULL){
		return $this->CreateResponse(ResponseEnum::$StatusCodes['Created']['id'], $Message);
	}

	public function BadRequest($Message = NULL){
		return $this->CreateResponse(ResponseEnum::$StatusCodes['BadRequest']['id'], $Message);
	}

	private function CreateResponse($StatusCode, $Message){
		if (isset($StatusCode) && !empty($StatusCode)){
			$response['status_code'] = $StatusCode;
		} else {
			$StatusCode = ResponseEnum::$StatusCodes['OK']['id'];
		}
		if (isset($Message) && !empty($Message)){
			$response['Message'] = $Message;
		}
		$response['data'] = $this->data;
		$this->data = json_encode($response);
		return new Response(
			$this->data,
			$StatusCode,
			array(
				'Content-Type' => 'application/json' 
			)
		);
	}
}