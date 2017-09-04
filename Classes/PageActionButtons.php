<?php

 class PageActionButtons
    {   
        private $claimType;
        private $enableSubmitButton;
        
        public function __construct($claimType)
        {
            $this->claimType = $claimType;
            
            $this->setPageButtons();
        }
     
        // checks current claim type and enables or disables the submit button 
        // based on if the claim can be processed or not
        private function checkDisableSubmit()
        {
            switch ($this->claimType)
            {
                case "invaild-keycode":
                    
                    return "disabled";
                    break;
                    
                case "keycode-not-found":
                    
                    return "disabled";
                    break;
                    
                case "repair-warranty":
                    return "";
                    break;
                
                case "repair-no-warranty":
                    return "";
                    break;
                    
                case "repair-no-transaction":
                    return "";
                    break;
                    
                 case "financial-warranty":
                    return "";
                    break;
                    
                case "financial-outside-warranty":
                    return "disabled";
                    break;
                    
                case "financial-no-transaction":
                    return "disabled";
                    break;
                    
                case "not-claimable-refund":
                    return "disabled";
                    break;
                    
                case "not-claimable-outside-warranty":
                    return "disabled";
                    break;
                    
                case "not-claimable-no-transaction":
                    return "disabled";
                    break; 
                    
                case null:
                    return "disabled";
                    break; 
            }
        }
        
        private function setPageButtons()
        {
            if($this->claimType == 'repair-warranty' || $this->claimType == 'repair-no-warranty' ||
               $this->claimType == 'repair-no-transaction')
            {
                echo "
                        <div id=\"customerclaim-error-container\">
                            
                        </div>";
            }
            echo "  
                        <div class=\"input-wrapper-last\" style=\"margin-top:30px\">
                
                        <div class=\"left-wrapper\">
                            <button name=\"cancel-claim-button\" value=\"cancel-claim-button\" type=\"button\" class=\"submit-button\"  id='cancel-claim-button' style=\"float:left\" onclick='return loadMainMenu()'>Cancel</button>
                        </div>

                        <div class=\"right-wrapper\">
                            <button name=\"submit-claim-button\" value=\"submit-claim-button\" type=\"submit\" class=\"submit-button\" " .$this->checkDisableSubmit(). ">Submit Claim</button>
                            
                        </div>
                     
                    </div>
                    
                </form>
                ";
        }
    }
?>