<?php
/*
 * Based on http://instagram.com/developer/endpoints/
 * most of these functions require the access token.
 * to get an access token, you need to get an authorized user for the client. 
 * when you register your app at http://instagram.com/developer/register/
 * you receive a CLIENT_ID and CLIENT_SECRET
 * 
 * if you don't need to be authorized, you only need a client id. 
 * 
 * SCOPE: 
 * scope=likes+comments
 * 
 */
require_once('http_request.php');

class Instagram
{
	
  public $base_url = INSTAGRAM_API_ROOT;
	protected $client_id = INSTAGRAM_CLIENT_ID;
	protected $client_secret = INSTAGRAM_CLIENT_SECRET;


	function __construct() 
	{
    $this->http_request = new HTTPRequest();
	}
	
	/*
	 * Comments Functions
	 * 
	 */

  /**
   * Deletes an Instagram comment
   * 
   * ### Requires
   *  
   *  * `access_token` (string) Instagram users' oauth
		  * `comment_id` (string)   ID of the instagram comment
   *  * `media_id` (string)      ID of the instagram media
	 *
	 * 	 * ### Returns
   *
   *   * NULL
	 * 
   */

	public function delete_comment($comment_id,$media_id,$access_token)
	{
		$payload = array(
			"access_token" => $access_token,
			"_method"				=> 'DELETE'
		);

		$endpoint = $this->base_url."media/$media_id/comments/$comment_id";
	  $result = $this->http_request->post($endpoint,$payload);	
	}

  /**
   * Returns an Instagram comment
   * 
   * ### Requires
   *  
   *  * `access_token` (string) Instagram users' oauth
	 *	* `media_id` (string)      ID of the instagram media
	 * 
	 * ### Returns
   *
   *   * JSON object with array of comments

   */

	public function get_comment($media_id,$access_token)
	{
		$endpoint = $this->base_url."media/$media_id/comments";
		$payload = array(
			'access_token' => $access_token
		);
		$result = $this->http_request->get($endpoint,$payload);
		return $result;
	}

	
  /**
   * Adds an Instagram comment to a piece of media
   * 
	 * ### You must email "apidevelopers@instagram.com" for permission to set comments on behalf of a user. 
	 * 
	 *  * ### Requires 
   *
   *  * `access_token` (string) Instagram users' oauth
	 *	* `media_id` (string)      ID of the instagram media
	 *  * `post_text` (string)    Text to add to the comment

	 *  * ### Returns
   *
   *   * JSON object with newest created comment

   */

	public function set_comment($post_text,$media_id,$access_token)
	{
		$endpoint = $this->base_url."media/$media_id/comments";
		$payload = array(
			"access_token" => $access_token,
			"text"				 => $post_text
		);
		$result = $this->http_request->post($endpoint,$payload);
		return $result;
	}


	/**
	 * 
	 * Media Functions
	 */
	
	 /**
   * Returns an array of popular photos
   * 
   * ### Requires
   *  
	 *	* `client_id` (string)  ID of application
	 * 
	 * 
	 * ### Returns
   *
   *   * JSON object with array of popular photos
   */
	
	public function get_popular_photos()
	{
		$endpoint = $this->base_url.'media/popular';
		$payload = array(
		  'client_id' => $this->client_id		
		);
		$result = $this->http_request->get($endpoint, $payload);
		return $result;
	}

	 /**
   * Returns information on a particular media_id
   * 
   * ### Requires
   *  * `access_token` (string) Instagram users' oauth OR `client_id`
	 *	* `media_id` (string)   ID of the instagram media
	 * 
	 * ### Returns
   *
   *   * JSON object pertaining to a piece of media

   */
	
	public function get_media($media_id, $access_token)
	{
		$endpoint = $this->base_url."media/$media_id";
		$payload = array(
	    'access_token' => $access_token		
		);

		$result = $this->http_request->get($endpoint, $payload);
		return $result;
	}

	 /**
   * Returns information on real-time pubsubhubbub. Not implemented here as it was leaky. 
   * 
   * ### Requires
   *  
	 *	* `client_id` (string)			ID of the application
	 *  * `client_secret` (string)	
	 * ### Returns
   *
   *   * JSON object pertaining to the pubsubhubub real-time updates your application subscribed to

   */
	
	public function get_subscription()
	{
		
		$endpoint = $this->base_url."subscriptions";
		$payload = array(
			'client_id'			=> $this->client_id,
			'client_secret' => $this->client_secret
		);
		
		$result = $this->http_request->get($endpoint, $payload);
		return $result;
	}
	
	/*
	 * User Functions
	 */
	
	
  /**
   * Returns a user's profile
   * 
   * ### Requires
   *  
   *  * `access_token` (string) Instagram users' oauth
	 * 
	 * ### Returns
   *
   *   * JSON object with user's basic information

   */
	
	public function get_user_profile($access_token)
	{
		$endpoint = $this->base_url."users/self";
		$payload = array(
		  "access_token" => $access_token		
		);
		$result = $this->http_request->get($endpoint,$payload);
		return $result;
	}

  /**
   * Returns a user's ID,name, website, other public data in profile
   * 
   * ### Requires
   *
	 * 	 * `access_token` (string) Instagram user's oauth
	 *	 * `handle` (string_ Instagram user's name
	 * 
	 * ### Returns
   *
   *   * JSON object with user data
   */
	
	public function get_user_id_by_handle($handle,$access_token)
	{
		$endpoint = $this->base_url."users/search";
		$payload = array(
			'access_token' => $access_token,
		  'q' => $handle		
		);
		$result = $this->http_request->get($endpoint,$payload);
		return $result;
	}
	
  /**
   * Returns a user's public data in profile
   * 
   * ### Requires
   *
	 * 	 * `access_token` (string) Instagram user's oauth
	 *	 * `user_id` (string) Instagram user's ID
	 * 
	 * ### Returns
   *
   *   * JSON object with user data
   */
					
	public function get_user_from_id($user_id,$access_token)
	{
		$endpoint = $this->base_url."users/$user_id/";
		$payload = array(
		  "access_token" => $access_token		
		);		
		$result = $this->http_request->get($endpoint,$payload);
		return $result;
	}
	
	
  /**
   * Returns a user's recent media feed
   * 
   * ### Requires
   *
	 * 	 * `access_token` (string) Instagram user's oauth
	 *	 * `user_id` (string) Instagram user's ID
	 * 
	 * ### OPTIONAL
	 * Several Instagram API methods offer optional data, such as count, lat/long, min/max timestamps. They are implemented as an example here.
	 * 
	 *  count	Count of media to return.
	 *	min_id (UTC TIMESTAMP)	Return media later than this min_id.
	 *	max_id (UTC TIMESTAMP)	Return media earlier than this max_id.s
	 * 
	 * ### Returns
   *
   *   * JSON object with user data
   */
	
	
	
	public function get_user_recent_media($user_id,$access_token,$options=NULL)
	{
		
		$payload = array(
		  "access_token" => $access_token		
		);
		
		if(is_array($options))
		{
			foreach ($options as $key => $value)
			{
				$payload["$key"] = $value;
			}
			
		}
		print_r($payload);
		$endpoint = $this->base_url."users/$user_id/media/recent/";
		#echo "$endpoint";
		$result = $this->http_request->get($endpoint,$payload);
		return $result;
	}
	
}
