<?php
    class CustomerDetailsPanel
    {
        private $claimID;
        private $claimType;
        private $queryResult;
        private $claimIDRegex = '/^[0-9]{1,20}$/';
        
        public function __construct()
        {
            // no action taken on creation
        }
        
        // retrieves all the customer details from database and stores it in $queryResult for use later
        private function getCustomerDetailsSQL($claimID)
        {
            $query = mysql_query("  SELECT FirstName, LastName, CustomerPhone, CustomerEmail, HouseNumber, Street,                           SuburbCity, State, Postcode 
                                FROM RepairClaim_ModifyClaimPage
                                WHERE ClaimID = $claimID")
                or die ("<h2> Error - Invaild Query - check class CustomerDetailsPanel - getCustomerDetailsSQL().");

            $this->queryResult = mysql_fetch_array($query);
        }
        
        
        // creates blank customer fields
        public function newCustomer()
        {
            $this->claimType = "new";
            $this->createCustomerFields();
        }
        
        // creates customer form inputs and loads the customers details
        // based on claimID
        public function loadExistingCustomer($claimID)
        {   
            // check to make sure vaild claim ID is being entered - stop injection
            if (preg_match($this->claimIDRegex, $claimID) == true)
            {
                $this->claimID = $claimID;
            }
            else
            {
                die("Error - Invaild ClaimID - Must contain only numbers and be a maxmium of 20 characters in length.");
            }
            
            $this->getCustomerDetailsSQL($this->claimID);
            $this->createCustomerFields();
        }
        
        // creates the html fields for customer details if the claim type is new or details exist in the query
        // if query result contains customer details they will be assigned to the inputs otherwise
        // they will be blank
        public function createCustomerFields()
        {
            // checks before printing the html wether claim is new or the query contains information
            // if not the customer fields will not show.
            if($this->queryResult["FirstName"] != null || $this->claimType == 'new')
            {
                echo "
                        <fieldset>
                            <legend> Customer Details </legend>
                            <div class=\"input-wrapper\">
                                <div class=\"left-wrapper\">
                                    <label>First Name: </label>
                                    <input type=\"text\" class=\"input-text-fifty\" name=\"customer-firstname-input\" id=\"customer-firstname-input\" value=\"".$this->queryResult["FirstName"]."\">
                                </div>

                                <div class=\"right-wrapper\">
                                    <label>Last Name: </label>
                                    <input type=\"text\" class=\"input-text-fifty\" name=\"customer-lastname-input\" id=\"customer-lastname-input\" value=\"".$this->queryResult["LastName"]."\">
                                </div>
                            </div>

                            <div class=\"input-wrapper\">
                                <div class=\"left-wrapper\">
                                    <label>Phone: </label>
                                    <input type=\"text\" class=\"input-text-fifty\" name=\"customer-phone-input\" id=\"customer-phone-input\" value=\"".$this->queryResult["CustomerPhone"]."\">
                                </div>

                                <div class=\"right-wrapper\">
                                    <label>Email: </label>
                                    <input type=\"text\" class=\"input-text-fifty\" name=\"customer-email-input\" id=\"customer-email-input\" value=\"".$this->queryResult["CustomerEmail"]."\">
                                </div>
                            </div>

                            <div class=\"input-wrapper\">
                                <div class=\"left-wrapper\">
                                    <label>Unit/House Number: </label>
                                    <input type=\"text\" class=\"input-text-fifty\" name=\"customer-unithouse-input\" id=\"customer-unithouse-input\" value=\"". $this->queryResult["HouseNumber"]."\">
                                </div>

                                <div class=\"right-wrapper\">
                                    <label>Street: </label>
                                    <input type=\"text\" class=\"input-text-fifty\" name=\"customer-street-input\" id=\"customer-street-input\" value=\"".$this->queryResult["Street"]."\">
                                </div>
                            </div>

                            <div class=\"input-wrapper\">
                                <div class=\"left-wrapper\">
                                    <label>City/Suburb: </label>
                                    <input type=\"text\" class=\"input-text-fifty\" name=\"customer-suburbcity-input\" id=\"customer-suburbcity-input\" value=\"".$this->queryResult["SuburbCity"]."\">
                                </div>

                                <div class=\"right-wrapper\">
                                    <label>State: </label>
                                    <input type=\"text\" class=\"input-text-fifty\" name=\"customer-state-input\" id=\"customer-state-input\" value=\"". $this->queryResult["State"]."\">
                                </div>
                            </div>

                            <div class=\"input-wrapper\">
                                <div class=\"left-wrapper\">
                                    <label>Postcode: </label>
                                    <input type=\"text\" class=\"input-text-fifty\" name=\"customer-postcode-input\" id=\"customer-postcode-input\" value=\"".$this->queryResult["Postcode"]."\">
                                </div>
                            </div>
                    </fieldset>
                    ";
            }
        }
        
    
    }
?>