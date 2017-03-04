<?php

if (!function_exists('json_decode'))
  throw Exception('Please install the json extension to use this class.');
  
if(!function_exists('curl_init'))
  throw Exception('Please install the php5-curl extension to use this class.');

class BitlyShortener extends Exception implements Shortener
{
  /**
   * The version of BitlyShortener
   *
   * @var end point url
   **/
  
  public static $API_URL ='https://api-ssl.bitly.com/v3/shorten?access_token=';
  /**
   * Bit.ly access_token
   *
   * @var string
   **/
  private $api_key;
  
  /**
   * Init object with API key
   * 
   * @var $api_key string
   */
  public function __construct($api_key) {
    $this->api_key = $api_key;
  }
  
   /**
   * Converts an array to an object
   *
   * @return object
   **/
  private function convertArrayToObject($arraydata) {
    $object = new stdClass();
    foreach ($arraydata as $a => $v) {
        $object->{$a} = $v;
    }
    return $object;
  }
  
  /**
   * Shorten a long URL
   *
   * @return mixed
   **/
  public function shorten($url) {
	$call_url = self::$API_URL.$this->api_key;

	if (substr($url, 0, 4) != 'http') {
	  $url = 'http://'.$url;
	}
	
	// Decode the url to bring it to a known state
	$url = urldecode($url);
	// Encode url according to bitly api specs
	$url = rawurlencode($url);

    $api_call = file_get_contents($call_url."&longUrl=".$url);
     
    $returndata = json_decode(utf8_encode($api_call),true);

    if ($returndata['status_txt'] == 'OK') {
		$data = $this->convertArrayToObject($returndata['data']);
        return $data;
    } else {
      return false;
    }
  }

}