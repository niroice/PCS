<?php

    class SupplierDetailsPanel
    {
        private $claimID;
        private $claimType;
        
        private $queryResult;
        
        private $returnSupplierInformationQuery = "SELECT SupplierID, SupplierName, SupplierPhone, SupplierEmail,                                             SupplierAddress
                                            FROM `ReturnClaim_ModifyClaimPage` 
                                            WHERE claimID =";
        
        private $repairSupplierInformationQuery = "SELECT SupplierID, SupplierName, SupplierPhone, SupplierAddress, 
                                            SupplierEmail
                                            FROM `RepairClaim_ModifyClaimPage` 
                                            WHERE claimID =";
        
        private $financialSupplierInformationQuery = "SELECT SupplierID, SupplierName, SupplierPhone, SupplierEmail,                                         SupplierAddress
                                            FROM `FinancialClaim_ModifyClaimPage` 
                                            WHERE claimID =";
        
        private $claimIDRegex = '/^[0-9]{1,20}$/';
        
        
        public function __construct()
        {          
            // do nothing
        }
        
        // retrieves the required claims from the database - also checks claim type is vaild
        private function getSupplierDetailsSQL($claimID, $claimType)
        {   
            if ($claimType == 'Warranty' || $claimType == 'Non-Warranty')
            {
                // retrieve all the products in the claim
                $this->queryResult = mysql_query($this->repairSupplierInformationQuery . $claimID . ';' );
                
                // check the claims results was found - if less than one show error
                if (mysql_num_rows($this->queryResult) < 1)
                {
                    die("<h2> Error - ClaimID '$claimID' not found in database for a '$claimType'.</h2>");
                }
            }
            else if ($claimType == 'Return')
            {
                // retrieve all the products in the claim
                $this->queryResult = mysql_query($this->returnSupplierInformationQuery . $claimID . ';' );
                
                // check the claims results was found - if less than one show error
                if (mysql_num_rows($this->queryResult) < 1)
                {
                    die("<h2> Error - ClaimID '$claimID' not found in database for a '$claimType'.</h2>");
                }
            }
            else if ($claimType == 'Financial')
            {
                // retrieve all the products in the claim
                $this->queryResult = mysql_query($this->financialSupplierInformationQuery . $claimID . ';' );
                
                // check the claims results was found - if less than one show error
                if (mysql_num_rows($this->queryResult) < 1)
                {
                    die("<h2> Error - ClaimID '$claimID' not found in database for a '$claimType'.</h2>");
                }
            }
            else
            {
                die("<h2> Error - Invaild ClaimType '$claimType' provided. Must be 'Warranty' or 'Non-Warranty' or 'Return' or 'Financial'.</h2>");
            }
            
        }
        
        // Description: Retrieves supplier details based on a product keycode and
        //              returns the information as a JSON object
        // Input:       Keycode
        // Output:      JSON object
        public function getSupplierDetailsJSON($keycode)
        {
            $queryError = array("response" => "query-error");
            
            $result = mysql_query(" SELECT Supplier.SupplierID, SupplierName, SupplierPhone, SupplierEmail,         SupplierAddress
                                    FROM   Product, Supplier
                                    WHERE  Product.Keycode = $keycode
                                    AND    Product.SupplierID = Supplier.SupplierID;");
                   //                 or
            //echo json_encode($queryError);
            
            
            // check to make sure results where found  if not send error back with json
            if (!$result)
            {
                $array = array("response" => "not-found");
                echo json_encode($array);
            }
            else // if found send result back as json object
            {
                $row = mysql_fetch_array($result);
                
                $array = array("response" => "found", "supplierid" => $row['SupplierID'], "name" => $row['SupplierName'], "phone" => $row['SupplierPhone'], "email" => $row['SupplierEmail'], "address" => $row['SupplierAddress']);
                
                echo json_encode($array);
            }
        }
        
        // generates claim html based on claimID and type
        public function loadClaim($claimID, $claimType)
        {
            // check to make sure vaild claim type is being entered - stop injection
            if (preg_match($this->claimIDRegex, $claimID) == false)
            {
                die("<h2> Error - Invaild Claim ID provided for class ClaimProductPanel - loadClaim() - Must only contain numbers and maxium 20 characters in length.</h2>");
            }
            
            // set claimID and type
            $this->claimID = $claimID;
            $this->claimType = $claimType;
            
            // get all the claims information from database - saves to global variable
            $this->getSupplierDetailsSQL($claimID, $claimType);
            
            // print the html to the screen
            $this->printHTML();
        }
        
        // creates new blank financial claim fields 
        public function startNewClaim()
        {
            // set claim type as new, to avoid accessing query
            $this->claimType = "new";
            
            // print the html to the screen
            $this->printHTML();

        }
        
        // prints the selected claims to the screen
        private function printHTML()
        {
            $supplierID = "";
            $name = "";
            $email = "";
            $phone = "";
            $address = "";
            
            // if claimtype not new, retrieve the information from query
            // else the inputs will print blank
            if ($this->claimType != "new")
            {
                $supplier = mysql_fetch_array($this->queryResult); // retrieve the result from query
                $supplierID = $supplier['SupplierID'];
                $name = $supplier['SupplierName'];
                $email = $supplier['SupplierEmail'];
                $phone = $supplier['SupplierPhone'];
                $address = $supplier['SupplierAddress'];
            }
                echo "
            <fieldset>
                <legend>Supplier Details</legend>
                <div class=\"input-wrapper\">
                    <div class=\"left-wrapper\">
                        <label>Supplier ID: </label>
                        <input type=\"text\" class=\"input-text-fifty\" name=\"claimdetails-supplierid-input\" 
                        id=\"claimdetails-supplierid-input\" value=\"$supplierID\" disabled>
                    </div>

                    <div class=\"right-wrapper\">
                        <label>Supplier Name: </label>
                        <input type=\"text\" class=\"input-text-fifty\" name=\"claimdetails-suppliername-input\" 
                        id=\"claimdetails-suppliername-input\" value=\"$name\" disabled>
                    </div>
                </div>

                <div class=\"input-wrapper\">
                    <div class=\"left-wrapper\">
                        <label>Email: </label>
                        <input type=\"text\" class=\"input-text-fifty\" name=\"claimdetails-supplieremail-input\"
                        id=\"claimdetails-supplieremail-input\" value=\"$email\" disabled>
                    </div>
                    <div class=\"right-wrapper\">
                        <label>Phone: </label>
                        <input type=\"text\" class=\"input-text-fifty\" name=\"claimdetails-supplierphone-input\"
                        id=\"claimdetails-supplierphone-input\" value=\"$phone\" disabled>
                    </div>
                </div>

                <div class=\"input-wrapper\">
                    <label>Address: </label>
                    <input type=\"text\" class=\"input-text-full\" name=\"claimdetails-supplieraddress-input\"
                    id=\"claimdetails-supplieraddress-input\" value=\"$address\" disabled>
                </div>

            </fieldset>
                            ";
        }
    }
?>