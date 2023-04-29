#!/usr/bin/php
<?php
$ROOT = "/home/sjm/git/IT490";
require_once("$ROOT/rabbit/path.inc");
require_once("$ROOT/rabbit/get_host_info.inc");
require_once("$ROOT/rabbit/rabbitMQLib.inc");

//NEW FUNCTIONS ADDED FOR API USE 

function groceryList($grocerylist)
{
	//basic search URL, doesn't have a query included 
        $testURL="https://spoonacular-recipe-food-nutrition-v1.p.rapidapi.com/food/ingredients/search?query=";

       	//split user input based on whitespace to format for URL
        $words = preg_split('/\s+/', $grocerylist, -1, PREG_SPLIT_NO_EMPTY);

        //append each individual word to the URL
        for($x=0; $x < sizeof($words)-1; $x++){ $testURL .= $words[$x] . "%20";}
        $testURL .= end($words);


	//CODE FROM API SITE - Use "Search Ingredients" function
	$curl = curl_init();
	curl_setopt_array($curl, [
		CURLOPT_URL => $testURL,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "GET",
		CURLOPT_HTTPHEADER => [
			"X-RapidAPI-Host: spoonacular-recipe-food-nutrition-v1.p.rapidapi.com",
			"X-RapidAPI-Key: 90976c7ed0msh995f89aedd012f2p15e099jsnc5dc454fb019"
		],
	]);

	$response = curl_exec($curl);
	$err = curl_error($curl);

	curl_close($curl);

	if ($err) {echo "cURL Error #:" . $err;}
	else {
		//if functionality produces results, return them to DB
		if (json_decode($response, false)->totalResults != 0) { echo $response; return $response; }
		
		//if ingredient isn't found using this functionality, then use the "Search Products" function
		else{


			//basic search URL, doesn't have a query included 
       	 		$testURL="https://spoonacular-recipe-food-nutrition-v1.p.rapidapi.com/food/products/search?query=";

			//split user input based on whitespace to format for URL
        		$words = preg_split('/\s+/', $grocerylist, -1, PREG_SPLIT_NO_EMPTY);

        		//append each individual word to the URL
        		for($x=0; $x < sizeof($words)-1; $x++){ $testURL .= $words[$x] . "%20";}
        		$testURL .= end($words);

			//CODE FROM API SITE - Use "Search Products" functionality 
			$curl = curl_init();
			curl_setopt_array($curl, [
				CURLOPT_URL => $testURL,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "GET",
				CURLOPT_HTTPHEADER => [
					"X-RapidAPI-Host: spoonacular-recipe-food-nutrition-v1.p.rapidapi.com",
					"X-RapidAPI-Key: 90976c7ed0msh995f89aedd012f2p15e099jsnc5dc454fb019"
				],
			]);

			$response = curl_exec($curl);
			$err = curl_error($curl);

			curl_close($curl);

			if ($err) {echo "cURL Error #:" . $err;}
			else {echo $response; return $response;}
		}

		}
}
function groceryRecipe($groceryrecipe)
{ 
	//basic search URL, doesn't have a query included 
	$testURL = "https://spoonacular-recipe-food-nutrition-v1.p.rapidapi.com/recipes/findByIngredients?ingredients=";
	
	//append each individual word to the URL (account for multi-word grocery items
	$toFind = " ";
	
	for($x=0; $x < sizeof($groceryrecipe)-1; $x++){
        	if (strpos($groceryrecipe[$x][0], $toFind) == false) { $testURL .= $groceryrecipe[$x][0] . "%2C";}
        	else{
                	$temp = explode(" ", $groceryrecipe[$x][0]);
                	for ($y=0; $y < sizeof($temp)-1; $y++){ $testURL .= $temp[$y] . "%20";}
                	$testURL .= end($temp) ."%2C";
        	}
	}


	if (strpos(end($groceryrecipe)[0], $toFind) == false){$testURL .= end($groceryrecipe)[0];}
	else{
        	$temp = explode(" ", end($groceryrecipe)[0]);
        	for ($y=0; $y < sizeof($temp)-1; $y++){ $testURL .= $temp[$y] . "%20";}
        	$testURL .= end($temp);
	}

	echo $testURL;

	//CODE FROM API SITE - Use "Search Recipes By Ingredients" function
	$curl = curl_init();
	curl_setopt_array($curl, [
		CURLOPT_URL => $testURL,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "GET",
		CURLOPT_HTTPHEADER => [
			"X-RapidAPI-Host: spoonacular-recipe-food-nutrition-v1.p.rapidapi.com",
			"X-RapidAPI-Key: 90976c7ed0msh995f89aedd012f2p15e099jsnc5dc454fb019"
		],
	]);

	$response = curl_exec($curl);
	$err = curl_error($curl);

	curl_close($curl);

	if ($err) {echo "cURL Error #:" . $err;} 
	else {
		//echo $response; 
		//return $response;
		}
	
	
	$finalResult = []; 
	$lenResponse = sizeof(json_decode($response));

	for($x=0; $x<$lenResponse; $x++){
		$decoded = json_decode($response, true);
		$recipeID = $decoded[$x]["id"]; 	

		$testURL2 = "https://spoonacular-recipe-food-nutrition-v1.p.rapidapi.com/recipes/" . $recipeID ."/information"; 

		//echo $testURL2 .PHP_EOL . PHP_EOL; 
		
		$curl2 = curl_init();

		curl_setopt_array($curl2, [
			CURLOPT_URL => $testURL2,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => [
				"X-RapidAPI-Host: spoonacular-recipe-food-nutrition-v1.p.rapidapi.com",
				"X-RapidAPI-Key: 90976c7ed0msh995f89aedd012f2p15e099jsnc5dc454fb019"
			],
		]);

		$response2 = curl_exec($curl2);
		$err2 = curl_error($curl2);

		curl_close($curl2);

		if ($err2) {echo "cURL Error #:" . $err2;} 
		else {
			$decoded2 = json_decode($response2, true); 
			array_push($finalResult, $decoded2);
		}
	}
	
	return json_encode($finalResult);
}


function keywordRecipe($keywordrecipe)
{
        //basic search URL, doesn't have a query included 
        $testURL="https://spoonacular-recipe-food-nutrition-v1.p.rapidapi.com/recipes/search?query=";

        //split user input based on whitespace to format for URL
        $words = preg_split('/\s+/', $keywordrecipe, -1, PREG_SPLIT_NO_EMPTY);

        //append each individual word to the URL
        for($x=0; $x < sizeof($words)-1; $x++){ $testURL .= $words[$x] . "%20";}
        $testURL .= end($words);

        //CODE FROM API SITE - use "Search Recipes" function 
        $curl = curl_init();
        curl_setopt_array($curl, [
                CURLOPT_URL => $testURL,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => [
                        "X-RapidAPI-Host: spoonacular-recipe-food-nutrition-v1.p.rapidapi.com",
                        "X-RapidAPI-Key: 90976c7ed0msh995f89aedd012f2p15e099jsnc5dc454fb019"
                ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {echo "cURL Error #:" . $err; }
        else {echo $response; return $response;}
}


function requestProcessor($request)
{
  echo "received request".PHP_EOL;
  
  try{
  var_dump($request);
  if(!isset($request['type']))
  {
    return "ERROR: unsupported message type";
  }
  switch ($request['type'])
  {
    case "login":
      return doLogin($request['username'],$request['password']);
    case "validate_session":
      return doValidate($request['sessionId']);
    case "keywordrecipe":
      return keywordRecipe($request['keywordrecipe']);
    case "groceryrecipe":
      return groceryRecipe($request['groceryrecipe']);
    case "expirerecipe":
      return groceryRecipe($request['expirerecipe']);
    case "grocerylist":
      return groceryList($request['grocerylist']);
	
  }
}

catch(Exception $e){
    $errClient = new rabbitMQClient("$ROOT/error_log/errorServerMQ.ini","errorServer");
    $errClient->send_request(['type' => 'DMZerrors', 'error' => $e->getMessage()]);
}


  return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

$server = new rabbitMQServer("$ROOT/dmz/dmzServerMQ.ini","DMZServer");

//echo "testRabbitMQServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
//echo "testRabbitMQServer END".PHP_EOL;
exit();
?>
