<?php
$client = new SoapClient("https://www.envmgr.com/LabelService/EwsLabelService.asmx?wsdl");
//echo "<b>USING ARRAY</b><br /><br />";
$service_type = 'PriorityMailInternational';
$ounces = '16';
$value = '500.00';
$to_fname = 'Eberhard Anheuser';
$to_company = 'hofbrauhaus';
$to_addr1 = 'Hofbrauallee 1';
$to_city = 'Munich';
$to_state = ' ';
$to_zip5 = '81829';
$to_country = 'Germany';
$to_phone = '6503212640';
$from_fname = 'Bob Smith';
$from_addr1 = '15442 36th Street NW';
$from_city = 'NEWFOLDEN';
$from_state = 'MN';
$from_zip5 = '56738';
$from_zip4 = '';
$from_phone = '2188743305';
$mime = 'GIF';
$data = array
(
'LabelRequest' => array
(
'RequesterID' => 'rtbig',
'AccountID' => '500000',
'PassPhrase' => 'reddy1409',
'MailClass' => $service_type,
'DateAdvance' => 0,
'WeightOz' => $ounces ,
'CostCenter' => 0,
'Value' => $value,
'Services' => array
(
'CertifiedMail' => 'OFF',
'DeliveryConfirmation' => 'OFF',
'ElectronicReturnReceipt' => 'OFF',
'InsuredMail' => 'ENDICIA',
'SignatureConfirmation' => 'OFF'
),
'Description' => 'Sample Label',
'PartnerCustomerID' => '12345ABCD',
'PartnerTransactionID' => '6789EFGH',
'OriginCountry' => 'United States',
'ToName' => $to_fname,
'ToCompany' => $to_company,
'ToAddress1' => $to_addr1,
'ToCity' => $to_city,
'ToState' => $to_state,
'ToPostalCode' => $to_zip5,
'ToCountry' => $to_country,
'ToPhone' => $to_phone,
'FromName' => $from_fname,
'ReturnAddress1' => $from_addr1,
'FromCity' => $from_city,
'FromState' => $from_state,
'FromPostalCode' => $from_zip5,
'FromZIP4' => $from_zip4,
'FromPhone' => $from_phone,
'CustomsQuantity1' => 0,
'CustomsValue1' => 0,
'CustomsWeight1' => 0,
'CustomsQuantity2' => 0,
'CustomsValue2' => 0,
'CustomsWeight2' => 0,
'CustomsQuantity3' => 0,
'CustomsValue3' => 0,
'CustomsWeight3' => 0,
'CustomsQuantity4' => 0,
'CustomsValue4' => 0,
'CustomsWeight4' => 0,
'CustomsQuantity5' => 0,
'CustomsValue5' => 0,
'CustomsWeight5' => 0,
'Test' => 'YES',
'LabelSize' => '6x4',
'LabelType' => 'International',
'ImageFormat' => $mime,
)
);

$result = $client->GetPostageLabel($data);
//$result = $client->__soapCall("GetPostageLabel",$data);
$label = $result->LabelRequestResponse->Label;
$image=$label->Image;
foreach ($image as $key => $value) {
    foreach ($value as $subkey => $subvalue) {
        header("Content-Type: image/gif");
        print_r(base64_decode(($subvalue)));
        
    }

}

$im = $image->Image;
header("Content-Type: image/gif");

print_r(base64_decode($im));



?>
