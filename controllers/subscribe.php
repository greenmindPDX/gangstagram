<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once('http_request.php');

class Subscribe extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
		$this->http_request = new HTTPRequest();
  }
	
	
	//Attempts to create a Pubsubhubbub subscription to Instagram's real-time updates
	//calling this function as URL/subscribe/user
  function user()
  {
		$postfields = array(
		  'client_id'			=> INSTAGRAM_CLIENT_ID,
			'client_secret' => INSTAGRAM_CLIENT_SECRET, 
			'object'				=> 'user',
		  'aspect'				=> 'media', 
			'verify_token'  => 'myVerifyToken',
			'callback_url'  => INSTAGRAM_CALLBACK_URL
		  );
		
		$url = "https://api.instagram.com/v1/subscriptions/";
			
	  $response = $this->http_request->post($url,$postfields);
  }

/***
 * initial GET request sent by Instagram's subscribe endpoint in user()
 * processed GET internally because it's not accepted by CI
 * if GET,  echo out the hub.challenge part of the get response from Instagram to confirm subscription
 * 
 * All subsequent calls to this will be POST notifications from Instagram.
 *
 * $this->post() is scrubbed by CI.
 * only way to access post data is from a PHP wrapper:
 * From php.net: "php://input is a read-only stream that allows you to read raw data from the request body. 
 * In the case of POST requests, it is preferable to use php://input 
 * instead of $HTTP_RAW_POST_DATA as it does not depend on special php.ini directives." 

 * 
 * Results from POST data look like this:

 			  array (
					0 => 
					stdClass::__set_state(array(
					'changed_aspect' => 'media',
					'subscription_id' => 111111,
					'object' => 'user',
					'object_id' => '1111111',
					'time' => 1349212379,
				)),
				)
 
 */			

	function callback()
	{
		if($_SERVER['REQUEST_METHOD'] == "GET")
    {
				$challenge = $_SERVER['QUERY_STRING'];
				$get_array = explode("&", $challenge);
				for($i=0;$i<count($get_array);$i++)
				{
					$check = $get_array[$i];
					if(strstr($check,"hub.challenge"))
					{
						$hub_array=explode("=", $check);
						{
							$hub = $hub_array[1];
						}
					}
				}
		    if($hub)
				{
					echo $hub;
				}

		}
		if($_SERVER['REQUEST_METHOD'] == "POST")
		{
			
			$data = json_decode(file_get_contents('php://input'));		
			//send to API to process
			foreach($data as $obj)
			{
				$object_id = $obj->object_id;
				$time = $obj->time;
				
				$payload = array(
					'time' => $time,
					'object_id' => $object_id
				);
				//now send $payload to a worker or database for processing...
			}			
		}
		
	}
	
		
	
}
