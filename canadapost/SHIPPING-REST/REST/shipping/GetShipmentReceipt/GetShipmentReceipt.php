<?php
 /**
 * Sample code for the GetShipmentReceipt Canada Post service.
 * 
 * The GetShipmentReceipt service is used to retrieve the shipment credit card receipt in XML 
 * format for a shipment created by a prior no manifest "create shipment" call using a credit card payment.
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
	if ($xml->{'shipment-cc-receipt'} ) {
		$shipmentReceipt = $xml->{'shipment-cc-receipt'}->children('http://www.canadapost.ca/ws/shipment-v4');
		if ( $shipmentReceipt->{'cc-receipt-details'} ) {
			echo 'Merchant Name: ' . $shipmentReceipt->{'cc-receipt-details'}->{'merchant-name'} . "\n";
			echo 'Merchant URL: ' . $shipmentReceipt->{'cc-receipt-details'}->{'merchant-url'} . "\n";
			echo 'Name on Card: ' . $shipmentReceipt->{'cc-receipt-details'}->{'name-on-card'} . "\n";
			echo 'Auth Code: ' . $shipmentReceipt->{'cc-receipt-details'}->{'auth-code'} . "\n";
			echo 'Auth Timestamp: ' . $shipmentReceipt->{'cc-receipt-details'}->{'auth-timestamp'} . "\n";
			echo 'Card Type: ' . $shipmentReceipt->{'cc-receipt-details'}->{'card-type'} . "\n";
			echo 'Charge Amount: ' . $shipmentReceipt->{'cc-receipt-details'}->{'charge-amount'} . "\n";
			echo 'Currency: ' . $shipmentReceipt->{'cc-receipt-details'}->{'currency'} . "\n";
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

