<?php 

/**
 * A class to validate and set form values into respective properties
 *
 * PHP version 5
 *
 * @category   Shipment Services
 * @author     Reddy <sriram.reddy@bigcommerce.com>
 * @author     Sriram <sriram.bandi@bigcommerce.com>
 * @version    Prototype
 * @link       http://temandoprototype.herokuapp.com
 * 
 */
    
    class RequestApp
    {
        // Initialize Field names and form errors
        public $regionFields = array( "originIs" => "", "originCode" => "", "originSuburb" => "", "destinationIs" => "" ,
                                    "destinationCode" => "", "destinationSuburb" => "" );
        public $dimensionFields = array ( "packaging" => "", "length" => "", "width" => "", "height" => "", "weight" => "" );
        private $numericFieldsArray = array( "originCode", "destinationCode", "length", "width", "height", "weight" );
        
        // Initialize default values
        public $formErrors = array();
        public $isValid = TRUE;
        
        // Define form errors through class construct
        public function __construct()
        {
            $this->formErrors = array_merge($this->regionFields, $this->dimensionFields);
        }
       
        /**
         * Validate fieldvalue against field property
        */
        public function validateAndReturn($inputValues, $fieldName)
        {
            // Check fieldValue is empty or not.
            $errorMessage = "";
            $fieldValue = trim($inputValues[$fieldName]);

            if( empty($fieldValue) )
            {
                $errorMessage = $fieldName." Need values";
                $this -> isValid = FALSE;
            } // Check if the filed belongs to numeric field set.
            elseif ( in_array($fieldName, $this -> numericFieldsArray) )
            {
                // Check fieldValue is numeric or not.
                if( !is_numeric($fieldValue) )
                {
                    $errorMessage = $fieldName. "Need  Numbers only";
                    $this -> isValid = FALSE;
                }
            }
            $this -> formErrors[$fieldName] = $errorMessage;
            return $fieldValue;
        }
        /**
         * Set form fileds into respective properties 
         */
        public function validateAndSet($inputValues)
        {
            // Requesting validation against regionFields
            foreach($this -> regionFields as $key => $value){
                $this -> regionFields[$key] = $this->validateAndReturn($inputValues,$key); 
               
            }          
            // Requesting validation against dimensionFields
            foreach($this -> dimensionFields as $key => $value){
                $this -> dimensionFields[$key] = $this->validateAndReturn($inputValues,$key); 
                
            }
        }
    }
?>