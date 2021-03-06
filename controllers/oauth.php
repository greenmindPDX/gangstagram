<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once('../libraries/http_request.php');

class Oauth  extends CI_Controller {

	/**
	 * Enter your redirect URL here. This MUST match the redirect supplied to Instagram when you registered your client.
	 * Your redirect URL has to be on a public-facing webserver. 
	 * You'll have to define constants somewhere, like CI's constants.php. 
	 * Authentication is 3 basic steps. 
	 * 1. Send user from a link on your app, including your client_id.
	 * 2. if Oauth goes through, Instagram will send a code value in the query string.
	 * 3. Exchange this code via a Curl post request for an access_token. 
	 */
	
	function __construct() 
	{
    $redirect_url = 	"";
    $this->http_request = new HTTPRequest();
		$base_url = INSTAGRAM_API_ROOT;
	  $client_id = INSTAGRAM_CLIENT_ID;
	  $client_secret = INSTAGRAM_CLIENT_SECRET;
	}
	

	/**
	 * method to authenticate Instagram using their dialogue box.  
	 * scope shows what you want your app to be able to do. In this case, we are asking users to post comments and likes on their behalf.
	 */
	
  function send()
	{
		$redirect_url = $this->redirect_url;
		echo "<a href=\"https://instagram.com/oauth/authorize/?scope=likes+comments&client_id=$this->client_id&redirect_uri=$this->redirect_url&response_type=code\">Click here to oauth</a>.";
	}	

  function receive()
	{

		$redirect_url = $this->redirect_url;
    //CI strips out raw GET and POSTS: this is a way to get through those
 		if($_SERVER['REQUEST_METHOD'] == "GET")
    {
			$challenge = $_SERVER['QUERY_STRING'];
			$get_array = explode("&", $challenge);
			for($i=0;$i<count($get_array);$i++)
			{
				$check = $get_array[$i];
				if(strstr($check,"code"))
				{
					$qs_array=explode("=", $check);
					{
						$code = $qs_array[1];
					}
				}
			}
	    echo "code is $qs";
			$postfields = array(
		  'client_id'			=> INSTAGRAM_CLIENT_ID,
			'client_secret' => INSTAGRAM_CLIENT_SECRET, 
			'grant_type'		=> 'authorization_code',
		  'aspect'				=> 'media', 
			'code'					=> $code,
			'redirect_uri'  => $redirect_url
		  );
	  $url = "https://api.instagram.com/oauth/access_token";
		//Step 3 post the following back to Instagram	
	  $response = $this->http->request->post($url,$postfields);
		//response is JSON user object, include's users' access_token.
		echo $response;		
		
	 }
	
	}	
	
}
