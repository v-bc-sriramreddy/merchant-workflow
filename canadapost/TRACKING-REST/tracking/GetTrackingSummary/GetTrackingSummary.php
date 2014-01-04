<?php
 /**
 * Sample code for the GetTrackingSummary Canada Post service.
 * 
 * The GetTrackingSummary service returns the most recent/significant event for a 
 * parcel. If it has been delivered, the delivery details are returned. (The parcel 
 * is identified using PIN, DNC or the other reference parameters).
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
$service_url = 'https://ct.soa-gw.canadapost.ca/vis/track/pin/1681334332936901/summary';
// DNC Summary URI
// $service_url = 'https://ct.soa-gw.canadapost.ca/vis/track/dnc/315052413796541/summary';
// REF Summary URI
// $service_url = 'https://ct.soa-gw.canadapost.ca/vis/track/ref/summary?mailingDateTo=2011-06-25&destinationPostalCode=K2H7X3&mailingDateFrom=2011-08-23&referenceNumber=DIA101';

$curl = curl_init($service_url); // Create REST Request
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); 
curl_setopt($curl, CURLOPT_CAINFO, realpath(dirname($_SERVER['SCRIPT_FILENAME'])) . '/../../../third-party/cert/cacert.pem'); // Mozilla cacerts
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
		
	$trackingSummary = $xml->children('http://www.canadapost.ca/ws/track');
	if ( $trackingSummary->{'pin-summary'} ) {
		foreach ( $trackingSummary as $pinSummary ) {
			echo 'PIN Number: ' . $pinSummary->{'pin'} . "\n";
			echo 'Mailed On Date: ' . $pinSummary->{'mailed-on-date'} . "\n";
			echo 'Event Description: ' . $pinSummary->{'event-description'} . "\n\n";
		}
	} else {
		$messages = $xml->children('http://www.canadapost.ca/ws/messages');		
		foreach ( $messages as $message ) {
			echo 'Error Code: ' . $message->code . "\n";
			echo 'Error Msg: ' . $message->description . "\n\n";
		}
	}
}

?>

