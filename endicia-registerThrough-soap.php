<?php


require_once('nusoap.php'); 


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


$soapclient = new soapclient($URL); 

$params = array( 'XMLInput' => $xmlRequest ); 

$soapclient->call('UserSignup',$params);

echo $soapclient->response;



