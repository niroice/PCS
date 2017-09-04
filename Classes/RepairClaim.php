<?php

    class RepairClaim
    {
        private $keycode;
        private $transactonID;
        private $status;
        private $firstName;
        private $lastName; 
        private $phone;
        private $email;
        private $unitHouseNumber; 
        private $street;
        private $suburb;
        private $cityState; 
        private $postcode;
        private $repairCost;
        private $repairAuthorisedName;
        private $repairType;
        private $repairMessage;
        private $claimID;
        private $raNumber;
        
        private $raNumberRegex = "/^[a-zA-Z0-9\s]{0,25}$/";
        private $keycodeRegex = "/^[0-9]{1,9}$/";
        private $claimIDRegex = "/^[0-9]{1,20}$/";
        private $transactionIDRegex = "/^[0-9\s]{0,20}$/";
        private $nameRegex = "/^[a-zA-Z\s]{2,15}$/";
        private $phoneRegex = "/^[0-9\s]{8,20}$/";
        private $emailRegex = "/^[a-zA-Z0-9-_\.]{1,40}@{1}[a-zA-Z0-9-_\.]{1,40}[\.]{1}[a-zA-Z0-9-_\.]{1,40}$/s";
        private $unitHouseRegex ="/^[0-9\/\s]{1,9}$/";
        private $streetRegex = "/^[a-zA-Z\s]{1,20}$/";
        private $suburbRegex = "/^[a-zA-Z\s]{1,30}$/";
        private $cityStateRegex =  "/^[a-zA-Z\s]{1,30}$/";
        private $postcodeRegex = "/^[0-9]{4}$/";
        private $repairCostRegex = "/^[0-9]{1,7}\.{0,1}[0-9]{0,2}$/";
        private $repairAuthorisedNameRegex = "/^[a-zA-Z\s]{1,25}$/";
        private $repairMessageRegex = "/^.{0,3000}$/s";
        
        public function __construct()
        {
            // does nothing
        }
        
        // returns the claim IDs
        public function getClaimID()
        {
            return $this->claimID;
        }
        
        // checks for vaild keycode and sets the global variable
        private function setKeycode($keycode)
        {
            $keycode = $this->formatNumber($keycode);
            
            if(preg_match($this->keycodeRegex, $keycode) == true)
            {
                // check to make sure the keycode provided is vaild/in database
                $result = mysql_query(  "SELECT *
                                        FROM Product
                                        WHERE Keycode = $keycode");

                $keycodeResult = mysql_fetch_array($result);

                // checks to see if the product was found - if not empty then it was
                if(!empty($keycodeResult))
                {
                    $this->keycode = $keycode;
                }
                else{
                    die("<h2> Error - Invaild Keycode - Product could not be found in database.");
                }
            }
            else
            {
                
                die("<h2> Error - Invaild Keycode - Must contain only numbers and be between 1-9 characters in length.");
            }
            
        }
        
        // checks for vaild transactionID in the database and sets the global variable
        private function setTransactionID($transactionID)
        {
            if(preg_match($this->transactionIDRegex, $transactionID) == true)
            {
                // remove any spaces before submitting to database
                $transactionID = $this->formatNumber($transactionID);
                
                // check the claimtype first, if it is repair without warranty, no transaction is needed
                // in which search should be skipped
                if($_POST['claimtype-input-hidden'] != "repair-no-transaction" && $_POST['claimtype-input-hidden'] != "repair-no-warranty")
                {
                    // check to make sure the transactionID provided is vaild/in database
                    $result = mysql_query(  "SELECT *
                                            FROM Transaction
                                            WHERE TransactionID = $transactionID");

                    // if transaction was not found it will be false, stop and show error
                    if($result == false)
                    {
                        die("<h2> Error - Invaild Transaction ID - Transaction could not be found in the database.");
                    }
                }
                // if no vaild transaction number is available set it to the word NULL for database query
                else
                {
                    $this->transactionID = null;
                }
            }
            else
            {
                die("<h2> Error - Invaild Transaction ID - Must contain only numbers and be between 1-20 characters in length.");
            }
            
            
        }
        
        // checks for vaild first name and sets the global variable
        private function setFirstName($firstName)
        {
            // regex checks to make sure string is between 2-15 characters and only contains letters and white spaces
            if(preg_match($this->nameRegex, $firstName) == true)
            {
                $this->firstName = $firstName;
            }
            else{
                die("<h2> Error - Invaild First Name - Must be between 2 and 15 characters in length and contain only letters and white spaces");
            }
        }
        
        // checks for vaild last name and sets the global variable
        private function setLastName($lastName)
        {
            // regex checks to make sure string is between 2-15 characters and only contains letters and white spaces
            if(preg_match($this->nameRegex, $lastName) == true)
            {
                $this->lastName = $lastName;
            }
            else{
                die("<h2> Error - Invaild Last Name - Must be between 2 and 15 characters in length and contain only letters and white spaces");
            }
        }
        
        // checks for vaild home or mobile number and sets the global variable
        private function setPhone($phone)
        {
            $phone = $this->formatNumber($phone);
            
            // regex checks to make sure string is between 2-15 characters and only contains letters and white spaces
            if(preg_match($this->phoneRegex, $phone) == true)
            {
                $this->phone = $phone;
            }
            else{
                die("<h2> Error - Invaild Phone Number - Must be between 8 and 20 characters in length and contain only numbers and white spaces </h2>");
            }
        }
        
        // checks for vaild email and sets the global variable
        private function setEmail($email)
        {
            if ($email == null)
            {
                echo "<h2> email - is null </h2>";
            }
            if ($email == "")
            {
                echo "<h2> email - is blank string </h2>";
            }
            
            // regex checks to make sure string is between 2-30 characters and only contains letters and white spaces
            // or email is allowed to be null as its not mandatory
            if(preg_match($this->emailRegex, $email) == true)
            {
                $this->email = $email;
            }
            else if ($email == null)
            {
                $this->email = null;
            }
            else{
                die("<h2> Error - Invaild Email - Must contain an @ and . operators </h2>");
            }
        }
        
        
        // checks for vaild unit/house number and sets the global variable
        private function setUnitHouseNumber($unitHouseNumber)
        {
            // regex checks to make sure string is between 2-15 characters and only contains letters and white spaces
            if(preg_match($this->unitHouseRegex, $unitHouseNumber) == true)
            {
                $this->unitHouseNumber = $unitHouseNumber;
            }
            else{
                die("<h2> Error - Invaild Unit/House Number - Must be between 1 and 9 characters in length and contain only numbers, white spaces and /");
            }
        }
        
        // checks for vaild street name and sets the global variable
        private function setStreet($street)
        {
            // regex checks to make sure string is between 2-20 characters and only contains letters and white spaces
            if(preg_match($this->streetRegex, $street) == true)
            {
                $this->street = $street;
            }
            else{
                die("<h2> Error - Invaild Street Name - Must be between 1 and 20 characters in length and contain only letters and white spaces");
            }
        }
        
        // checks for vaild suburb and sets the global variable
        private function setSuburb($suburb)
        {
            // regex checks to make sure string is between 2-30 characters and only contains letters and white spaces
            if(preg_match($this->suburbRegex, $suburb) == true)
            {
                $this->suburb = $suburb;
            }
            else{
                die("<h2> Error - Invaild Suburb Name - Must be between 1 and 30 characters in length and contain only letters and white spaces");
            }
        }
        
        // checks for vaild city and/or state and sets the global variable
        private function setCityState($cityState)
        {
            
            // regex checks to make sure string is between 2-30 characters and only contains letters and white spaces
            if(preg_match($this->cityStateRegex, $cityState) == true)
            {
                $this->cityState = $cityState;
            }
            else{
                die("<h2> Error - Invaild City and/or State Name - Must be between 1 and 30 characters in length and contain only letters and white spaces");
            }
        }
        
        // checks for vaild postcode and sets the global variable
        private function setPostcode($postcode)
        {
            $postcode = $this->formatNumber($postcode);
            
            // regex checks to make sure string is between 2-30 characters and only contains letters and white spaces
            if(preg_match($this->postcodeRegex, $postcode) == true)
            {
                $this->postcode = $postcode;
            }
            else{
                die("<h2> Error - Invaild Postcode - Must be 4 characters in length and contain only numbers");
            }
        }
        
        // checks for vaild repair cost and sets the global variable
        private function setRepairCost($repairCost)
        {
            // regex checks to make sure string is vaild price and only contains numbers and full stops.
            if(preg_match($this->repairCostRegex, $repairCost) == true)
            {
                $this->repairCost = $repairCost;
            }
            // if no repair cost was given, pass null into the variable
            else if ($repairCost == "")
            {
                $this->repairCost = null;
            }
            else{
                die("<h2> Error - Invaild Repair Cost - Must contain only numbers and be vaild price format, eg 0.00");
            }
        }
        
        // checks for a vaild authorised by name was entered and sets the global variable
        private function setAuthorisedBy($repairAuthorisedName)
        {
            // regex checks to make sure string is between 2-25 characters and only contains letters and white spaces
            if(preg_match($this->repairAuthorisedNameRegex, $repairAuthorisedName) == true)
            {
                $this->repairAuthorisedName = $repairAuthorisedName;
            }
            else{
                die("<h2> Error - Invaild Name - Must be between 1 and 25 characters in length and contain only letters and white spaces");
            }
        }
        
        // checks for a vaild repair type and sets the global variable
        private function setRepairType($repairType)
        {
            // checks to make sure the warranty type is correct, before being submitted to database
            if($repairType == "Warranty" || $repairType =="Non-Warranty")
            {
                $this->repairType = $repairType;
            }
            else{
                die("<h2> Error - Invaild Repair Type provided - Must be ethier Warranty or Non-Warranty");
            }
        }

        // checks for a vaild status and sets the global variable
        private function setStatus($status)
        {
            $vaildStatusBoolean = false;
            
            // checks to make sure the status is vaild
            switch($status) {
                case "Created":
                    $vaildStatusBoolean = true;
                    break;
                case "RA Requested":
                    $vaildStatusBoolean = true;
                    break;
                case "Shipped":
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
                die("<h2> Error - Invaild Repair Status - Must be ethier Created, RA Requested, Shipped, Complete, Cancelled");
            }
        }
        
        // used in query to determain if transactionID should be placed in the query
        // fixes issue with null value causing a sql error. TypeNeed is if the function should
        // return the column name or column value eg "column" or "value"
        private function checkTransactionRequiredQuery($typeNeeded)
        {
            if($this->transactonID != null)
            {
                //
                if($typeNeeded == "column")
                {
                    return "TransactionID,";
                }
                else if($typeNeeded == "value")
                {
                    return $this->transactonID + ",";
                }
                else
                {
                    die("<h2> Error - Invaild TypeNeeded - Must be ethier column or value");
                }
            }
            else
            {
                return null;
            }
        }
        
        // adds the repair claim to the database
        private function addClaimDatabase()
        {
            $currentDate = date("Y/m/d");
            
            // three different queries are used to insert into the three differe tables. 
            // Had to use different queries due to php creating a new connection for each insert
            // statement which made the "SET @name = LAST_INSERT_ID()" not work.
            $query = mysql_query("
                        INSERT INTO Customer (FirstName, LastName, CustomerPhone, CustomerEmail, HouseNumber, Street, SuburbCity, State, PostCode)
                        VALUES ('$this->firstName','$this->lastName','$this->phone','$this->email','$this->unitHouseNumber','$this->street','$this->suburb','$this->cityState','$this->postcode');
                        ")
                or
                die("<h2> Error - Query Failed to add the the customer to the database </h2>");

            $customerID = mysql_insert_id(); // saving the customer ID to use in the last query
            
            echo "<h1> customer id = $customerID</h1>";
            
            $query = mysql_query("
                        INSERT INTO Claim (CreatedDate)
                        VALUES ('$currentDate');
                        ")
                or
                die("<h2> Error - Query Failed to add the claim to the Claim table in the database </h2>");

            $this->claimID = mysql_insert_id(); // saving claimID to use in the last query 
            
            echo "<h1> $this->claimID,'$this->repairType','$this->status',$this->keycode, ".$this->checkTransactionRequiredQuery("value")." $customerID, NULL, '$this->repairCost','$this->repairAuthorisedName','$this->repairMessage'</h1>";

            $query = mysql_query("
                        INSERT INTO RepairClaim (ClaimID, Type, ClaimStatus, Keycode,".$this->checkTransactionRequiredQuery("column")." CustomerID, 	ReturnAuthorizationNumber, RepairCost, ReturnAuthorizedName, RepairMessage)
                        VALUES ($this->claimID,'$this->repairType','$this->status',$this->keycode, ".$this->checkTransactionRequiredQuery("value")." $customerID, NULL, '$this->repairCost','$this->repairAuthorisedName','$this->repairMessage');
                                ")
                or
                die("<h2> Error - Query Failed to add the repair claim to the RepairClaim table in the database </h2>");
            
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
        
        
        // checks for vaild return authorisation number - if invaild dispays error 
        private function setRANumber($raNumber)
        {
            if(preg_match($this->raNumberRegex, $raNumber) == true)
            {
                $this->raNumber = $raNumber;
            }
            else
            {
                die ('<h2> Error - Invaild RA Number - Must contain numbers and Letters only; be between 1-20 characters in length.');
            }
        }
        
        
        // adds a new repair claim into the RepairClaim table - checks for vaild inputs before submission
        public function addRepairClaimSQL($keycode, $transactonID, $repairType, $status, $firstName, $lastName, $phone, $email, $unitHouseNumber, $street, $suburb, $cityState, $postcode, $repairCost, $repairAuthorisedName, $repairMessage)
        {
            $this->setKeycode($keycode);
            $this->setTransactionID($transactonID);
            $this->setRepairType($repairType);
            $this->setStatus($status);
            $this->setFirstName($firstName);
            $this->setlastName($lastName);
            $this->setPhone($phone);
            $this->setEmail($email);
            $this->setUnitHouseNumber($unitHouseNumber);
            $this->setStreet($street);
            $this->setSuburb($suburb);   
            $this->setCityState($cityState);   
            $this->setPostcode($postcode);  
            $this->setRepairCost($repairCost);  
            $this->setAuthorisedBy($repairAuthorisedName);
            $this->setRepairMessage($repairMessage);
            $this->addClaimDatabase();
        }
        
        // updates an existing claim in the repair claim table - checks for vaild types before submission
        public function updateRepairClaimSQL ($claimID, $firstName, $lastName, $phone, $email, $houseNumber, $street, $suburb, $cityState, $postcode, $repairType, $claimStatus, $raNumber, $repairCost, $returnAuthorizedName, $repairMessage)
        {
            $this->setClaimID($claimID);
            $this->setFirstName($firstName);
            $this->setlastName($lastName);
            $this->setPhone($phone);
            $this->setEmail($email);
            $this->setUnitHouseNumber($houseNumber);
            $this->setStreet($street);
            $this->setSuburb($suburb);   
            $this->setCityState($cityState);
            $this->setPostcode($postcode); 
            $this->setRepairType($repairType);
            $this->setStatus($claimStatus);
            $this->setRANumber($raNumber);
            $this->setRepairCost($repairCost);  
            $this->setAuthorisedBy($returnAuthorizedName);
            $this->setRepairMessage($repairMessage);
            $this->sendUpdateQuery();
        }
        
        // adds the repair claim to the database
        private function sendUpdateQuery()
        {
            
            // 
            $query = mysql_query("
                        UPDATE Customer 
                        SET FirstName = '$this->firstName', LastName = '$this->lastName', CustomerPhone = '$this->phone', CustomerEmail = '$this->email', HouseNumber = '$this->unitHouseNumber', Street = '$this->street', SuburbCity = '$this->suburb', State = '$this->cityState', PostCode = '$this->postcode'
                        WHERE Customer.CustomerID = (
                                                    SELECT CustomerID
                                                    FROM RepairClaim
                                                    WHERE RepairClaim.ClaimID = $this->claimID);
                        ")
                or
                die("<h2> Error - Query Failed to update the customer for the repair claim</h2>");

            $query = mysql_query("
                        UPDATE RepairClaim 
                        SET Type = '$this->repairType', ClaimStatus = '$this->status', ReturnAuthorizationNumber = '$this->raNumber', RepairCost = '$this->repairCost', ReturnAuthorizedName = '$this->repairAuthorisedName', RepairMessage = '$this->repairMessage'
                        WHERE RepairClaim.ClaimID = $this->claimID;
                                ")
                or
                die("<h2> Error - Query Failed to update the repair claim details in the RepairClaim table.</h2>");
            
        }
        
        // checks for a vaild repair message and sets the global variable
        private function setRepairMessage($repairMessage)
        {
            // regex checks to make sure string is between 1-3000 characters
            if(preg_match($this->repairMessageRegex, $repairMessage) == true)
            {
                $this->repairMessage = $repairMessage;
            }
            else{
                die("<h2> Error - Invaild Repair Message - Must be between 1 and 3000 characters in length");
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