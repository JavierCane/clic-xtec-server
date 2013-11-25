<?
// Si no existeix la funció getallheaders() la creem
if (!function_exists('getallheaders'))
{
    function getallheaders()
    {
           $headers = '';
       foreach ($_SERVER as $name => $value)
       {
           if (substr($name, 0, 5) == 'HTTP_')
           {
               $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
           }
       }
       return $headers;
    }
} 

class HTML{
	public static function do_GET($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
		curl_close($ch);
		return $output;
	}

	public static function getHTTPHeader($header){
		foreach (getallheaders() as $name => $value) {
		    if(strtolower($name) == strtolower($header))
		    		return $value;
		}
	}

}
?>