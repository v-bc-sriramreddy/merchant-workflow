<?php
 /**
 * Sample code for the VoidShipment Canada Post service.
 * 
 * The VoidShipment service is used to indicate to Canada Post that a printed label 
 * has been spoiled or cancelled and that it will not be used and should not be 
 * transmitted or billed.
 *
 * This sample is configured to access the Developer Program sandbox environment. 
 * Use your development key username and password for the web service credentials.
 * 
 **/

// Your username, password and customer number are imported from the following file    	
// CPCWS_Shipping_PHP_Samples\REST\shipping\user.ini
$userProperties = parse_ini_file(realpath(dirname($_SERVER['SCRIPT_FILENAME'])) . '/../user.ini');

$username = $userProperties['username']; 
$password = $userProperties['password'];
$mailedBy = $userProperties['customerNumber'];
$mobo = $userProperties['customerNumber'];

// REST URL
$service_url = 'https://ct.soa-gw.canadapost.ca/rs/' . $mailedBy . '/' . $mobo . '/shipment/340531309186521749';

$curl = curl_init($service_url); // Create REST Request
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($curl, CURLOPT_CAINFO, realpath(dirname($_SERVER['SCRIPT_FILENAME'])) . '/../../../third-party/cert/cacert.pem'); // Signer Certificate in PEM format
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($curl, CURLOPT_USERPWD, $username . ':' . $password);
$curl_response = curl_exec($curl); // Execute REST Request
if(curl_errno($curl)){
	echo 'Curl error: ' . curl_error($curl) . "\n";
}

echo 'HTTP Response Status: ' . curl_getinfo($curl,CURLINFO_HTTP_CODE) . "\n";

if ( curl_getinfo($curl,CURLINFO_HTTP_CODE) == 204 ) {
	// If server returns HTTP status code 204 then delete is successful.
	echo 'Shipment successfully deleted.' . "\n";
} else {
	// Example of using SimpleXML to parse xml error response
	libxml_use_internal_errors(true);
	$xml = simplexml_load_string('<root>' . preg_replace('/<\?xml.*\?>/','',$curl_response) . '</root>');
	if (!$xml) {
		echo 'Failed loading XML' . "\n";
		echo $curl_response . "\n";
		foreach(libxml_get_errors() as $error) {
			echo "\t" . $error->message;
		}
	}
	if ($xml->{'messages'} ) {					
		$messages = $xml->{'messages'}->children('http://www.canadapost.ca/ws/messages');		
		foreach ( $messages as $message ) {
			echo 'Error Code: ' . $message->code . "\n";
			echo 'Error Msg: ' . $message->description . "\n\n";
		}
	}
}

curl_close($curl);	
?>

