<?php 

	/**
	 * F1 API
	 * @author Daniel Boorn - daniel.boorn@gmail.com
	 * @copyright Daniel Boorn
	 * @license Creative Commons Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
	 * @namespace F1
	 */
	
	/**
	 * 6/13/2013 - Daniel Boorn
	 * The class uses a JSON api_path.js file that defines the API endpoints and paths.
	 * The package include a DocGen utillity for generating and saving the JSON api_path.js file.
	 * However, you do NOT need to edit or geneate this file as it already includes all methods.
	 * This class is chainable! Please see examples before use.
	 *
	 */

	//phpQuery only requrired for those who wish to work with xml
	require_once('vendor/com.github.tobiaszcudnik/phpquery.php');
	require_once('vendor/com.rapiddigitalllc/f1/api.php');
	

	
	# Example of \F1\Exception
	/*
	try{
		$f1 = \F1\API::forge();
	}catch(\F1\Exception $e){
		var_dump($e);
	}
	*/
	
	
	try{
		
		$f1 = \F1\API::forge();//2nd party auto sign-in
		
		# Show available chain paths by id
		//var_dump($f1->paths);
		
		

		# example of household search with array return
		/*
		$r = $f1->households()->search(array(
			'searchFor' => 'Boorn',
			'include'=> 'address,communications,attributes',
		))->get();
		
		if($r['results']['@count']>0){
			echo $r['results']['household'][0]['householdName'];
		}
		var_dump($r);
		*/		
		
		
		# example of house hold search with phpQuery xml doc return
		/*
		$doc = $f1->xml()->households()->search(array(
			'searchFor' => 'Boorn',
		 	'include'=> 'address,communications,attributes',
		))->get();
		echo $doc['householdName']->text();
		var_dump($doc);
		*/
		
		//to switch back to json use: $f1->json() on your next chain request
		 
		# search people (note the json)
		/*
		$r = $f1->json()->people()->search(array( 
			'searchFor' => 'Boorn',
			'include'=> 'address,communications,attributes',		
		))->get();
		var_dump($r);
		*/
		
		# search people (xml with selectors)
		/*
		$doc = $f1->xml()->people()->search(array(
			'searchFor' => 'Boorn',
			'include' => 'address,communications,attributes',
		))->get();
		echo $doc['person']->attr('id');
		echo $doc['person:first maritalStatus']->text();
		echo $doc['person:first communication:first communicationValue']->text();
		*/
		
		# edit person (array or ojbect)
		/*
		$model = $f1->json()->people(Daniel)->edit()->get();
		$model['person']['firstName'] = 'Brandy';
		$model = $f1->people($model['person']['@id'])->update($model);
		*/
		
		# edit person (xml)
		/*
		$personId = 12121222;
		$model = $f1->xml()->people($personId)->edit()->get();
		$model['person']['firstName']->text('Daniel');
		$model = $f1->people($model['person']->attr('id'))->update((string)$model);
		*/
		
		// ... any path defined in the api_paths.js file is chainable ...
		
		/*
		$r = $f1->funds()->list()->get();
		var_dump($r);
		*/
		
		/*
		$r = $f1->events()->list()->get();
		var_dump($r);
		*/
		
		
		//build your own chain to an api method...
	
	
	}catch(\F1\Exception $e){
		var_dump($e);
	}


?>