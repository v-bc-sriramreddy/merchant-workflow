<?php
 /**
 * Sample code for the TransmitShipments Canada Post service.
 * 
 * The TransmitShipments service is used to specify shipments to be included in a manifest. 
 * Inclusion in a manifest is specified by group. Specific shipments may be excluded if 
 * desired.
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
$service_url = 'https://ct.soa-gw.canadapost.ca/rs/' . $mailedBy . '/' . $mobo . '/manifest';

// Create CreateShipment request xml
$groupId = '4326432';
$requestedShippingPoint = 'H2B1A0';

$xmlRequest = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<transmit-set xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://www.canadapost.ca/ws/manifest-v4" >
  <group-ids>
    <group-id>{$groupId}</group-id>
  </group-ids>
  <requested-shipping-point>{$requestedShippingPoint}</requested-shipping-point>
  <detailed-manifests>true</detailed-manifests>
  <method-of-payment>Account</method-of-payment>
  <manifest-address>
    <manifest-company>MajorShop</manifest-company>
    <phone-number>514 829 5879</phone-number>
    <address-details>
      <address-line-1>1230 Tako RD.</address-line-1>
      <city>Ottawa</city>
      <prov-state>ON</prov-state>
  	  <country-code>CA</country-code>
      <postal-zip-code>H2B1A0</postal-zip-code>
    </address-details>
  </manifest-address>
</transmit-set>
XML;

$curl = curl_init($service_url); // Create REST Request
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($curl, CURLOPT_CAINFO, realpath(dirname($_SERVER['SCRIPT_FILENAME'])) . '/../../../third-party/cert/cacert.pem'); // Signer Certificate in PEM format
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $xmlRequest);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($curl, CURLOPT_USERPWD, $username . ':' . $password);
curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/vnd.cpc.manifest-v4+xml', 'Accept: application/vnd.cpc.manifest-v4+xml'));
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
	if ($xml->{'manifests'} ) {
		$manifest = $xml->{'manifests'}->children('http://www.canadapost.ca/ws/manifest-v4');
		if ( $manifest->{'link'} ) {
			foreach ( $manifest->{'link'} as $link ) {  
				echo $link->attributes()->{'rel'} . ': ' .$link->attributes()->{'href'} . "\n";
			}
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

