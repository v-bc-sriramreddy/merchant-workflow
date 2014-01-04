<?php
 /**
 * Sample code for the GetMerchantRegistrationInfo Canada Post service.
 * 
 * The GetMerchantRegistrationInfo service is called by the ecommerce platform after 
 * the merchant has completed the Canada Post sign-up process. This call returns
 * merchant data such as customer number and merchant username and password. This
 * information is necessary for the platform to perform web service shipping
 * transactions for the merchant.
 *
 * This sample is configured to access the Developer Program sandbox environment. 
 * Use your development key username and password for the web service credentials.
 * 
 **/

// Your username and password are imported from the following file
// CPCWS_Platforms_PHP_Samples\REST\platforms\user.ini
$userProperties = parse_ini_file(realpath(dirname($_SERVER['SCRIPT_FILENAME'])) . '/../user.ini');

$username = $userProperties['username']; 
$password = $userProperties['password'];

$username = "532480e548f1a09d";
$password = "76d0546f8d76f6628685fb";

// REST URI
$service_url = 'https://ct.soa-gw.canadapost.ca/ot/token/1111111111111111111111';

$curl = curl_init($service_url); // Create REST Request
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); 
curl_setopt($curl, CURLOPT_CAINFO, realpath(dirname($_SERVER['SCRIPT_FILENAME'])) . '/../../third-party/cert/cacert.pem'); // Mozilla cacerts
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($curl, CURLOPT_USERPWD, $username . ':' . $password);
curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept:application/vnd.cpc.registration+xml', 'Accept-Language:en-CA', 'platform-id:8208839'));
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
		
	$merchantInfo = $xml->children('http://www.canadapost.ca/ws/merchant/registration');
	var_dump($merchantInfo);
	if ( $merchantInfo ) {
		if ( $merchantInfo->{'customer-number'} ) {
			echo 'Customer Number: ' . $merchantInfo->{'customer-number'} . "\n";
		}
		if ( $merchantInfo->{'contract-number'} ) {
			echo 'Contract Number: ' . $merchantInfo->{'contract-number'} . "\n";
		}
		if ( $merchantInfo->{'merchant-username'} ) {
			echo 'Merchant Username: ' . $merchantInfo->{'merchant-username'} . "\n";
		}
		if ( $merchantInfo->{'merchant-password'} ) {
			echo 'Merchant Password: ' . $merchantInfo->{'merchant-password'} . "\n";
		}
		echo 'Has Default CC:  ' . $merchantInfo->{'has-default-credit-card'} . "\n";
	} else {
		$messages = $xml->children('http://www.canadapost.ca/ws/messages');
		foreach ( $messages as $message ) {
			echo 'Error Code: ' . $message->code . "\n";
			echo 'Error Msg: ' . $message->description . "\n\n";
		}
	}
}

?>

