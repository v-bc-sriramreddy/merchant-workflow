<?php
 /**
 * Sample code for the CreateShipment Canada Post service.
 * 
 * The CreateShipment service is used to create a new shipping item, to 
 * request the generation of a softcopy image of shipping labels, and to provide 
 * links to these shipping labels and other information associated with the 
 * shipping item.. 
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
$service_url = 'https://ct.soa-gw.canadapost.ca/rs/' . $mailedBy . '/' . $mobo . '/shipment';

// Create CreateShipment request xml
$groupId = '4326432';
$requestedShippingPoint = 'H2B1A0';
$mailingDate = '2012-10-24';
$contractId = '0040662521';

$xmlRequest = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<shipment xmlns="http://www.canadapost.ca/ws/shipment-v4">
	<group-id>{$groupId}</group-id>
	<!-- <transmit-shipment>true</transmit-shipment> -->
	<requested-shipping-point>{$requestedShippingPoint}</requested-shipping-point>
	<expected-mailing-date>{$mailingDate}</expected-mailing-date>
	<delivery-spec>
		<service-code>DOM.EP</service-code>
			<sender>
				<name>Bulma</name>
				<company>Capsule Corp.</company>
				<contact-phone>1 (514) 820 5879</contact-phone>
				<address-details>
					<address-line-1>502 MAIN ST N</address-line-1>
					<city>MONTREAL</city>
					<prov-state>QC</prov-state>
					<country-code>CA</country-code>
					<postal-zip-code>H2B1A0</postal-zip-code>
				</address-details>
			</sender>
			<destination>
				<name>John Doe</name>
				<company>ACME Corp</company>
				<address-details>
					<address-line-1>123 Postal Drive</address-line-1>
					<city>Ottawa</city>
					<prov-state>ON</prov-state>
					<country-code>CA</country-code>
					<postal-zip-code>K1P5Z9</postal-zip-code>
				</address-details>
			</destination>
		<options>
			<option>
				<option-code>DC</option-code>
			</option>
		</options>
		<parcel-characteristics>
			<weight>15</weight>
			<dimensions>
				<length>6</length>
				<width>12</width>
				<height>9</height>
			</dimensions>
			<unpackaged>false</unpackaged>
			<mailing-tube>false</mailing-tube>
		</parcel-characteristics>
		<notification>
			<email>ryuko.saito@kubere.com</email>
			<on-shipment>true</on-shipment>
			<on-exception>false</on-exception>
			<on-delivery>true</on-delivery>
		</notification>
		<print-preferences>
			<output-format>8.5x11</output-format>
		</print-preferences>
		<preferences>
			<show-packing-instructions>true</show-packing-instructions>
			<show-postage-rate>false</show-postage-rate>
			<show-insured-value>true</show-insured-value>
		</preferences>
		<settlement-info>
			<contract-id>{$contractId}</contract-id>
			<intended-method-of-payment>Account</intended-method-of-payment>
		</settlement-info>
	</delivery-spec>
</shipment>
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
curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/vnd.cpc.shipment-v4+xml', 'Accept: application/vnd.cpc.shipment-v4+xml'));
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
	if ($xml->{'shipment-info'} ) {
		$shipment = $xml->{'shipment-info'}->children('http://www.canadapost.ca/ws/shipment-v4');
		if ( $shipment->{'shipment-id'} ) {
	        echo  'Shipment Id: ' . $shipment->{'shipment-id'} . "\n";                 
			foreach ( $shipment->{'links'}->{'link'} as $link ) {  
				echo $link->attributes()->{'rel'} . ': ' . $link->attributes()->{'href'} . "\n";
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

