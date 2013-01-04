<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


require_once('libraries/Instagram.php');

class Example extends CI_Controller {

  function __construct()
  {
    parent::__construct();
    $this->http_request = new HTTPRequest();
    $this->insta = new Instagram();
  }


//testing Instagram functions with HTTP_request
  function delete_comment()
	{
    $access_token = "321.786";
		$comment_id = "111";
    $media_id = "321_123";
		$data_dump = json_decode($this->insta->delete_comment($comment_id,$media_id,$access_token));
	}
	
	function get_comment()
	{
    $access_token = "321.786";
    $media_id = "321_123";
		$data_dump = json_decode($this->insta->get_comment($media_id,$access_token));
		var_dump($data_dump);
	}
	
	function set_comment()
	{
    $access_token = "321.786";
    $media_id = "321_123";
		$post_text = "this PHP instagram library is swell!";
		$data_dump = json_decode($this->insta->set_comment($post_text,$media_id,$access_token));
		var_dump($data_dump);
	}

	function get_popular_photo()
	{
		$data_dump = json_decode($this->insta->get_popular_photos());
		var_dump($data_dump);
	}
	function get_media()
	{
    $media_id = "321_123";
		$access_token = "321.786";
		$data_dump = json_decode($this->insta->get_media($media_id,$access_token));
		var_dump($data_dump);
	}
	function get_sub()
	{
		$data_dump = json_decode($this->insta->get_subscription());
		var_dump($data_dump);
	}
	
	function get_user_id()
	{
    $access_token = "321.786";
		$handle = "arthurthemoth";
    $data_dump = json_decode($this->insta->get_user_id_by_handle($handle,$access_token));
		var_dump($data_dump);
	}
	function get_user_feed()
	{
    $access_token = "321.786";
    $data_dump = json_decode($this->insta->get_user_profile($access_token));
		var_dump($data_dump);
	}

	function get_user_from_id()
	{
    $access_token = "321.786";
		$user_id = 111;
    $data_dump = json_decode($this->insta->get_user_from_id($user_id,$access_token));
		var_dump($data_dump);
	}
	
	function get_user_recent_media()
	{
    $access_token = "321.786";
		$user_id = 111;
		$options = array(
		  'count' => 5,
			'min_timestamp' =>1352915862
		);
    $data_dump = json_decode($this->insta->get_user_recent_media($user_id,$access_token, $options));
		var_dump($data_dump);
	}


}
