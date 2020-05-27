<?php 
	ini_set("max_execution_time", 60 * 60);

	// Set global variables
	$GLOBALS["sleepSeconds"] = 10;

	// Include required files
	require('vendor/autoload.php');
	require("helpers/spintax.php");
	include("scripts/login.php");
	include("scripts/boards.php");
	require("scripts/auto_pins.php");
	require("scripts/auto_repins.php");
	require("scripts/add_comments.php");
	require("scripts/auto_follow.php");
	require("scripts/direct_msg.php");

	$accounts = json_decode(file_get_contents("data/account.json"),true);
	foreach($accounts as $key => $value)
	{
		foreach ($value as $key_type => $type_value) {

			if($key_type == 'password')
			{
				continue;
			}

			$delaytime = 3600 * 24 * 1000;
			if($type_value['frequency'] == 'daily')
			{
				$delaytime = $delaytime * 1;
			}
			else if($type_value['frequency'] == 'weekly')
			{
				$delaytime = $delaytime * 7;
			}
			else if($type_value['frequency'] == 'monthly')
			{
				$delaytime = $delaytime * 30;
			}

			if(time() - $type_value['time'] > $delaytime)
		    {
		    	switch ($key_type) {
				    case "pins":

				    $bot = new Pins($key, $value['password'], $type_value['counter']);
				    $results = $bot->createPins();
				    break;

				    case "repins":
				    $bot = new AutoRepins($key, $value['password'], $type_value['keywords'], $type_value['counter']);
				    $results = $bot->createRepins();
				    break;
				    
				    case "add_comments":
				    $bot = new AutoAddComments($key, $value['password'], $type_value['keywords'], $type_value['counter']);
				    $results = $bot->createComments();
				    break;
				    
				    case "follow":
				    $bot = new AutoFollow($key, $value['password'], $type_value['keywords'], $type_value['counter']);
				    $results = $bot->startFollowing();
				    break;
				    
				    case "direct_msg":
				    $bot = new AutoDirectMessage($key, $value['password'], $type_value['keywords'], $type_value['counter']);
				    $results = $bot->sendMessages();
				    break;
			  	}

			  	$type_value['time'] = time();
		    }
			

		}
	}

?>