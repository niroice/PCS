<?php

    class ClaimDetailsPanel
    {
        private $claimID;
        private $claimType;
        
        private $queryResult;
        
        private $returnClaimInformationQuery = "SELECT CreatedDate, ClaimStatus, ReturnAuthorizationNumber, TotalRefund
                                            FROM `ReturnClaim_ModifyClaimPage` 
                                            WHERE claimID =";
        
        private $repairClaimInformationQuery = "SELECT CreatedDate, Type, ClaimStatus, ReturnAuthorizationNumber,
                                            RepairCost
                                            FROM `RepairClaim_ModifyClaimPage` 
                                            WHERE claimID =";
        
        private $financialClaimInformationQuery = "SELECT CreatedDate, ClaimStatus, TotalRefund
                                            FROM `FinancialClaim_ModifyClaimPage` 
                                            WHERE claimID =";
        
        private $claimIDRegex = '/^[0-9]{1,20}$/';
        
        // claim status's used to generate status drop down
        private $financialStatuses = array('Created', 'Claim Raised', 'Complete', 'Cancelled');
        private $returnStatuses = array('Created', 'Shipped', 'Complete', 'Cancelled');
        private $repairStatuses = array('Created', 'RA Requested', 'Shipped', 'Complete', 'Cancelled');
        
        
        public function __construct()
        {          
            // do nothing
        }
        
        // retrieves the required claims from the database - also checks claim type is vaild
        private function getClaimsProductsSQL($claimID, $claimType)
        {   
            if ($claimType == 'Warranty' || $claimType == 'Non-Warranty')
            {
                // retrieve all the products in the claim
                $this->queryResult = mysql_query($this->repairClaimInformationQuery . $claimID . ';' );
                
                // check the claims results was found - if less than one show error
                if (mysql_num_rows($this->queryResult) < 1)
                {
                    die("<h2> Error - ClaimID '$claimID' not found in database for a '$claimType'.</h2>");
                }
            }
            else if ($claimType == 'Return')
            {
                // retrieve all the products in the claim
                $this->queryResult = mysql_query($this->returnClaimInformationQuery . $claimID . ';' );
                
                // check the claims results was found - if less than one show error
                if (mysql_num_rows($this->queryResult) < 1)
                {
                    die("<h2> Error - ClaimID '$claimID' not found in database for a '$claimType'.</h2>");
                }
            }
            else if ($claimType == 'Financial')
            {
                // retrieve all the products in the claim
                $this->queryResult = mysql_query($this->financialClaimInformationQuery . $claimID . ';' );
                
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
            $this->getClaimsProductsSQL($claimID, $claimType);
            
            
            // print the html to the screen
            $this->printHTML();
        }
        
        // creates new blank financial claim fields 
        public function startNewFinancialClaim()
        {
            // set claim type as new, to avoid accessing query
            $this->claimType = "new-financial";
            
            // print the html to the screen
            $this->printHTML();

        }
        
         // creates new blank return claim fields 
        public function startNewReturnClaim()
        {
            // set claim type as new, to avoid accessing query
            $this->claimType = "new-return";
            
            // print the html to the screen
            $this->printHTML();

        }
        
        // generates the html for claim status dropdown, based on the claim type
        // returns: string value and Requires a string value claim type
        private function generateClaimStatus($claimStatus)
        {
            $selectHTML = "";
            
            if ($this->claimType == "Financial")
            {
                $selectHTML = "<option value=\"$claimStatus\">$claimStatus</option>";
                
                foreach($this->financialStatuses as $row)
                {
                    // if current status matches the row status dont add it
                    if($claimStatus != $row )
                    {
                        $selectHTML .= "<option value=\"$row\">$row</option>";
                    }
                }
            }
            else if ($this->claimType == "Return")
            {
                $selectHTML = "<option value=\"$claimStatus\">$claimStatus</option>";
                
                foreach($this->returnStatuses as $row)
                {
                    // if current status matches the row status dont add it
                    if($claimStatus != $row )
                    {
                        $selectHTML .= "<option value=\"$row\">$row</option>";
                    }
                }
            }
            else if ($this->claimType == "Warranty" || $this->claimType == "Non-Warranty")
            {
                $selectHTML = "<option value=\"$claimStatus\">$claimStatus</option>";
                
                foreach($this->repairStatuses as $row)
                {
                    // if current status matches the row status dont add it
                    if($claimStatus != $row )
                    {
                        $selectHTML .= "<option value=\"$row\">$row</option>";
                    }
                }
            }
            else if ($this->claimType == "new-financial")
            { 
                foreach($this->financialStatuses as $row)
                {
                        $selectHTML .= "<option value=\"$row\">$row</option>";
                }
            }
            else if ($this->claimType == "new-return")
            { 
                foreach($this->returnStatuses as $row)
                {
                        $selectHTML .= "<option value=\"$row\">$row</option>";
                }
            }
            else
            {
                die("<h2> Error - Invaild Claim Type provided for class ClaimDetailsPanel - generateClaimStatus() - Must be ethier 'Warranty', 'Non-Warranty', 'Return' or 'Financial'.</h2>");
            }
            
            return $selectHTML;
        }
            
        
        
        // prints the selected claims to the screen
        private function printHTML()
        {   
            
            // set the default claim amount in session variable to zero dollars, each
            // check will change the value if needed
            $_SESSION['ClaimAmount'] = 0;
            
            // To ensure the correct repair type is shown in the claimtype dropdown, the 
            // repair type from database is used instead of the brought in type. This fixs
            // any potiential issue with the wrong repair type being used. Other types
            // do not suffer this issue due to different views being used.
            if ($this->claimType == 'Warranty' || $this->claimType == 'Non-Warranty')
            {
                $row = mysql_fetch_array($this->queryResult);
                $claimStatus =  $row['ClaimStatus'];
                $date = date('d/m/y', strtotime($row['CreatedDate']));
                $repairCost = $row['RepairCost'];
                $raNumber = $row['ReturnAuthorizationNumber'];
                $this->claimType = $row['Type'];
                
                // set session claimamount/repair cost
                $_SESSION['ClaimAmount'] = (float)$repairCost;
                
            }
            
            if($this->claimType == "Return" || $this->claimType == "new-return")
            {
                $row = "";
                $claimStatus = "";
                $raNumber = "";
                $date = date("d/m/Y");
                $claimAmount = 0;
                
                // if claim type is return/existing claim retrieve the information
                // from the query, otherwise fields will be blank for a new claim
                if($this->claimType == "Return")
                {
                    $row = mysql_fetch_array($this->queryResult);
                    $claimStatus =  $row['ClaimStatus'];
                    $raNumber= $row['ReturnAuthorizationNumber'];
                    $date = date('d/m/y', strtotime($row['CreatedDate']));
                    $claimAmount = $row['TotalRefund'];
                    
                    // set the session variables total refund/claim amount
                    $_SESSION['ClaimAmount'] = (float)$claimAmount;
                }

                echo "
                <fieldset>
                    <legend>Claim Details</legend>
                    <div class=\"input-wrapper\">

                        <div class=\"left-wrapper\">
                            <label>Claim Number: </label>
                            <input type=\"text\" class=\"input-text-fifty\" name=\"claimdetails-claimid-input\" 
                                   id=\"claimdetails-claimid-input\" value=\"$this->claimID\" disabled>
                        </div>

                        <div class=\"right-wrapper\">
                            <label>Claim Type:</label>
                            <select name=\"claimdetails-claimtype-select\" id=\"claimdetails-claimtype-select\"  ";
                            
                // if existing claim disable dropdown from being changed
                 if($this->claimType == "Return")  
                 {
                     echo "disabled";
                 }
                            echo ">
                                <option value=\"Return\">Return</option>
                                <option value=\"Financial\">Financial</option>
                            </select>
                        </div>
                    </div>

                    <div class=\"input-wrapper\">
                        <div class=\"left-wrapper\">
                            <label>Created Date: </label>
                            <input type=\"text\" class=\"input-text-fifty\" name=\"claimdetails-date-input\" id=\"claimdetails-date-input\" value=\"$date\" disabled>
                        </div>
                        <div class=\"right-wrapper\">
                            <label>Claim Status:</label>
                            <select name=\"claimdetails-claimstatus-select\" id=\"claimdetails-claimstatus-select\">
                                ".$this->generateClaimStatus($claimStatus)."
                            </select>

                        </div>
                    </div>
                    <div class=\"input-wrapper\">
                        <div class=\"left-wrapper\">
                            <label>Claim Amount: </label>
                            <input type=\"text\" class=\"input-text-fifty\" name=\"claimdetails-claimamount-input\" id=\"claimdetails-claimamount-input\" value=\"$claimAmount\">
                        </div>
                    <div class=\"right-wrapper\">
                            <label>RA Number: </label>
                            <input type=\"text\" class=\"input-text-fifty\" name=\"claimdetails-ranumber-input\" id=\"claimdetails-ranumber-input\" value=\"$raNumber\">
                        </div>
                    </div>
                </fieldset>
                    ";
            }
            else if ($this->claimType == "Financial" || $this->claimType == "new-financial")
            {
                $row = "";
                $claimStatus = "";
                $date = date("d/m/Y");
                $claimAmount = 0;
                
                if ($this->claimType == 'Financial')
                {
                    $row = mysql_fetch_array($this->queryResult);
                    $claimStatus =  $row['ClaimStatus'];
                    $date = date('d/m/y', strtotime($row['CreatedDate']));
                    $claimAmount = $row['TotalRefund'];
                    
                    // if claim type is existing financial set claim amount
                    // otherwise leave at zero dollars if new financial claim
                    $_SESSION['ClaimAmount'] = (float)$claimAmount;
                }
                
                echo "
                <fieldset>
                    <legend>Claim Details</legend>
                    <div class=\"input-wrapper\">

                        <div class=\"left-wrapper\">
                            <label>Claim Number: </label>
                            <input type=\"text\" class=\"input-text-fifty\" name=\"claimdetails-claimid-input\" 
                                   id=\"claimdetails-claimid-input\" value=\"$this->claimID\" disabled>
                        </div>

                        <div class=\"right-wrapper\">
                            <label>Claim Type:</label>
                            <select name=\"claimdetails-claimtype-select\" id=\"claimdetails-claimtype-select\" ";
                            
                // if existing claim disable dropdown from being changed
                 if($this->claimType == "Financial")  
                 {
                     echo "disabled";
                 }
                            echo ">
                                <option value=\"Financial\">Financial</option>
                                <option value=\"Return\">Return</option>
                            </select>
                        </div>
                    </div>

                    <div class=\"input-wrapper\">
                        <div class=\"left-wrapper\">
                            <label>Created Date: </label>
                            <input type=\"text\" class=\"input-text-fifty\" name=\"claimdetails-date-input\" id=\"claimdetails-date-input\" value=\"$date\" disabled>
                        </div>
                        <div class=\"right-wrapper\">
                            <label>Claim Status:</label>
                            <select name=\"claimdetails-claimstatus-select\" id=\"claimdetails-claimstatus-select\">
                                ".$this->generateClaimStatus($claimStatus)."
                            </select>

                        </div>
                    </div>
                    <div class=\"input-wrapper\">
                        <div class=\"left-wrapper\">
                            <label>Claim Amount: </label>
                            <input type=\"text\" class=\"input-text-fifty\" name=\"claimdetails-claimamount-input\" id=\"claimdetails-claimamount-input\" value=\"$claimAmount\">
                        </div>
                    </div>
                </fieldset>
                    ";
            }
            else if ($this->claimType == "Warranty")
            {
                echo "
                <fieldset>
                    <legend>Claim Details</legend>
                    <div class=\"input-wrapper\">

                        <div class=\"left-wrapper\">
                            <label>Claim Number: </label>
                            <input type=\"text\" class=\"input-text-fifty\" name=\"claimdetails-claimid-input\" 
                                   id=\"claimdetails-claimid-input\" value=\"$this->claimID\" disabled>
                        </div>

                        <div class=\"right-wrapper\">
                            <label>Claim Type:</label>
                            <select name=\"claimdetails-claimtype-select\" id=\"claimdetails-claimtype-select\">
                                <option value=\"Warranty\">Warranty Repair</option>
                                <option value=\"Non-Warranty\">Non-Warranty Repair</option>
                            </select>
                        </div>
                    </div>

                    <div class=\"input-wrapper\">
                        <div class=\"left-wrapper\">
                            <label>Created Date: </label>
                            <input type=\"text\" class=\"input-text-fifty\" name=\"claimdetails-date-input\" id=\"claimdetails-date-input\" value=\"$date\" disabled>
                        </div>
                        <div class=\"right-wrapper\">
                            <label>Claim Status:</label>
                            <select name=\"claimdetails-claimstatus-select\" id=\"claimdetails-claimstatus-select\">
                                ".$this->generateClaimStatus($claimStatus)."
                            </select>

                        </div>
                    </div>
                    <div class=\"input-wrapper\" id=\"claimdetails-ranumber-div\">
                        <div class=\"left-wrapper\">
                            <label>RA Number: </label>
                            <input type=\"text\" class=\"input-text-fifty\" name=\"claimdetails-ranumber-input\" id=\"claimdetails-ranumber-input\" value=\"$raNumber\">
                        </div>
                    </div>
                </fieldset>
                    ";
            }
            else if ($this->claimType == "Non-Warranty")
            {
                echo "
                <fieldset>
                    <legend>Claim Details</legend>
                    <div class=\"input-wrapper\">

                        <div class=\"left-wrapper\">
                            <label>Claim Number: </label>
                            <input type=\"text\" class=\"input-text-fifty\" name=\"claimdetails-claimid-input\" 
                                   id=\"claimdetails-claimid-input\" value=\"$this->claimID\" disabled>
                        </div>

                        <div class=\"right-wrapper\">
                            <label>Claim Type:</label>
                            <select name=\"claimdetails-claimtype-select\" id=\"claimdetails-claimtype-select\" disabled>       <option value=\"Non-Warranty\">Non-Warranty Repair</option>
                                <option value=\"Warranty\">Warranty Repair</option>
                            </select>
                        </div>
                    </div>

                    <div class=\"input-wrapper\">
                        <div class=\"left-wrapper\">
                            <label>Created Date: </label>
                            <input type=\"text\" class=\"input-text-fifty\" name=\"claimdetails-date-input\" id=\"claimdetails-date-input\" value=\"$date\" disabled>
                        </div>
                        <div class=\"right-wrapper\">
                            <label>Claim Status:</label>
                            <select name=\"claimdetails-claimstatus-select\" id=\"claimdetails-claimstatus-select\">
                                ".$this->generateClaimStatus($claimStatus)."
                            </select>
                        </div>
                    </div>
                </fieldset>
                    ";
            }

        }
    }
?>