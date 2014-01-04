<?php
 /**
 * Sample code for the GetTrackingDetails Canada Post service.
 * 
 * The GetTrackingDetails service  returns all tracking events recorded for a specified 
 * parcel. (The parcel is identified using a PIN or DNC).
 *
 * This sample is configured to access the Developer Program sandbox environment. 
 * Use your development key username and password for the web service credentials.
 * 
 **/

// Your username and password are imported from the following file
// CPCWS_Tracking_PHP_Samples\REST\tracking\user.ini
$userProperties = parse_ini_file(realpath(dirname($_SERVER['SCRIPT_FILENAME'])) . '/../user.ini');

$username = $userProperties['username']; 
$password = $userProperties['password'];

// PIN Summary URI
$service_url = 'https://ct.soa-gw.canadapost.ca/vis/track/pin/1371134583769923/details';
// DNC Summary URI
// $service_url = 'https://ct.soa-gw.canadapost.ca/vis/track/dnc/315052413796541/details';

$curl = curl_init($service_url); // Create REST Request
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); 
curl_setopt($curl, CURLOPT_CAINFO, realpath(dirname($_SERVER['SCRIPT_FILENAME'])) . '/../../third-party/cert/cacert.pem'); // Mozilla cacerts
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($curl, CURLOPT_USERPWD, $username . ':' . $password);
curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept:application/vnd.cpc.track+xml', 'Accept-Language:en-CA'));
$curl_response = curl_exec($curl); // Execute REST Request
if(curl_errno($curl)){
	echo 'Curl error: ' . curl_error($curl) . "\n";
}

echo 'HTTP Response Status: ' . curl_getinfo($curl,CURLINFO_HTTP_CODE) . "\n";

curl_close($curl);

// Example of using SimpleXML to parse xml response
libxml_use_internal_errors(true);
$xml = simplexml_load_string($curl_response);
if (!$xml) {
	echo 'Failed loading XML' . "\n";
	echo $curl_response . "\n";
	foreach(libxml_get_errors() as $error) {
		echo "\t" . $error->message;
	}
} else {
		
	$trackingDetail = $xml->children('http://www.canadapost.ca/ws/track');
	if ( $trackingDetail->{'pin'} ) {
		echo 'PIN Number: ' . $trackingDetail->{'pin'} . "\n";
		echo 'Signature Exists: ' . $trackingDetail->{'signature-image-exists'} . "\n";
		echo 'Suppress Signature: ' . $trackingDetail->{'suppress-signature'} . "\n";
	} else {
		$messages = $xml->children('http://www.canadapost.ca/ws/messages');		
		foreach ( $messages as $message ) {
			echo 'Error Code: ' . $message->code . "\n";
			echo 'Error Msg: ' . $message->description . "\n\n";
		}
	}
}

?>

