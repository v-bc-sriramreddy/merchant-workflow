<?php
 /**
 * Sample code for the GetSignatureImage Canada Post service.
 * 
 * The GetSignatureImage  service  returns a signature image captured at delivery 
 * of the parcel if available. (The parcel is identified using a PIN only). Please note 
 * the following:
 *   - U.S.A. and international services do not support signature retrieval.
 *   - Signature images are available for 45 days after the last scan.
 *   - Recipients of parcels may request suppression of their signature image from view.
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

// REST URI
$service_url = 'https://ct.soa-gw.canadapost.ca/vis/signatureimage/1371134583769923';

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
	$signature = $xml->children('http://www.canadapost.ca/ws/track');
	if ( $signature->{'filename'} ) {
		echo 'base64 Encoded: ' . $signature->{'image'} . "\n";
		echo 'File name: ' . $signature->{'filename'} . "\n";
		echo 'Mime type: ' . $signature->{'mime-type'} . "\n";
		// Decoding base64 signature to file
		$fileLoc = realpath(dirname($_SERVER['SCRIPT_FILENAME'])) . DIRECTORY_SEPARATOR . $signature->{'filename'};
		echo 'Decoding to ' . $fileLoc . "\n";
		$fp = fopen($fileLoc, 'w');
		stream_filter_append($fp, 'convert.base64-decode');
		fwrite($fp, $signature->{'image'});
		fclose($fp);		
	} else {
		$messages = $xml->children('http://www.canadapost.ca/ws/messages');		
		foreach ( $messages as $message ) {
			echo 'Error Code: ' . $message->code . "\n";
			echo 'Error Msg: ' . $message->description . "\n\n";
		}
	}
}

?>

