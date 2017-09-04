<?php
    include("VerdictPanel.php");
    include("InformationPanel.php");
    include("RepairOptionsPanel.php");
    include("CustomerDetailsPanel.php");
    include("FinaliseRepairPanel.php");
    include("PageActionButtons.php");
    include("HiddenInputsPanel.php");
    
    class SearchPanel 
    {
        private $keycode;
        private $transactionID;
        private $keycodeResult;
        private $transactionResult;
        private $claimType;
        private $submitButtonRequired;
        
        private $keycodeRegex = '/^[0-9]{1,9}$/';
        
        public function __construct($keycode, $transactionID)
        {  
            // assigning the varaiables and formating them by removing any spaces
            $this->keycode = $this->formatNumber($keycode);
            $this->transactionID = $this->formatNumber($transactionID);
            
            // if keycode string is NOT blank - search for vaild transaction and product
            if($this->keycode != null)
            {
                // get transaction information from database and store
                $this->searchTransactionID();

                // determin the claim type based on keycode and transactionID
                $this->determainclaimType();
            }

            // always needs to create the search panel html
            $this->createSearchFields();
            
            // create form to process a the claim or cancel
            echo "<form action=\"CustomerClaim.php\" method=\"post\" name=\"submit-repair-claim\">";

            // if keycode was found and not blank - try and retrieve product information from database
            if ($this->claimType != "keycode-not-found" && $this->keycode != null && 
                $this->keycode != "invaild-keycode" )
            {
                $this->getProductInformation();
            }

            // create the verdict panel to display the error
            $newVerdict =  new VerdictPanel($this->keycodeResult["ProductName"], $this->claimType);
            
            // create the relevant panels based on the claimType
            $this->showPanels();
            
            // create the the submit button and cancel button - based on claim type
            $newPageActionButtons = new PageActionButtons($this->claimType); 
        }
        
        private function showPanels()
        {
            // if claim type is a repair - create repair options, customer and finalise repair panels
            if ($this->claimType == "repair-warranty" || $this->claimType == "repair-no-warranty" ||
            $this->claimType == "repair-no-transaction")
            {
                // show the information panel
                $newInformationPanel = new InformationPanel($this->keycodeResult, $this->transactionResult);

                // create form form to process a repair claim
                //echo "<form action=\"CustomerClaim.php\" method=\"post\" name=\"submit-repair-claim\">";

                // create form for repair panel
                $newRepairOptionsPanel = new RepairOptionsPanel($this->claimType);

                // create customer details panel object and create the blank fields
                $newCustomerDetailsPanel = new CustomerDetailsPanel();
                $newCustomerDetailsPanel->createCustomerFields();

                // create repair options panel object and create the blank html fields
                $newfinaliseRepairPanel =  new FinaliseRepairPanel();
                $newfinaliseRepairPanel->createFinaliseRepairFields();
                
                // object has hidden inputs for keycode and transaction ID so they can be retrieved from 
                // second form on the page, as the first form contains the first inputs for them
                $newHiddenInputsPanel = new HiddenInputsPanel($this->claimType);

            }
            
            // finanical claim show information panel and set form to finanical claim
            else if($this->claimType == "finanical-warranty" || $this->claimType == "finanical-outside-warranty" || 
               $this->claimType == "finanical-no-transaction")
            {
                // show the information panel
                $newInformationPanel = new InformationPanel($this->keycodeResult, $this->transactionResult);
                
                // create form to process a finanical claim
                //echo "<form action=\"CustomerClaim.php\" method=\"post\" name=\"submit-repair-claim\">";
                
                // object has hidden inputs for keycode and transaction ID so they can be retrieved from 
                // second form on the page, as the first form contains the first inputs for them
                $newHiddenInputsPanel = new HiddenInputsPanel($this->claimType);
                
            }
            
            // if product is not claimable but exists show information panel
            else if ($this->claimType != "keycode-not-found" && $this->keycode != null)
            {
                // create a form that does nothing - used to make html vaild as </form> is used later
                //echo "<form>";
                
                // if vaild product is found show the information about it
                $newInformationPanel = new InformationPanel($this->keycodeResult, $this->transactionResult);
            }
        }
        
        
        // returns the claim status
        public function getClaimType()
        {
            return $this->claimType;
        }
        
        private function formatNumber($number)
        {
            $formatedNumber = preg_replace("/\s/", "", $number);
            return $formatedNumber;
        }
        
        private function determainclaimType()
        {   
            // check to make sure the keycode is number and within
            // 9 digits
            if (preg_match($this->keycodeRegex, $this->keycode) == false)
            {
                $this->claimType = "invaild-keycode";
            }
            
            // if keycode is NOT null, then search in the database
            else if ($this->keycode != null)
            {
                $result =  mysql_query("SELECT Keycode, Claimable, Repairable,                                                     WarrantyMonths
                                        FROM Product 
                                        WHERE Product.Keycode = $this->keycode");

                $array = mysql_fetch_array($result);

                // if keycode is null, item does not exist in the database
                //if ($array["Keycode"] == false)
                if (empty($array))
                {
                    $this->claimType = "keycode-not-found";
                }
                // if product is claimable and repairable
                else if ($array["Claimable"] == 1 && $array["Repairable"] == 1)
                {    
                     // checks make sure transaction number was found, if number
                    // rows are about zero this means the query found a result
                    if (!empty($this->transactionResult))
                    {
                        // checks product is within warrant period based on purchase date
                        // returns true if it is and false if it is not
                        $warrantyBoolean = $this->withinWarrantyPeriod($this->transactionResult["PurchaseDate"], $array["WarrantyMonths"]);

                        if ($warrantyBoolean == true) 
                        {
                            //// check warranty period
                            $this->claimType = "repair-warranty";
                        }
                        else
                        {
                            // check warranty period
                            $this->claimType = "repair-no-warranty";
                        }
                    }
                    else // transactionResult number rows is zero, this means no transaction was founds
                    { 
                        $this->claimType = "repair-no-transaction";
                    }
                }
                 // if product offers a finanical claim
                else if ($array["Claimable"] == 1 && $array["Repairable"] == 0)
                {
                    // checks make sure transaction number was found, if number
                    // rows are about zero this means the query found a result
                    if (!empty($this->transactionResult))
                    {

                        // checks product is within warrant period based on purchase date
                        // returns true if it is and false if it is not
                        $warrantyBoolean = $this->withinWarrantyPeriod($this->transactionResult["PurchaseDate"], $array["WarrantyMonths"]);

                        if ($warrantyBoolean == true) // true means inside warranty period
                        {
                            $this->claimType = "finanical-warranty";
                        }
                        else // false means outside warranty period
                        {
                            $this->claimType = "finanical-outside-warranty";
                        }
                    }
                    else // transactionResult's rows was zero so no tranaction number was found
                    {
                        $this->claimType = "finanical-no-transaction";
                    }
                }
                // if a product is NOT claimable with supplier
                else if ($array["Claimable"] == 0)
                {
                     // checks make sure transaction number was found, if number
                    // rows are above zero this means the query found a result
                    if (!empty($this->transactionResult))
                    {
                        // checks product is within warrant period based on purchase date
                        // returns true if it is and false if it is not
                        $warrantyBoolean = $this->withinWarrantyPeriod($this->transactionResult["PurchaseDate"], $array["WarrantyMonths"]);

                        if ($warrantyBoolean == true) // true means inside warranty period
                        {
                            $this->claimType = "not-claimable-refund";
                        }
                        else // false means outside warranty period
                        {
                            $this->claimType = "not-claimable-outside-warranty";
                        }
                    }
                    else // no vaild transaction number provided
                    {
                        $this->claimType = "not-claimable-no-transaction";
                    }
                }
            }
           
        }
        
        // check within warranty period - retunrs true or false
        private function withinWarrantyPeriod($purchaseDate, $WarrantyMonths)
        {
            // get current date time and create obj
            $currentDate = new DateTime(); 
            
            // format date time into datetime obj
            $formatedPurchaseDate = new DateTime($purchaseDate); 
            
            // get difference between dates current date and purchase date
            $timeDifference = $currentDate->diff($formatedPurchaseDate);
            
            $days =  $currentDate->diff($formatedPurchaseDate)->d;
                
            $months = $currentDate->diff($formatedPurchaseDate)->m;
            
            $years = $currentDate->diff($formatedPurchaseDate)->y;
            
            $yearsMonths = $years * 12;
            // subtract difference in years from warranty - in months
            $WarrantyMonths -= $yearsMonths;
            
            // subtract diference in months from warranty
            $WarrantyMonths -= $months;
            
            // if there is zero months left, check to see that the days difference 
            // is not zero, which would indicates its the last day of warranty
            if ($WarrantyMonths <= 0 && $days > 0) 
            {
                return false;
            }
            else
            {    
                return true;
            }
        }
        
        // gets the products, supplier information from the database. The repair agent details
        // are also returned if the claimType is a repair claim
        private function getProductInformation()
        {
            
            // if product is repairable get the repair agents details as well as products and suppliers
            if ($this->claimType == "repair-warranty" || $this->claimType == "repair-no-warranty" || 
                $this->claimType == "repair-no-transaction")
            {
                $result = mysql_query(  "SELECT *
                                        FROM ProductSupplierRepairAgent_VIEW
                                        WHERE Keycode = $this->keycode");
                
                $this->keycodeResult = mysql_fetch_array($result);
            }
            // if the product is not repairable grab the product information and supplier information only
            else
            {
                $result = mysql_query(  "SELECT *
                                        FROM ProductSupplier_VIEW
                                        WHERE Keycode = $this->keycode");
                
                $this->keycodeResult = mysql_fetch_array($result);
            }
        }
        
        private function searchTransactionID()
        {
            // check to see iv vaild transaciton number range was provided, must be maxium of 20 characters is length
            if ($this->transactionID > 0 && $this->transactionID < 100000000000000000000)
            {
                // retrieve product, supplier, repair agent details from database
                $result =  mysql_query("SELECT TransactionID, PurchaseDate, PurchasePrice
                                        FROM TransactionProducts_VIEW
                                        WHERE TransactionID = $this->transactionID AND Keycode = $this->keycode");
                
                // store transaction results for use later
                $this->transactionResult = mysql_fetch_array($result);
                
              
                
            }
        }
        
        // creates the html for the search fields and places the keycode and transaction
        // numbers in the input
        private function createSearchFields()
        {
            
    echo "
    <fieldset>
            <legend> Search Product Claim Status </legend>
            <form action=\"CustomerClaim.php\" method=\"post\" name=\"product-warranty-search\">
                <div class=\"input-wrapper\">
                    <div class=\"left-wrapper\">
                        <label>Keycode: </label>
                        <input type=\"text\" class=\"input-text-fifty\" name=\"keycode-input\" value= \"$this->keycode\">
                    </div>

                    <div class=\"right-wrapper\">
                        <label>Transaction ID: </label>
                        <input type=\"text\" class=\"input-text-fifty\" name=\"transactionid-input\" value=\"$this->transactionID\">
                    </div>
                </div>

                <div class=\"input-wrapper-last\">
                    <div class=\"right-wrapper\">
                        <button text=\"Search\" type=\"submit\" class=\"search-button\" value=\"submit\"> Search </button>
                    </div>
                </div>
            </form>
        </fieldset>
          ";
        }
    }
?>
        