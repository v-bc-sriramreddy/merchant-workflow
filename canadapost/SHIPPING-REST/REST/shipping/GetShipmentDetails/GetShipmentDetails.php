<?php
 /**
 * Sample code for the GetShipmentDetails Canada Post service.
 * 
 * The GetShipmentDetails service is used to retrieve the shipment details in XML 
 * format for a shipment created by a prior "create shipment" call.
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
$service_url = 'https://ct.soa-gw.canadapost.ca/rs/' . $mailedBy . '/' . $mobo . '/shipment/340531309186521749/details';

$curl = curl_init($service_url); // Create REST Request
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($curl, CURLOPT_CAINFO, realpath(dirname($_SERVER['SCRIPT_FILENAME'])) . '/../../../third-party/cert/cacert.pem'); // Signer Certificate in PEM format
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($curl, CURLOPT_USERPWD, $username . ':' . $password);
curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept:application/vnd.cpc.shipment-v4+xml', 'Accept-Language:en-CA'));
$curl_response = curl_exec($curl); // Execute REST Request
if(curl_errno($curl)){
	echo 'Curl error: ' . curl_error($curl) . "\n";
}

echo 'HTTP Response Status: ' . curl_getinfo($curl,CURLINFO_HTTP_CODE) . "\n";

curl_close($curl);

// Example of using SimpleXML to parse xml response
libxml_use_internal_errors(true);
$xml = simplexml_load_string('<root>' . preg_replace('/<\?xml.*\?>/','',$curl_response) . '</root>');
if (!$xml) {
	echo 'Failed loading XML' . "\n";
	echo $curl_response . "\n";
	foreach(libxml_get_errors() as $error) {
		echo "\t" . $error->message;
	}
} else {		
	if ($xml->{'shipment-details'} ) {
		$shipmentDetails = $xml->{'shipment-details'}->children('http://www.canadapost.ca/ws/shipment-v4');
		if ( $shipmentDetails->{'tracking-pin'} ) {
			echo 'Tracking Pin: ' . $shipmentDetails->{'tracking-pin'} . "\n";
			if ($shipmentDetails->{'shipment-detail'}->{'transmit-shipment'}) {
				echo 'Transmit Shipment: ' . $shipmentDetails->{'shipment-detail'}->{'transmit-shipment'} . "\n";
			}
			else{
				echo 'Group Id: ' . $shipmentDetails->{'shipment-detail'}->{'group-id'} . "\n";
			}
			echo 'Sender Postal Code: ' . $shipmentDetails->{'shipment-detail'}->{'delivery-spec'}->{'sender'}->{'address-details'}->{'postal-zip-code'} . "\n";
			echo 'Destination Postal Code: ' . $shipmentDetails->{'shipment-detail'}->{'delivery-spec'}->{'destination'}->{'address-details'}->{'postal-zip-code'} . "\n";
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

?>

