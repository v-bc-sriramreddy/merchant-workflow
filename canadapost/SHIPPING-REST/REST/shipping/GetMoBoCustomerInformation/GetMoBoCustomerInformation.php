<?php
 /**
 * Sample code for the GetMOBOCustomerInformation Canada Post service.
 * 
 * The GetMOBOCustomerInformation service retrieves general information about a Canada Post 
 * mailed-on-behalf-of (mobo) customer including contract number, the valid payers and the 
 * allowed methods of payment for each payer. (The mobo customer is identified by the 
 * mailed-on-behalf-of customer number)
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
$service_url = 'https://ct.soa-gw.canadapost.ca/rs/customer/' . $mailedBy . '/behalfof/' . $mobo;

$curl = curl_init($service_url); // Create REST Request
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); 
curl_setopt($curl, CURLOPT_CAINFO, realpath(dirname($_SERVER['SCRIPT_FILENAME'])) . '/../../../third-party/cert/cacert.pem'); // Mozilla cacerts
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($curl, CURLOPT_USERPWD, $username . ':' . $password);
curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept:application/vnd.cpc.customer+xml', 'Accept-Language:en-CA'));
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
	if ($xml->{'behalf-of-customer'} ) {
		$customer = $xml->{'behalf-of-customer'}->children('http://www.canadapost.ca/ws/customer');
		if ( $customer->{'customer-number'} ) {
			echo "\n" . 'Customer Number: ' . $customer->{'customer-number'} . "\n\n";
			echo 'Contract Ids:' . "\n";
			foreach ( $customer->{'contracts'} as $contractId ) {
				echo '- ' . $contractId->{'contract-id'} . "\n";							
			}
			echo "\n" . 'Payers:' . "\n";
			foreach ( $customer->{'authorized-payers'}->{'payer'} as $payer ) {
				echo '- Customer Number: ' . $payer->{'payer-number'} . "\n";
				$i = 0;
				foreach ( $payer->{'methods-of-payment'}->{'method-of-payment'} as $methodOfPayment ) {
					if ( $i == 0 ) {
						echo ' Payment Methods:' . "\n";						
					}
					echo '  - ' . $methodOfPayment . "\n";
					$i++;
				}
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

