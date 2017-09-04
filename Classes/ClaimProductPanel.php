<?php

    class ClaimProductPanel
    {
        private $claimID;
        private $claimType;
        private $queryResult;
        private $jsonProduct = array();
        
        private $returnClaimProductsQuery = "SELECT QuantityRefunded, Keycode, CostPrice, Model, ProductName, SupplierID
                                            FROM `ReturnClaim_ModifyClaimPage` 
                                            WHERE claimID =";
        
        private $repairClaimProductQuery = "SELECT '1' as QuantityRefunded, Keycode, Model, ProductName, SupplierID
                                            FROM `RepairClaim_ModifyClaimPage` 
                                            WHERE claimID =";
        
        private $financialClaimProductQuery = "SELECT QuantityRefunded, Keycode, CostPrice,
                                            Model, ProductName, SupplierID
                                            FROM `FinancialClaim_ModifyClaimPage` 
                                            WHERE claimID =";
        private $productSelectQuery = " SELECT Keycode, ProductName, Model, CostPrice, SupplierID
                                        FROM `Product` 
                                        WHERE keycode = ";
        
        private $claimIDRegex = '/^[0-9]{1,20}$/';
        private $keycodeRegex =  '/^[0-9]{1,9}$/';
        private $quantityRegex =  '/^[0-9]{1,6}$/';
        
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
                $this->queryResult = mysql_query($this->repairClaimProductQuery . $claimID . ';' );
                
                // check the claims results was found - if less than one show error
                if (mysql_num_rows($this->queryResult) < 1)
                {
                    die("<h2> Error - ClaimID '$claimID' not found in database for a '$claimType'.</h2>");
                }
            }
            else if ($claimType == 'Return')
            {
                // retrieve all the products in the claim
                $this->queryResult = mysql_query($this->returnClaimProductsQuery . $claimID . ';' );
                
                // check the claims results was found - if less than one show error
                if (mysql_num_rows($this->queryResult) < 1)
                {
                    die("<h2> Error - ClaimID '$claimID' not found in database for a '$claimType'.</h2>");
                }
            }
            else if ($claimType == 'Financial')
            {
                // retrieve all the products in the claim
                $this->queryResult = mysql_query($this->financialClaimProductQuery . $claimID . ';' );
                
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
        
        // if modifying existing claim, loads the claim information from database
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
        
        public function startNewClaim()
        {
            // set claim type as new, to avoid accessing query
            $this->claimType = "new";
            
            // print the html to the screen
            $this->printHTML();

        }
        
        // checks for vaild keycode
        private function checkKeycode($keycode)
        {
            if (preg_match($this->keycodeRegex, $keycode) == false)
            {
                die("Error - Invaild Keycode Provided - Class ClaimProductPanel - Must be numbers only and between 1 and 9 characters in length.");
            }
        }
        
        // checks for vaild claimID
        private function checkClaimID($claimID)
        {
            if (preg_match($this->claimIDRegex, $claimID) == false)
            {
                die("Error - Invaild ClaimID Provided - Class ClaimProductPanel - Must be numbers only and between 1 and 20 characters in length.");
            }
        }
        
        // checks for vaild quantity returned
        private  function checkquantity($quantity)
        {
            if (preg_match($this->quantityRegex, $quantity) == false)
            {
                die("Error - Invaild Quantity Provided - Class ClaimProductPanel - Must be numbers only and between 1 and 6 characters in length.");
            }
        }
        
        // calculates the total cost for the claim by adding all the cost prices
        // and stores it in the session variable
        private function calculateTotalCostSession()
        {
            $totalRefund = 0;
            
            // loop through all products in the claim and add the cost prices
            // to get the total repair cost
            foreach($_SESSION['ProductsInClaim'] as $product)
            {
                $totalRefund += (float)($product['costprice'] * $product['quantity']);
            }
            
            // set the total cost in the session variable
            $_SESSION['ClaimAmount'] = $totalRefund;
        }
        
        // adds product to session and prints the new product to screen
        public function addProductSession($keycode, $quantity)
        {
            // check incoming variable for invaild types/injection
            $this->checkKeycode($keycode);
            $this->checkquantity($quantity);
            
            $productExistsBoolean = false;
            
            //create the query
            $query = $this->productSelectQuery . $keycode . ";";

            $result = mysql_query($query);

            $row = mysql_fetch_array($result);
            
            // check to make sure results where found - if not send error message back
            if (empty($row))
            {
                $array = array("response" => "product-not-found");
                echo json_encode($array);
            }
            // loop throught session to make sure product is not already
            // in the claim - if so set print boolean false
            // double check session exist - long periods of time session may time out
            // losing data if so show error 
            else if (isset($_SESSION))  
            {
                foreach($_SESSION['ProductsInClaim'] as $product)
                {
                    if($product['keycode'] == $keycode)
                    {
                        $array = array("response" => "already-in-claim");
                        echo json_encode($array);
                        
                        $productExistsBoolean = true;
                    }
                }
                
                // supplier must be the same or no products in the claim currently - other wise cannot add product
                if ( empty($_SESSION['ProductsInClaim']) || 
                    $_SESSION['ProductsInClaim'][0]['supplierid'] == $row['SupplierID'])
                {
                    // check print is response and print product to the screen
                    if ($productExistsBoolean == false)
                    {
                        $keycode =  $row['Keycode'];
                        $model = $row['Model'];
                        $description = $row['ProductName'];
                        $supplierID = $row['SupplierID'];
                        $costPrice = $row['CostPrice'];

                        $html = "
                           <div id=\"product-container-".$keycode."\" class=\"claimdetails-product-container\">
                                    <div class=\"quantity-container\" id=\"quantity-$keycode\"> ".$quantity." </div>
                                    <div class=\"keycode-container\" id=\"keycode-$keycode\"> ".$keycode." </div>
                                    <div class=\"model-container\" id=\"model-$keycode\"> ".$model." </div>
                                    <div class=\"product-description-container\" id=\"description-$keycode\"> ".$description." </div>

                                <button type=\"button\" class=\"claimdetails-removebutton-individual\" 
                                id=\"remove-".$keycode."\">Remove</button>
                            </div>";

                        // put the product being added into current session
                        $product = array("keycode" => $keycode, "quantity" => $quantity,
                                       "model" => $model, "description" => $description, "supplierid" => $supplierID,
                                        "costprice" => $costPrice);

                        array_push($_SESSION['ProductsInClaim'], $product);
                        
                        // since new product is added calulate new repair cost and add to session
                        $this->calculateTotalCostSession(); 
                        
                        $array = array("response" => "success", "html" => $html, "description" => $description, 
                                      "claim-amount" => $_SESSION['ClaimAmount']);
                        
                        echo json_encode($array);
                    }
                }
                else // suppliers not same dont add product send error type
                {
                    $array = array("response" => "different-supplier");
                    echo json_encode($array);
                }
            }
            else if (!isset($_SESSION)) 
            {
                $array = array("response" => "session-expired");
                echo json_encode($array);
            } 
        }
        
        // removes the keycode given from session array
        public function removeProductSession($keycode)
        {
            // get length of session array
            $arrayLength = count($_SESSION['ProductsInClaim']);
            
            $removedBoolean = false;
                
            // loop throught products in claim array stored in session variable
            for ($i = 0; $i < $arrayLength; $i++)
            {
                // if keycode in session matchs keycode brought in remove it from the array
                if ($_SESSION['ProductsInClaim'][$i]['keycode'] == $keycode)
                {
                    // remove the cost price of the product from claim price - before splicing array
                    $_SESSION['ClaimAmount'] -= (float)($_SESSION['ProductsInClaim'][$i]['costprice'] *
                                                      $_SESSION['ProductsInClaim'][$i]['quantity'] );
                    
                    //removes array element and resizes array - 1 is needed to remove one element only
                    array_splice($_SESSION['ProductsInClaim'],$i,1);
                    
                    $array = array("response" => "product-removed", "claim-amount" => $_SESSION['ClaimAmount']);
                    echo json_encode($array);
                    
                    $removedBoolean = true;
                    
                    // stop the loop once found, as the array is resized which can cause index out of
                    // range error
                    break;
                }
            } 
            if ($removedBoolean == false)
            {
                $array = array("response" => "product-not-removed");
                echo json_encode($array);
            }
        }

        // updates the given keycodes quantity in the session variable
        public function updateProductSession($keycode, $quantity)
        {
            $this->checkKeycode($keycode);
            $this->checkquantity($quantity);
            
            $arrayLength = count($_SESSION['ProductsInClaim']);
            $foundProductBoolean = false;

            //loop through session array and find the product
            for($i=0; $i < $arrayLength; $i++)
            {
                // if keycode matches update the quantity in the session
                if ($_SESSION['ProductsInClaim'][$i]['keycode'] == $keycode)
                {
                    $_SESSION['ProductsInClaim'][$i]['quantity'] = $quantity;
                    
                    $foundProductBoolean = true;
                    
                    $this->calculateTotalCostSession();

                    $array = array("response" => "product-updated", "description" => $_SESSION['ProductsInClaim'][$i]['description'], "claim-amount" => $_SESSION['ClaimAmount']);
                    echo json_encode($array);

                    break; // break out of loop as no more checks are needed
                }
            }

            if ($foundProductBoolean == false)
            {
                $array = array("response" => "product-not-updated");
                echo json_encode($array);
            }
        }
        
        
        // prints the selected claims to the screen
        private function printHTML()
        {
            $row;
            $quantity;
            $keycode;
            $model;
            $description;
            $_SESSION['ProductsInClaim'] = array(); // all products in claim will be stored here
            
            // claim type is a repair block controls and put the one product into the inputs on load
            if($this->claimType == 'Warranty' || $this->claimType == 'Non-Warranty')
            {
                $row = mysql_fetch_array($this->queryResult);
                $quantity = $row['QuantityRefunded'];
                $keycode =  $row['Keycode'];
                $model = $row['Model'];
                $description = $row['ProductName'];
                $supplierID = $row['SupplierID'];

                
                // add product details to session for re-use later when saving claim
                // if page is loading for the first time reset the products 
                // in the claim session
                $product = array("keycode" => $keycode, "quantity" => $quantity,
                                   "model" => $model, "description" => $description, "supplierid" => $supplierID,
                                    "costprice" => $_SESSION['ClaimAmount']);
                        
                array_push($_SESSION['ProductsInClaim'], $product);
                
                echo "
                <fieldset>
                    <legend>Product Details</legend>
                    <div class=\"input-wrapper\">

                        <div class=\"left-wrapper\">
                            <label>Keycode: </label>
                            <input type=\"text\" class=\"input-text-fifty\" id=\"claimdetails-keycode-input\" value=\"$keycode\" disabled>
                        </div>

                        <div class=\"right-wrapper\">
                            <label>Product Description: </label>
                            <input type=\"text\" class=\"input-text-fifty\" id=\"claimdetails-productdescription-input\" value=\"$description\" disabled>
                        </div>
                    </div>

                    <div class=\"input-wrapper\">
                        <div class=\"left-wrapper\">
                            <label>Quantity in Claim: </label>
                            <input type=\"text\" class=\"input-text-fifty\" id=\"claimdetails-quantity-input\" value=\"$quantity\" disabled>
                        </div>
                        <div class=\"right-wrapper\">
                            <button text=\"Search\" type=\"button\" id=\"claimdetails-removeproduct-button\"
                            class=\"product-details-buttons\" disabled> Remove </button>
                            <button text=\"Search\" type=\"button\" id=\"claimdetails-updateproduct-button\"
                            class=\"product-details-buttons\" disabled> Update </button>
                            <button text=\"Search\" type=\"button\" id=\"claimdetails-addproduct-button\"
                            class=\"product-details-buttons\" disabled> Add </button>
                        </div>
                    </div>

                </fieldset>

                <fieldset>
                    <legend>Products in Claim</legend>
                    <div class=\"input-wrapper\">
                        <div class=\"quantity-container\">Qty</div>
                        <div class=\"keycode-container\">Keycode</div>
                        <div class=\"model-container\">Model</div>
                        <div class=\"product-description-container\">Description</div>
                    </div>

                    <div class=\"line\"></div>
                    
               <div id=\"product-container-$keycode\" class=\"claimdetails-product-container\">
                        <div class=\"quantity-container\" id=\"quantity-$keycode\" >$quantity</div>
                        <div class=\"keycode-container\"  id=\"keycode-$keycode\" >$keycode</div>
                        <div class=\"model-container\" id=\"model-$keycode\">$model</div>
                        <div class=\"product-description-container\" id=\"description-$keycode\">$description</div>

                    <button type=\"button\" class=\"claimdetails-removebutton-individual\" id=\"remove-$keycode\" disabled>Remove</button>
                </div>
                </fieldset>";
            }
            else
            {
                echo "
                <fieldset>
                    <legend>Product Details</legend>
                    <div class=\"input-wrapper\">

                        <div class=\"left-wrapper\">
                            <label>Keycode: </label>
                            <input type=\"text\" class=\"input-text-fifty\" id=\"claimdetails-keycode-input\">
                        </div>

                        <div class=\"right-wrapper\">
                            <label>Product Description: </label>
                            <input type=\"text\" class=\"input-text-fifty\" id=\"claimdetails-productdescription-input\" disabled>
                        </div>
                    </div>

                    <div class=\"input-wrapper\">
                        <div class=\"left-wrapper\">
                            <label>Quantity in Claim: </label>
                            <input type=\"text\" class=\"input-text-fifty\"id=\"claimdetails-quantity-input\">
                        </div>
                        <div class=\"right-wrapper\">
                            <button text=\"Search\" type=\"button\" class=\"product-details-buttons\"
                            id=\"claimdetails-removeproduct-button\"> Remove </button>
                            
                            <button text=\"Search\" type=\"button\" class=\"product-details-buttons\"
                            id=\"claimdetails-updateproduct-button\"> Update </button>
                            
                            <button text=\"Search\" type=\"button\" class=\"product-details-buttons\"
                            id=\"claimdetails-addproduct-button\"> Add </button>
                            
                        </div>
                    </div>

                </fieldset>

                <fieldset>
                    <legend>Products in Claim</legend>
                    <div class=\"input-wrapper\">
                        <div class=\"quantity-container\">Qty</div>
                        <div class=\"keycode-container\">Keycode</div>
                        <div class=\"model-container\">Model</div>
                        <div class=\"product-description-container\">Description</div>
                    </div>

                    <div class=\"line\" id=\"claimdetails-insertline\"></div>
                ";
                
                // if claim type not 'new' print the products to the screen
                if ($this->claimType != 'new')
                {
                    // loop through results and print each product in claim to the screen
                    while($row = mysql_fetch_array($this->queryResult))
                    {
                        $quantity = $row['QuantityRefunded'];
                        $keycode =  $row['Keycode'];
                        $model = $row['Model'];
                        $description = $row['ProductName'];
                        $supplierID = $row['SupplierID'];
                        $costPrice = $row['CostPrice'];
                        
                        // add product details to session for re-use later when saving claim
                        // if page is loading for the first time reset the products 
                        // in the claim session
                        $product = array("keycode" => $keycode, "quantity" => $quantity,
                                   "model" => $model, "description" => $description, "supplierid" => $supplierID,
                                    "costprice" => $costPrice);
                        
                        array_push($_SESSION['ProductsInClaim'], $product);
                

                        echo "
                       <div id=\"product-container-$keycode\" class=\"claimdetails-product-container\">
                                <div class=\"quantity-container\" id=\"quantity-$keycode\">$quantity</div>
                                <div class=\"keycode-container\" id=\"keycode-$keycode\">$keycode</div>
                                <div class=\"model-container\" id=\"model-$keycode\">$model</div>
                                <div class=\"product-description-container\" id=\"description-$keycode\">$description</div>

                            <button type=\"button\" class=\"claimdetails-removebutton-individual\" id=\"remove-$keycode\">Remove</button>
                        </div>";
                    }
                }
                echo "</fieldset>";    
            }
        }
    }
?>