<?php




$URL = "https://www.endicia.com/ELS/ELSServices.cfc?wsdl";
$xmlRequest = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
  <UserSignupRequest> 
    <FirstName>reddy</FirstName> 
    <Test>N</Test> 
    <LastName>Customer</LastName> 
    <EmailAddress>sriram.reddy@bigcommerce.com</EmailAddress> 
    <EmailConfirm>sriram.reddy@bigcommerce.com</EmailConfirm> 
    <PhoneNumber>650-555-1212</PhoneNumber>
    <ICertify>Y</ICertify> 
    <OverrideEmailCheck>N</OverrideEmailCheck> 
    <PhysicalAddress>247 High St.</PhysicalAddress> 
    <PhysicalCity>Palo Alto</PhysicalCity> 
    <PhysicalState>CA</PhysicalState> 
    <PhysicalZipCode>94301</PhysicalZipCode> 
    <WebPassword>Siddi@0608</WebPassword> 
    <PassPhrase>passphrase</PassPhrase> 
    <ChallengeQuestion>Next door?</ChallengeQuestion> 
    <ChallengeAnswer>Dentist</ChallengeAnswer> 

    <PartnerId>lbig</PartnerId> 
    <ProductType>LABELSERVER</ProductType> 
    <CreditCardNumber>4111111111111111</CreditCardNumber> 
    <CreditCardAddress>247 High St.</CreditCardAddress> 
    <CreditCardCity>Palo Alto</CreditCardCity> 
    <CreditCardState>CA</CreditCardState> 
    <CreditCardZipCode>94301</CreditCardZipCode> 
    <CreditCardType>V</CreditCardType> 
    <CreditCardExpMonth>04</CreditCardExpMonth> 
    <CreditCardExpYear>2013</CreditCardExpYear> 
    <PaymentType>CC</PaymentType>
  </UserSignupRequest>
XML;

$curl_handle = curl_init (); 
curl_setopt ($curl_handle, CURLOPT_URL, $URL); 
curl_setopt ($curl_handle, CURLOPT_FOLLOWLOCATION, 1); 
curl_setopt ($curl_handle, CURLOPT_RETURNTRANSFER, 1); 
curl_setopt ($curl_handle, CURLOPT_SSL_VERIFYPEER, 0); 
curl_setopt ($curl_handle, CURLOPT_POST, 1); 
curl_setopt ($curl_handle, CURLOPT_POSTFIELDS, '&method=UserSignup&XMLInput=' . $xmlRequest);

$curl_result = curl_exec ($curl_handle) or die ("There has been a CURL_EXEC error");

curl_close ($curl_handle);
var_dump($curl_result);



