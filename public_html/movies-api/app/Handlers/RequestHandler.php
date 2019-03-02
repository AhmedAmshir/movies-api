<?php

namespace App\Handlers;

class RequestHandler {

    public function ExtractRequestData($request) {
		
		// var_dump($request);die;
		// $path = $request->path();
		// $path = explode('/', $path);
		
		// if($path[0] == 'api') {
		if ($request->isMethod('post')) {
			$RequestArray = $request->all();
		} else {
			$RequestArray = $request->query();
		}
		// } else {
		// 	$RequestArray = ['lang' => $path[0]];
		// }
    
        
		$_data = [];
		foreach ($RequestArray as $Key => $Value){
			$_data[$Key] = $Value;
		}

		return $_data;
    }
}