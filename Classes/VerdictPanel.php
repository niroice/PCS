<?php
    class VerdictPanel
    {
        private   $REPAIR_WARRANTY_MSG = "- is still under warranty and is required to be sent away for repair.";
        
        private   $REPAIR_NOWARRANTY_MSG = "- is no longer under warranty, but can be sent away for repair for a fee; based on an evaluation.";
        
        private   $REPAIR_NOTRANSACTION_MSG = "- can be repaired, but a vaild transaction number is required to complete the claim under warranty. Otherwise a fee will apply.";
        
        private   $FINANCIAL_WARRANTY_MSG = "- is still under warranty but cannot be repaired. Replace or Refund the item. Make sure you complete the claim before replacing the item.";
        
        private   $FINANCIAL_NOTRANSACTION_MSG = "- can be replaced or refunded, when a vaild transaction number is provided.";
        
        private   $FINANCIAL_NOWARRANTY_MSG = "- can NOT be replaced or refunded as it is outside of the warranty period.";
        
        private   $KEYCODE_NOT_FOUND_MSG = "Product could not be found. Please provide a vaild keycode and try Again.";
        
        private   $NOTCLAIMABLE_WARRANT_MSG = "- is still within the 12 month warranty period. However the supplier does not offer any warranty, so it cannot be claimed. Replace or refund the item.";
        
        private   $NOTCLAIMABLE_NOWARRANT_MSG = "- is no longer within the warranty period and cannot be claimed.";
        
        private   $NOTCLAIMABLE_NOTRANSACTION_MSG = "- can be refunded or replaced when a vaild transaction number is provided.";
        
        private   $INVAILD_KEYCODE_MSG = "ERROR - Invaild keycode provided. Keycode must contain numbers only.";
        
        private   $verdictRed = "no-verdict-container";
        private   $verdictGreen = "yes-verdict-container";
        private   $verdictOrange = "orange-verdict-container";
        private   $verdictAppearance;
        
        private $productDescription;
        private $claimType;
        private $claimMessage;
        
        public function __construct($productDescription, $claimType)
        {
            $this->productDescription = (string) $productDescription;
            $this->claimType = $claimType;
            $this->claimMessage = $this->determainMessage();
            $this->printMessage();    
        }
        
        private function printMessage()
        {
            echo "
                    <div id=\"$this->verdictAppearance\">
                        <h2>$this->productDescription $this->claimMessage </h2>
                    </div>
                ";
        }
        
        private function determainMessage()
        {
            switch ($this->claimType)
            {
                case "keycode-not-found":
                    $this->verdictAppearance = $this->verdictRed;
                    return $this->KEYCODE_NOT_FOUND_MSG;
                    break;
                    
                case "repair-warranty":
                    $this->verdictAppearance = $this->verdictGreen;
                    return $this->REPAIR_WARRANTY_MSG ;
                    break;
                
                case "repair-no-warranty":
                    $this->verdictAppearance = $this->verdictOrange;
                    return $this->REPAIR_NOWARRANTY_MSG ;
                    break;
                    
                case "repair-no-transaction":
                    $this->verdictAppearance = $this->verdictOrange;
                    return $this->REPAIR_NOTRANSACTION_MSG ;
                    break;
                    
                 case "financial-warranty":
                    $this->verdictAppearance = $this->verdictGreen;
                    return $this->FINANCIAL_WARRANTY_MSG ;
                    break;
                    
                case "financial-outside-warranty":
                    $this->verdictAppearance = $this->verdictRed;
                    return $this->FINANCIAL_NOWARRANTY_MSG ;
                    break;
                    
                case "financial-no-transaction":
                    $this->verdictAppearance = $this->verdictRed;
                    return $this->FINANCIAL_NOTRANSACTION_MSG ;
                    break;
                    
                case "not-claimable-refund":
                    $this->verdictAppearance = $this->verdictGreen;
                    return $this->NOTCLAIMABLE_WARRANT_MSG ;
                    break;
                    
                case "not-claimable-outside-warranty":
                    $this->verdictAppearance = $this->verdictRed;
                    return $this->NOTCLAIMABLE_NOWARRANT_MSG;
                    break;
                    
                case "not-claimable-no-transaction":
                    $this->verdictAppearance = $this->verdictRed;
                    return $this->NOTCLAIMABLE_NOTRANSACTION_MSG;
                    break;    
                    
                case "invaild-keycode":
                    $this->verdictAppearance = $this->verdictRed;
                    return $this->INVAILD_KEYCODE_MSG;
                    break;   
            }
        }
        
    
    }
?>