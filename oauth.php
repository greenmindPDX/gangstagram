<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once('http_request.php');

class Oauth  extends CI_Controller {

	
	function __construct() 
	{
		//enter your redirect URL here. This MUST match the redirect supplied to Instagram when you registered your client.
   	$redirect_url = 	"";
    $this->http_request = new HTTPRequest();
		$base_url = INSTAGRAM_API_ROOT;
	  $client_id = INSTAGRAM_CLIENT_ID;
	  $client_secret = INSTAGRAM_CLIENT_SECRET;
	}
	
	//routed as url/Oauth/send in CI
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
		//response is JSON user objec
		echo $response;		
		
	 }
	
	}	
	
}
