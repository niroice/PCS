<?php
    class FinancialClaim
    {
        private $claimID;
        private $status;
        private $keycodesArray = array();
        private $quantitiesArray = array();
        private $quantityRefunded; 
        private $totalRefundInputBox; // store total refund from the total refund input box
        private $totalRefund; // store calculate refund when add, update or remove button is clicked
        private $existingClaimBoolean = false;
        private $newClaimBoolean = false;
        
        private $keycodeRegex = "/^[0-9]{1,9}$/";
        private $quantityRegex = "/^[0-9]{1,6}$/";
        private $totalRefundRegex = "/^[0-9]{1,7}\.{0,1}[0-9]{0,2}$/";
        private $claimIDRegex = "/^[0-9]{1,20}$/";
        
        public function __construct()
        {
            // do nothing
        }
        
        // returns the claim IDs
        public function getClaimID()
        {
            return $this->claimID;
        }
        
        // adds a product to the claim
        public function addProduct($keycode, $quantity)
        {
            // remove any white spaces from the numbers so formating is correct
            $keycode = $this->formatNumber($keycode);
            $quantity = $this->formatNumber($quantity);

            // check make sure vaild number ranges are provided
            if(preg_match($this->keycodeRegex, $keycode) == true && preg_match($this->quantityRegex, $quantity) == true )
            {
                // check to make sure the transactionID provided is vaild/in database
                $result = mysql_query(  "   SELECT CostPrice
                                            FROM Product
                                            WHERE Keycode = $keycode")
                    or die("
                            <h2> Error - Keycode could not be found in the database. </h2>
                    ");

                $cost = mysql_fetch_array($result);

                // add the current products cost and quantity to the total refund price
                $this->totalRefund += ((float)$cost * (float)$quantity);

                // store keycode and quanitity into array to push into productsArray
                array_push($this->keycodesArray, $keycode);

                array_push($this->quantitiesArray, $quantity);

            }
            else
            {
                die("<h2> Error - Invaild Keycode and/or Quantity provided - Keycode must contain only numbers and be between 1-9 characters in length. Quantity must be a number and be between 1 to 6 characters in length. </h2> ");
            }
        }

        // checks for a vaild status and sets the global variable
        public function setStatus($status)
        {
            $vaildStatusBoolean = false;
     
            // checks to make sure the status is vaild
            switch($status) {
                case "Created":
                    $vaildStatusBoolean = true;
                    break;
                case "Claim Raised":
                    $vaildStatusBoolean = true;
                    break;
                case "Complete":
                    $vaildStatusBoolean = true;
                    break;
                case "Cancelled":
                    $vaildStatusBoolean = true;
                    break;    
            }
            
            // if vaild status, assign status to the global variable
            if($vaildStatusBoolean == true)
            {
                $this->status = $status;
            }
            else{
                die("<h2> Error - Invaild Repair Status - Must be ethier Created, Claim Raised, Complete or Cancelled");
            }
        }
        
        // creates the string query for the products and quantity and returns as string
        private function generateFinancialProductsQuery()
        {
            // begining of the query
            $financialProductQuery = "INSERT INTO FinancialClaimProduct (ClaimID,Keycode,QuantityRefunded)
                        VALUES";
            
            // get size of array
            $size = count($this->keycodesArray);
            
            // generate the values for the FinancialClaimProduct query
            for($i = 0; $i < $size; $i++)
            {  
                $financialProductQuery .= "(";
                $financialProductQuery .= $this->claimID;
                $financialProductQuery .= ",";
                $financialProductQuery .= $this->keycodesArray[$i];
                $financialProductQuery .= ","; 
                $financialProductQuery .= $this->quantitiesArray[$i];
                $financialProductQuery .= ")";
                $financialProductQuery .= ","; 
            }
            
            // remove the last comma from the query as its not needed
            $financialProductQuery = rtrim($financialProductQuery, ",");
                
            // add semi colon to the end of the query
            $financialProductQuery .= ";";
            
            // returns the query string
            return $financialProductQuery;
        }
        
        // sets up the object as new claim
        public function setAsNewClaim($status, $totalRefund)
        {
            // check for vaild passed in variables
            $this->setStatus($status);
            $this->setTotalRefund($totalRefund);
            
            $this->newClaimBoolean = true;
        }
        
        // adds the repair claim to the database
        public   function addClaimDatabaseSQL()
        {
            // check to make sure the claim is existing claim and has been setup
            if ($this->newClaimBoolean == true)
            {
                // check to make sure at least one product is in the claim before adding to database
                if (!empty($this->keycodesArray))
                {
                    $currentDate = date("Y/m/d");

                    // three different queries are used to insert into the three differe tables. 
                    // Had to use different queries due to php creating a new connection for each insert
                    // statement which made the "SET @name = LAST_INSERT_ID()" not work.
                    $query = mysql_query("
                                INSERT INTO Claim (CreatedDate)
                                VALUES ('$currentDate');
                                ")
                        or
                        die("<h2> Error - Query Failed to add the claim to the Claim table in the database </h2>");

                    $this->claimID = mysql_insert_id(); // saving claimID to use in the last query

                    $query = mysql_query("
                                INSERT INTO FinancialClaim (ClaimID,ClaimStatus, TotalRefund)
                                VALUES ($this->claimID,'$this->status','$this->totalRefund');
                                        ")
                        or
                        die("<h2> Error - Query Failed to add the claim to the FinancialClaim table in the database </h2>");

                    // generate the query for the FinancialClaimProduct
                    $productQueryString = $this->generateFinancialProductsQuery();

                    $query = mysql_query($productQueryString)
                        or
                        die("<h2> Error - Query Failed to add product to the FinancialClaimProduct table in the database </h2>"); 
                }
                else
                {
                    die("<h2> Error - Before adding a claim to the database, at least one product must exist in the claim.</h2>"); 
                }
            }
            else
            {
                die ("<h2> Error - Must set claim as new claim to use function 'addClaimDatabaseSQL()'. Function 'setAsNewClaim(claimID, status, raNumber, totalRefund)' is used to set the claim as new. </h2>");
            }
        }
        
        // sets up the object as existing claim loading claim details
        public function setAsExistingClaim($claimID, $status, $totalRefund)
        {
            // check for vaild passed in variables
            $this->setStatus($status);
            $this->setTotalRefund($totalRefund);
            $this->setClaimID($claimID);
            
            $this->existingClaimBoolean = true;
        }
        
        // checks for vaild claim id - if invaild displays error
        private function setClaimID($claimID)
        {
            if(preg_match($this->claimIDRegex, $claimID) == true)
            {
                $this->claimID = $claimID;
            }
            else
            {
                die ('<h2> Error - Invaild claimID - Must contain only numbers and be between 1-20 characters in length.');
            }
        }
        
        // sets the total refund and checks for vaild combination
        private function setTotalRefund($totalRefund)
        {
            if(preg_match($this->totalRefundRegex, $totalRefund) == true)
            {
                $this->totalRefund = $totalRefund;
                $this->totalRefundInputBox = $totalRefund;;
            }
            else
            {
                die("<h2> Error - Invaild Total Refund - Must contain only numbers and a decimal place.");
            }
        }
        
        
        // updates a financial claim in the database
        public function updateClaimDatabaseSQL()
        { 
            // check to make sure the claim is existing claim and has been setup
            if ($this->existingClaimBoolean == true)
            {
            
                $query = mysql_query("
                            UPDATE FinancialClaim 
                            SET ClaimStatus = '$this->status', TotalRefund = '$this->totalRefundInputBox'
                            WHERE FinancialClaim.ClaimID = $this->claimID;
                                    ")
                    or
                    die("<h2> Error - Update Finanical claim table, Query Failed. Check Class Finanical Claim.</h2>");


                $arrayLength = count($this->keycodesArray); // length for the loop
                
                // remove all the existing products in the claim table
                $query = mysql_query("  DELETE FROM FinancialClaimProduct
                                        WHERE ClaimID = $this->claimID;");

                for($i=0; $i< $arrayLength; $i++)
                {
                    // loop through the products in the claim and insert into the database
                    // query trys to insert, if it exist it updates instead
                    $query = mysql_query("
                                        INSERT INTO FinancialClaimProduct
                                          (ClaimID, Keycode, QuantityRefunded)
                                        VALUES
                                          ($this->claimID, ".$this->keycodesArray[$i].", ".$this->quantitiesArray[$i].")
                                        ON DUPLICATE KEY UPDATE
                                        ClaimID = VALUES(ClaimID),
                                        Keycode = VALUES(Keycode),
                                        QuantityRefunded = VALUES(QuantityRefunded);
                                        ")
                        or
                        die("<h2> Error - Update Finanical claim product table, Query Failed. Check Class Finanical Claim.</h2>");
                }
            }
            else
            {
                die ("<h2> Error - Must set claim as existing to use function 'updateClaimDatabaseSQL()'. Function 'setAsExistingClaim(claimID, status, raNumber, totalRefund)' is used to set the claim as existing.  </h2>");
            }
        }
        
        // removes any spaces from numbers to keep formatting correct
        private function formatNumber($number)
        {
            $formatedNumber = preg_replace("/\s/", "", $number);
            return $formatedNumber;
        }
    }
?>