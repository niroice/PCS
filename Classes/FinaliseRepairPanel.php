<?php
    
    class FinaliseRepairPanel 
    {
        private $queryResult;
        private $claimID;
        private $claimType;
        
        public function __construct()
        {
            // performs no action on creation
            
        }
        
        // sends query to database to retrieve the informaiton required to fill the 
        // finalise repair panel
        public function setRepairDetailsSQL($claimID)
        {
            $query = mysql_query("  SELECT Type, ClaimStatus, ReturnAuthorizationNumber, RepairCost,    
                                    ReturnAuthorizedName
                                    FROM RepairClaim
                                    WHERE ClaimID = $claimID");
            
            $this->queryResult = mysql_fetch_array($query);
            
            if(empty($this->queryResult))
            {
                echo "<h2> ERROR - Finalise Repair Option Panel - could not find the provided claimID in the database.</h2>";
            }
        }
        
        // allows claimtype to be set - used if claim doesnt exist in database
        public function setClaimType($claimType)
        {
            if ($claimType == "repair-warranty" || $claimType == "repair-no-warranty" ||
               $claimType == "repair-no-transaction")
            {
                $this->claimType = $claimType;
            }
            else
            {
                die("<h2> Error - Invaild Claim Type provided for setClaimType() method. Must be ethier repair-warranty, repair-no-warranty or repair-no-transaction.</h2>");
            }
        }
        
        // checks if claim is new or existing, if claim is new the print checkbox will be checked.
        // If it is existing claim it will be unchecked by default
        private function checkPrintCondition()
        {
            // is queryResult is empty this means it is a new claim
            if(empty($this->queryResult))
            {
                return "checked";
            }
        }
        
        
        private function checkRACondition()
        {
            // if repair type is non-warranty type of repair or claim status is not created. Disable 
            // RA send option, as its not required
            if ($this->queryResult["Type"] == "Non-Warranty" || $this->claimType == "repair-no-warranty" ||
                $this->claimType == "repair-no-transaction" || $this->queryResult["ClaimStatus"] == "RA Requested" || 
                $this->queryResult["ClaimStatus"] == "Shipped" || $this->queryResult["ClaimStatus"] == "Complete" || $this->queryResult["ClaimStatus"] == "Cancelled")
            {
                return "disabled=\"disabled\"";
            }
            // otherwise set to checked
            else
            {
                return "checked";
            }
        }
        
         private function checkRepairCost()
        {
            //
            if ($this->queryResult["RepairCost"] == null)
            {
                return "";
            }
             else
             {
                 return "".$this->queryResult["RepairCost"]."";
             }
        }
        
        // creates the finalise repair fields
        public function createFinaliseRepairFields()
        {
            echo "
            <fieldset>
                <legend> Finalise Repair </legend>
                <div class=\"input-wrapper\">
                    <div class=\"left-wrapper\">
                        <label>Repair Cost: </label>
                        <input type=\"text\" class=\"input-text-fifty\" name=\"repair-cost-input\" id=\"repair-cost-input\"
                        value=\"".$this->checkRepairCost()."\">
                    </div>

                    <div class=\"right-wrapper\">
                        <label>Authorised By: </label>
                        <input type=\"text\" class=\"input-text-fifty\" name=\"return-authorized-name-input\" id=\"return-authorized-name-input\"
                        value=\"".$this->queryResult["ReturnAuthorizedName"]."\">
                    </div>
                </div>

                <div class=\"input-wrapper\">
                    <div class=\"left-wrapper\" style=\"width:330px\">
                        <input type=\"checkbox\" name=\"print-checkbox\"  value=\"print-checkbox\"
                        ".$this->checkPrintCondition().">
                        <label>Print Repair Order Form/s </label>

                    </div>
                </div>

                <div class=\"input-wrapper\">
                    <div class=\"left-wrapper\" style=\"width:100%\">
                        <input type=\"checkbox\" name=\"ra-checkbox\" value=\"ra-checkbox\" id=\"ra-checkbox\" ".$this->checkRACondition().">
                        <label> Send request for \"Return Authorisation Number\" now (recommended) </label>
                    </div>
                </div>
            </fieldset> ";
        }
        
    }
?>
        