<?php
 /**
 * Sample code for the GetManifestArtifact Canada Post service.
 * 
 * The GetManifestArtifact service is used to retrieve the rendered manifest document(s). 
 * May be called more than once to perform a reprint.
 *
 * This sample is configured to access the Developer Program sandbox environment. 
 * Use your development key username and password for the web service credentials.
 * 
 **/

// Your username and password are imported from the following file
// CPCWS_Shipping_PHP_Samples\REST\shipping\user.ini 
$userProperties = parse_ini_file(realpath(dirname($_SERVER['SCRIPT_FILENAME'])) . '/../user.ini');

$username = $userProperties['username']; 
$password = $userProperties['password'];

// REST URI
$service_url = 'https://ct.soa-gw.canadapost.ca/ers/artifact/' . $username . '/444/0';

$curl = curl_init($service_url); // Create REST Request
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); 
curl_setopt($curl, CURLOPT_CAINFO, realpath(dirname($_SERVER['SCRIPT_FILENAME'])) . '/../../../third-party/cert/cacert.pem'); // Mozilla cacerts
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($curl, CURLOPT_USERPWD, $username . ':' . $password);
curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept:application/pdf', 'Accept-Language:en-CA'));
$curl_response = curl_exec($curl); // Execute REST Request
if(curl_errno($curl)){
	echo 'Curl error: ' . curl_error($curl) . "\n";
}

echo 'HTTP Response Status: ' . curl_getinfo($curl,CURLINFO_HTTP_CODE) . "\n";

$contentType =  curl_getinfo($curl,CURLINFO_CONTENT_TYPE); 

if ( strpos($contentType, 'application/pdf' ) !== FALSE ) {
	// Writing binary response to file
	$fileName = 'manifestArtifact.pdf';
	$filePath = realpath(dirname($_SERVER['SCRIPT_FILENAME'])) . DIRECTORY_SEPARATOR . $fileName;		
	echo 'Writing response to ' . $filePath . "\n";
	file_put_contents($filePath, $curl_response);
} elseif (strpos($contentType, 'xml' ) > -1 ) {
	// Example of using SimpleXML to parse xml error response
	libxml_use_internal_errors(true);
	$xml = simplexml_load_string('<root>' . preg_replace('/<\?xml.*\?>/','',$curl_response) . '</root>');
	if (!$xml) {
		echo 'Failed loading XML' . "\n";
		echo $curl_response . "\n";
		foreach(libxml_get_errors() as $error) {
			echo "\t" . $error->message;
		}
	} else {	
		if ($xml->{'messages'} ) {					
			$messages = $xml->{'messages'}->children('http://www.canadapost.ca/ws/messages');		
			foreach ( $messages as $message ) {
				echo 'Error Code: ' . $message->code . "\n";
				echo 'Error Msg: ' . $message->description . "\n\n";
			}
		}
	}
} else {
	echo 'Unknown Content Type: ' . $contentType . "\n";
}

curl_close($curl);

?>

