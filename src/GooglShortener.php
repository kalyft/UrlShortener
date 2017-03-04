<?php

if (!function_exists('json_decode'))
  throw Exception('Please install the json extension to use this class.');
  
if(!function_exists('curl_init'))
  throw Exception('Please install the php5-curl extension to use this class.');

class GooglShortener extends Exception implements Shortener
{
  /**
   * The version of GoogShortener
   *
   * @var string The version number
   **/
  const VERSION = '0.2.0';
  
  /**
   * Goo.gl API key
   * Request your API key here: <https://code.google.com/apis/console/>
   *
   * @var string
   **/
  private $api_key;
  
  /**
   * Goo.gl API URL
   *
   * @var string
   **/
  public static $API_URL = 'https://www.googleapis.com/urlshortener/v1/url?key=';
  
  /**
   * Init object with API key
   * 
   * @var $api_key string
   */
  public function __construct($api_key) {
    $this->api_key = $api_key;
  }
  
  /**
   * Curl opts to call the API
   *
   * @var array
   **/
  public static $CURL_OPTS = array(
      CURLOPT_USERAGENT      => 'GooglShortener',
      CURLOPT_CONNECTTIMEOUT => 5,
      CURLOPT_RETURNTRANSFER => 1,
      CURLOPT_TIMEOUT        => 30
   );
  
  /**
   * Shorten a long URL
   *
   * @return mixed
   **/
  public function shorten($url) {
    $returndata = null;
    $url = urldecode($url);
    // shorten a single link
    $returndata = $this->callApi(array('longUrl' => $url), null, 'post');
    
    return $returndata;
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
   * Calls the Goo.gl API with curl
   *
   * @return string A JSON string
   **/
  private function callApi($post_data, $url_parameter, $method = 'post') {
    // the url to call the api
    $call_url = self::$API_URL.$this->api_key;
    $url_parameter_append = '';
    $post_json = '{';
    
    if(!empty($post_data) && !is_array($post_data)) {
      throw Exception('Please provide an array for the data');
    }
    
    // convert $post_data array to object and then to json
    if(!empty($post_data)) {
      $post_data = $this->convertArrayToObject($post_data);
      $post_json = json_encode($post_data);
    }
    
    // format url parameter
    if(!empty($url_parameter) && is_array($url_parameter)) {
      foreach($url_parameter as $key => $val) {
        $url_parameter_append .= '&'.$key.'='.$val;
      }
    }
    
    // init curl
    $curl = curl_init();
    
    curl_setopt($curl, CURLOPT_URL, $call_url.$url_parameter_append);
    // for some reason CURLOPT_HTTPHEADER don't work in curl_setopt_array...
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt_array($curl, self::$CURL_OPTS);
    
    if($method == 'post') {
      curl_setopt($curl, CURLOPT_POST, true);
      curl_setopt($curl, CURLOPT_POSTFIELDS, $post_json);
    } elseif($method == 'get') {
      curl_setopt($curl, CURLOPT_POST, false);
    } else {
      throw Exception('API call method must be post or get');
    }
    
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
    curl_setopt($curl, CURLOPT_HTTPHEADER,array('Content-Type:application/json; Accept: application/json; charset=utf8'));
    
    // call API
    $result = curl_exec($curl);
    if ($result === false) {
      $exc = new Exception('Curl Error: '.curl_errno($curl).': '.curl_error($curl));
      curl_close($curl);
      throw $exc;
    }
    
    return json_decode($result);
  } 
}