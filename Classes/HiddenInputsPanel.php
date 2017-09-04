<?php
    class HiddenInputsPanel
    {
        private $claimType;
        private $costPrice;
        
        public function __construct($claimType, $costPrice)
        {
            $this->claimType = $claimType;
            $this->costPrice = $costPrice;
            $this->createHiddentFields();
            
        }
        
        private function createHiddentFields()
        {
             echo "  <input type=\"text\" name=\"keycode-input-hidden\" value=\" ".$_POST['keycode-input']." \" style=\"display:none\">
             
                    <input type=\"text\" name=\"transactionid-input-hidden\" value=\" ".$_POST['transactionid-input']." \" style=\"display:none\">
                    
                    <input type=\"text\" name=\"claimtype-input-hidden\" value=\"$this->claimType\" style=\"display:none\">
                    
                    <input type=\"text\" style=\"display:none\" name=\"customerclaim-productcostprice-input\" value=\"$this->costPrice\">
                ";
        }
        
    
    }
?>