<?php
    class RepairOptionsPanel
    {
        private $claimID;
        private $claimType = "";
        private $repairMessage;
        private $claimIDRegex = '/^[0-9]{1,20}$/';
        
        private $repairDetailsQuery = " SELECT RepairMessage
                                        FROM RepairClaim 
                                        WHERE ClaimID =";
        
        public function __construct()
        {
            // do nothing
        }
        
        
        private function checkClaimType($claimType)
        {
            if ($claimType == 'Warranty' || $claimType == 'Non-Warranty' || $claimType == 'repair-warranty' ||
               $claimType == 'repair-no-warranty' || $claimType == 'Non-Warranty' || $claimType == 'Non-Warranty'
               || $claimType == 'repair-no-transaction')
            {
                $this->claimType = $claimType;
            }
            else
            {
                die ("<h2> Error - Invaild ClaimType provided. Must be ethier: Warranty, Non-Warranty, repair-warranty, repair-no-warranty, Non-Warranty and Non-Warranty");
            }
        }
        
        
        public function loadClaim($claimID)
        {
            $this->claimType = "existing";
            
            // check to make sure vaild claim type is being entered - stop injection
            if (preg_match($this->claimIDRegex, $claimID) == true)
            {
                $this->repairDetailsQuery .= $claimID . ' ;';
                
                $result = mysql_query($this->repairDetailsQuery)
                    or die ("<h2> Error - Query invaild - check class RepairOptionsPanel.</h2>");
                
                $row = mysql_fetch_array($result);
                
                if (empty($row) == true)
                {
                    die("<h2> Error - Claim ID not found in the repair claim table.</h2>");
                }
                
                $this->repairMessage = $row['RepairMessage']; 
                
                $this->printHTML();
            }
            else
            {
                die("<h2> Error - Invaild Claim ID - Must be numbers only and maximum of 20 characters.</h2>");
            }
        }
        
        
        public function newClaim($claimType)
        {
            $this->checkClaimType($claimType);
            
            $this->printHTML();
        }
        
        
        private function printHTML()
        {
            echo "
                    <fieldset>
                        <legend>Repair Details</legend>";
                    
                    // if existing claim do not show type selection
                    if ($this->claimType != 'existing')
                    {
                        echo "
                        <div class=\"input-wrapper\">
                            <span>Repair Options:</span>
                            <input type=\"radio\" id=\"warranty-repair\" class=\"warranty-radio\" name=\"repairtype-radio\" value=\"Warranty\" " .$this->warrantyRadioCheck(). " >
                            <label>Under-Warranty</label>
                            <input type=\"radio\" id=\"no-warranty-repair\" class=\"warranty-radio\" name=\"repairtype-radio\" value=\"Non-Warranty\" " .$this->nonWarrantyRadioCheck(). ">
                            <Label>NOT Under-Warranty</Label>
                        </div> ";
                    }
                        echo "
                        <div class=\"input-wrapper-last\">
                            <label>Description of fault:</label>
                            <textarea id=\"fault-textarea\" name=\"repair-message-textarea\"> $this->repairMessage</textarea>
                        </div>
                    </fieldset>
                    ";
        }
        
        
        // checks the claimtype to determain if warranty repair is available, if not disables the warranty radio
        private function warrantyRadioCheck()
        {
            if ($this->claimType == "repair-no-warranty" || $this->claimType == "repair-no-transaction")
            {
                return "disabled = \"disabled\"";
            }
            else
            {
                return "checked = \"checked\"";
            }
        }
        
         private function nonWarrantyRadioCheck()
        {
            if ($this->claimType == "repair-no-warranty" || $this->claimType == "repair-no-transaction")
            {
                return "checked = \"checked\"";
            }
        }
    
    }
?>