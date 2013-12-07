<?php
 /**
 * Sample code for the GetMerchantRegistrationToken Canada Post service.
 * 
 * The GetMerchantRegistrationToken service returns a unique registration token that is used
 * to launch a merchant into the Canada Post sign-up process.
 *
 * This sample is configured to access the Developer Program sandbox environment. 
 * Use your development key username and password for the web service credentials.
 * 
 **/

// Your username and password are imported from the following file
// CPCWS_Platforms_PHP_Samples\REST\platforms\user.ini


$username = '1f24a3ffca77adb5'; 
$password = '8d9642d785d704d3db806a';


$platformId = '8208839';

// REST URI
$service_url = 'https://soa-gw.canadapost.ca/ot/token';

$curl = curl_init($service_url); // Create REST Request
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); 
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, '');
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
        
    $token = $xml->children('http://www.canadapost.ca/ws/merchant/registration');
    if ( $token ) {
        echo 'Token Id: ' . $token->{'token-id'} . "\n";
    } else {
        $messages = $xml->children('http://www.canadapost.ca/ws/messages');
        foreach ( $messages as $message ) {
            echo 'Error Code: ' . $message->code . "\n";
            echo 'Error Msg: ' . $message->description . "\n\n";
        }
    }
}

?>

