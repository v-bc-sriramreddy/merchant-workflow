<?php



//$URL = "https://www.envmgr.com/LabelService/EwsLabelService.asmx/ChangePassPhraseXML";
$URL = "https://elstestserver.endicia.com/LabelService/EwsLabelService.asmx/ChangePassPhraseXML";
$xmlRequest = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
 <ChangePassPhraseRequest> 
  <RequesterID>lXXX</RequesterID> 
  <RequestID>rtbig</RequestID> 
  <CertifiedIntermediary> 
    <AccountID>2500901</AccountID> 
    <PassPhrase>reddy1409</PassPhrase> 
  </CertifiedIntermediary> 
  <NewPassPhrase>testphrase</NewPassPhrase> 
 </ChangePassPhraseRequest>
XML;

$curl_handle = curl_init ();
curl_setopt ($curl_handle, CURLOPT_URL, $URL); 
curl_setopt ($curl_handle, CURLOPT_FOLLOWLOCATION, 1); 
curl_setopt ($curl_handle, CURLOPT_RETURNTRANSFER, 1); 
curl_setopt ($curl_handle, CURLOPT_SSL_VERIFYPEER, 0); 
curl_setopt ($curl_handle, CURLOPT_POST, 1); 
curl_setopt ($curl_handle, CURLOPT_POSTFIELDS, '&changePassPhraseRequestXML=' . $xmlRequest);

$curl_result = curl_exec ($curl_handle) or die ("There has been a CURL_EXEC error");

curl_close ($curl_handle);
var_dump($curl_result);



