
<?php

ini_set('display_errors', '1');
ini_set("soap.wsdl_cache_enabled", "1");

/**
 * A class to handle request and response of Temando API 
 * PHP version 5
 *
 * @category Shipmen_Services
 * @package  Tag
 * @author   Sriram <sriram.bandi@bigcommerce.com>
 * @license  http://temandoprototype.herokuapp.com temando
 * @version  CVS: 1.0
 * @link     http://temandoprototype.herokuapp.com 
 */
class TemandoProcessApp
{
    public $quotesList = array();
    /**
     * Make the quotes List from individual quote
     * 
     * @param array $aQuote quoteArray
     * 
     * @return response arrayDetails
     */
    public function setQuotesList($aQuote)
    {
        // set required properties among all properties of a quote
        $individualQuote = array() ;
        $individualQuote['deliveryMethod']=$aQuote->deliveryMethod;
        $individualQuote['$etaFrom']=$aQuote->etaFrom;
        $individualQuote['$etaTo']=$aQuote->etaTo;
        $individualQuote['$totalPrice']=$aQuote->totalPrice;
        $carrierObj=$aQuote->carrier;
        $individualQuote['companyName']=$carrierObj->companyName;
        
        // Append individual quote to quotes List 
        array_push($this->quotesList, $individualQuote);
    }
    /** 
     * This function get quote details from API and Return the response with status and quotes List
     * 
     * @param array $regionFields        having regionalfield values
     * @param array $dimensionFieldValue having dimensionfield values
     * 
     * @return array
     */
    public function getQuoteDetails ($regionFields=array(),
                                     $dimensionFieldValue=array())
    {
        // Initialize default Values
        $response = array('flag'=>false , "quotesList"=>array(), 'exceptionMessage'=>'');
        $temandoWsdlUrl = "https://training-api.temando.com/schema/2009_06/server.wsdl";
        $requestHeaderUrl = "wsse:http://schemas.xmlsoap.org/ws/2002/04/secext";
        // Create a new SoapHeader containing all your login details.
        $username="temandotest2";
        $password="password";
        $headerSecurityStr = "<Security><UsernameToken><Username>".$username."</Username><Password>".$password."</Password></UsernameToken></Security>";
        $headerSecurityVar = new SoapVar($headerSecurityStr, XSD_ANYXML);
        $soapHeader = new SoapHeader($requestHeaderUrl, 'soapenv:Header', $headerSecurityVar);
        // Create a new SoapClient referencing the Temando WSDL file.
        try
        {
            $client = new SoapClient($temandoWsdlUrl, array( 'soap_version' => SOAP_1_2) );
            // Add the SoapHeader to your SoapClient.
            $client -> __setSoapHeaders(array($soapHeader));
            // Get response using get quotes request  using SoapClient
            $quotesByRequest = array();
            $quotesByRequest["anywhere"] =  $regionFields;
            $quotesByRequest["anythings"] = array($dimensionFieldValue);
            $getQuotesByRequestResponse = $client->getQuotesByRequest($quotesByRequest);
            //Check if response have quote details,Set multiple quotes into quotes list
            if (property_exists($getQuotesByRequestResponse, 'quote')) {
                $quotes = $getQuotesByRequestResponse -> quote;
                if (count($quotes) == 1) {
                    $this -> setQuotesList($quotes);
                } else {
                    foreach ($quotes as $quoteKey => $quoteDetails) {
                        $this -> setQuotesList($quoteDetails);
                    }
                }
                $response['flag'] = true;
                $response['quotesList'] = $this -> quotesList;
            }
        }
        catch(SoapFault $e) { // Soap client exception handling
            $response['exceptionMessage'] = $e -> getMessage();
        }
        catch(Exception $e) { // Exception handling
            $response['exceptionMessage'] = $e -> getMessage();
        }
        return $response;
    }

}
?>
