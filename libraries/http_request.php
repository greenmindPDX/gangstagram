<?php

class HTTPRequest
{

  public $CI;
  public $url;
  public $header;

  /**
   * The HTTP STATUS code from the cURL request
   */
  public $status_code;

  /**
   * The HTTP info
   */
  public $http_info;

  /**
   * The body of the HTTP response
   */
  public $http_body;

  /**
   * The headers of the HTTP response
   */
  public $http_response_headers;

  /**
   * Property to contain the cURL connection
   */
  protected $curl_handle;

  /**
   * Property for the cURL response
   */
  protected $curl_response;

  /**
   * Property array of valid http response codes
   */
  protected $response_codes;

  /*
   * Proberty bool flag to not exit and die... used in user controller
   */
  protected $do_not_exit;

  //@todo -> remove no_exit_flag. currently only used in the user controller
  public function __construct($url = null)
  {
    $this->CI = get_instance();
		//supply any necessary headers here
    $this->header = array();
    $this->do_not_exit = false;
    $this->set_valid_response_codes();
    if($url)
    {
      $this->url = $url;
    }
  }

  public function delete($url, $payload = array())
  {
    $payload['_method'] = 'delete';
    $this->url = $url . '?' . http_build_query($payload);
    $this->curl_handle = curl_init();
    curl_setopt($this->curl_handle, CURLOPT_URL, $this->url);
    curl_setopt($this->curl_handle, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($this->curl_handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($this->curl_handle, CURLOPT_HTTPHEADER, $this->header);
    curl_setopt($this->curl_handle, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($this->curl_handle, CURLOPT_HEADER, true);
    $this->curl_response = curl_exec($this->curl_handle);

    $this->parse_curl_response();
    return $this->http_body;
  }

  public function post($url, $payload = array(), $do_not_exit = true)
  {
    //do not exit flag used in user controller.
    if($do_not_exit)
    {
      $this->do_not_exit = true;
    }

    $this->url = $url;
    $this->curl_handle = curl_init();
    $payload = http_build_query($payload);
    curl_setopt($this->curl_handle, CURLOPT_URL, $this->url);
    curl_setopt($this->curl_handle, CURLOPT_POST, true);
    curl_setopt($this->curl_handle, CURLOPT_HTTPHEADER, $this->header);
    curl_setopt($this->curl_handle, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($this->curl_handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($this->curl_handle, CURLOPT_HEADER, true);
    $this->curl_response = curl_exec($this->curl_handle);

    $this->parse_curl_response();
    return $this->http_body;
  }

  /**
   * Make a HTTP GET request
   *
   * @param string $url
   * @param array $payload
   */
  public function get($url, $payload = array(), $do_not_exit = true, $return_transfer = true, $uri = false, $curl_opt_header = true)
  {
    //do not exit flag used in user controller.
    if($do_not_exit)
    {
      $this->do_not_exit = true;
    }

    $this->url = $url;
    $payload_arr = array();
    $curlopt_header = false;
    
    if ($uri)
    {
      foreach($payload as $v)
      {
        $payload_arr[] = urlencode($v);
      }
      $this->url = $url . '/' . implode('/', $payload_arr); 
    }
    else
    {
      foreach($payload as $k => $v)
      {
        $payload_arr[] = urlencode($k) . '=' . urlencode($v);
      }
      $this->url = $url . '?' . implode('&', $payload_arr);
    }
    $this->curl_handle = curl_init();
    curl_setopt($this->curl_handle, CURLOPT_URL, $this->url);
    curl_setopt($this->curl_handle, CURLOPT_HTTPHEADER, $this->header);
    curl_setopt($this->curl_handle, CURLOPT_RETURNTRANSFER, $return_transfer ); //true may kill the response
    curl_setopt($this->curl_handle, CURLOPT_HEADER, $curl_opt_header);
    $this->curl_response = curl_exec($this->curl_handle);

    $this->parse_curl_response();
    return $this->http_body;
  }

  public function put($url, $payload = array())
  {
    $this->url = $url;
    $this->curl_handle = curl_init($this->url);
    curl_setopt($this->curl_handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($this->curl_handle, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($this->curl_handle, CURLOPT_HTTPHEADER, $this->header);
    curl_setopt($this->curl_handle, CURLOPT_POSTFIELDS, http_build_query($payload));
    curl_setopt($this->curl_handle, CURLOPT_HEADER, true);
    $this->curl_response = curl_exec($this->curl_handle);

    $this->parse_curl_response();
    return $this->http_body;
  }

  /**
   * Private method to extract the HTTP headers and HTTP response body
   */
  private function parse_curl_response()
  {
    $this->status_code = curl_getinfo($this->curl_handle, CURLINFO_HTTP_CODE);
    #$this->http_info = array_merge($this->http_info, curl_getinfo($this->curl_handle));

    $header_size = curl_getinfo($this->curl_handle, CURLINFO_HEADER_SIZE);

    // Capture the HTTP response headers
    $this->http_response_headers = substr($this->curl_response, 0, $header_size);

    // Capture the  HTTP response body
    $this->http_body = substr($this->curl_response, $header_size);

    if(!$this->do_not_exit)
    {
      $this->_check_valid_response($this->http_body);
    }
    //close connection
    curl_close($this->curl_handle);
  }

  /**
   * Private method to kill continued execution of bad response
   */
  private function _check_valid_response($http_body_json = '')
  {
    if(!in_array($this->status_code, $this->valid_response_codes))
    {
      //@todo log bad response info
      $body_string = '';
      $body_array = array();
      $body_array = json_decode($http_body_json, true);
      if(!empty($body_array))
      {
        foreach($body_array as $k => $v)
        {
          $body_string .= "$k: $v ";
        }
      }

      $x_message_data = 'X-Chirpify-Message: ' . $body_string;
      header($x_message_data);
      $header_data = 'HTTP/1.1 ' . $this->status_code;
      header($header_data);

      exit();
    }
  }

  /**
   * HTTP response code(s) we want to handle without exiting
   */
  protected function set_valid_response_codes()
  {
    //@todo manage response codes
    $this->valid_response_codes = array(200);
  }

}
