<?php
    
    class RepairAgentPanel
    {
        private $claimID;
        private $claimType;
        private $queryResult;
        private $repairAgentID;
        private $name;
        private $phone;
        private $email;
        private $unitHouse;
        private $street;
        private $suburbCity;
        private $state;
        private $postcode;
        private $supplierID;
        
        private $claimIDRegex = '/^[0-9]{1,20}$/';
        private $repairAgentIDRegex = '/^[0-9]{1,20}$/';
        private $nameRegex = "/^[a-zA-Z0-9-_@&#%'\s]{2,40}$/";
        private $phoneRegex = "/^[0-9\s]{8,20}$/";
        private $emailRegex = "/^[a-zA-Z0-9-_\.]{1,40}@{1}[a-zA-Z0-9-_\.]{1,40}[\.]{1}[a-zA-Z0-9-_\.]{1,40}$/s";
        private $unitHouseRegex ="/^[0-9\/\-\s]{1,11}$/";
        private $streetRegex = "/^[a-zA-Z\s]{1,40}$/";
        private $suburbCityRegex = "/^[a-zA-Z,\s]{1,70}$/";
        private $stateRegex =  "/^[a-zA-Z\s]{1,40}$/";
        private $postcodeRegex = "/^[0-9]{4}$/";
        private $supplierIDRegex = "/^[0-9]{1,20}$/";
        
        public function __construct()
        {          
            // do nothing
        }
        
        
        // description: Prints Repair Agent Panel to the screen (HTML) based on a provided claim ID
        //              and claim type; must be warranty or non-warraty
        // input:       ClaimID, ClaimType
        // output:      HTML
        public function printRepairAgentPanel($claimID, $claimType)
        {
            // check to make sure vaild claim type is being entered - stop injection
            if (preg_match($this->claimIDRegex, $claimID) == false)
            {
                die("<h2> Error - Invaild Claim ID provided for class RepairAgentPanel - loadClaim() - Must only contain numbers and maxium 20 characters in length.</h2>");
            }
            
            // set claimID and type
            $this->claimID = $claimID;
            $this->claimType = $claimType;
            
            // get all the claims information from database - saves to global variable
            $this->getRepairAgentDetailsSQL($claimID, $claimType);
            
            // generate the html and print to screen
            echo $this->generateRepairAgentPanelHTML();
            
        }
        
        // description: Retrieves the ID and name of Repair Agents for a particular supplier
        //              and creates each repair agent as an html tag <option>. Result is returned
        //              as a string.
        // input:       String
        // output:      String
        private function generateRepairAgentOptionsHTML($supplierID)
        {
            // if repair agent ID not null then create list
            if ($this->repairAgentID != null)
            {
                // holds the html for the repair agent panel options
                $selectRepairAgentHtml = " <option value=\"$this->repairAgentID\" id=\"repairagent-option-$this->repairAgentID\"> $this->name </option>";

                $result = mysql_query( "
                                        SELECT RepairAgentID, RepairAgentName
                                        FROM  RepairAgent 
                                        WHERE SupplierID = $supplierID 
                                        ORDER BY YEAR(LastUsedDate) DESC, MONTH(LastUsedDate) DESC, DAY(LastUsedDate) DESC;
                                        ")
                    or die("<h2> Invaild query for class RepairAgentPanel - getRepairAgentSQL <h2>");

                // loop through repair agent results and create repair agent options
                while ($row = mysql_fetch_array($result))
                {
                    if ($this->repairAgentID != $row['RepairAgentID'])
                    {
                        $selectRepairAgentHtml .= " <option value=\"".$row['RepairAgentID']."\" id=\"repairagent-option-".$row['RepairAgentID']."\">" .$row['RepairAgentName']. " </option>";
                    } 
                }
            }
            else // if no repair agent number exist for product leave blank
            {
                //$selectRepairAgentHtml = "<option value=\"no-repairagents\" id=\"option-blank\"></option>";
                $selectRepairAgentHtml = "";
            }

                return $selectRepairAgentHtml; // return the select html
        }
        
        // retrieves the repair agents detail from database and stores it into global
        // queryResult array
        private function getRepairAgentDetailsSQL($claimID, $claimType)
        {   
            if ($claimType == 'Warranty' || $claimType == 'Non-Warranty')
            {
                // check the repair to see if repair agent has already been assigned, if so get the id
                $result = mysql_query("
                                        SELECT RepairAgentID, SupplierID
                                        FROM `RepairClaim_ModifyClaimPage` 
                                        WHERE claimID = $claimID;"
                                     )
                    or die("<h2> Error - Invaild query, check class RepairAgentPanel - getRepairAgentDetailsSQL().</h2>");
                
                $array = mysql_fetch_array($result);
                
                // store both repair agent id and supplier id to use next for queries
                $this->repairAgentID = $array['RepairAgentID'];
                $supplierID = $array['SupplierID'];
                
                // if repair agentID is null as it hasnt been assigned yet, then load a repair agent that was used last for this supplier
                if ($this->repairAgentID == null)
                {
                    // select the repair agent with the last used date
                    $result = mysql_query( "SELECT *
                                            FROM  RepairAgent
                                            WHERE SupplierID = $supplierID 
                                            ORDER BY YEAR(LastUsedDate) DESC, MONTH(LastUsedDate) DESC, DAY(LastUsedDate) DESC
                                            LIMIT 1;
                                            ")
                        or die("<h2> Error - Invaild query, check class RepairAgentPanel - getRepairAgentDetailsSQL() - Get Repair Agent Details by supplierID and date last used.</h2>");
                    
                }
                else // use repair agent ID to load its details
                {
                    $result = mysql_query(" SELECT *
                                            FROM RepairAgent 
                                            WHERE RepairAgentID = $this->repairAgentID;
                                            ")
                        or die ("<h2> Error - Invaild query, check class RepairAgentPanel - getRepairAgentDetailsSQL() - Get Repair Agent Details by RepairAgentID.</h2>");
                    
                    //$this->queryResult = $result; // assign the results to the global results variable
                }
                
                // assign the repair agent details to the global variables
                $repairAgent = mysql_fetch_array($result);
                
                $this->repairAgentID = $repairAgent['RepairAgentID'];
                $this->name = $repairAgent['RepairAgentName'];
                $this->phone = $repairAgent['RepairAgentPhone'];
                $this->email = $repairAgent['RepairAgentEmail'];
                $this->unitHouse = $repairAgent['UnitHouseNumber'];
                $this->street= $repairAgent['Street'];
                $this->suburbCity = $repairAgent['SuburbCity'];
                $this->state = $repairAgent['State'];
                $this->postcode = $repairAgent['Postcode'];
                $this->supplierID = $repairAgent['SupplierID'];  
            }  
        }
        
        // Description: remove the repair agent from the database
        // INPUT: repair agent id
        // OUTPUT: Json object -> Success Message
        public function removeRepairAgent($repairAgentID)
        {
            // check vaild claim id
            // check to make sure vaild claim type is being entered - stop injection
            if (preg_match($this->repairAgentIDRegex, $repairAgentID) == true)
            {
                $query = mysql_query("   DELETE FROM RepairAgent
                                WHERE repairAgentID = $repairAgentID");
                
                // send back json object with success or fail
                if (mysql_affected_rows()  > 0 ) // if 1 or more then was repair agent was deleted
                {   
                    $array = array("response" => "removed");
                    echo json_encode($array);
                }
                else // if query failed send error message back so javascript can display error
                {
                    $array = array("response" => "not-found");
                    echo json_encode($array);
                }
            }
            else
            {
                $array = array("response" => "invaild-repairid");
                echo json_encode($array);
            }
        }
        
        // Description: retrives repair agents details from database
        // Input: Repair Agent ID number
        // Output: Json object -> success message and repair agent details
        public function getRepairAgentDetailsJSON($repairAgentID)
        {
            // check vaild claim id
            // check to make sure vaild claim type is being entered - stop injection
            if (preg_match($this->repairAgentIDRegex, $repairAgentID) == true)
            {
                $query = mysql_query("  SELECT *
                                        FROM RepairAgent
                                        WHERE repairAgentID = $repairAgentID");
                
                // send back json object with success or fail
                if (mysql_num_rows($query) > 0) // if number rows greater zero then found
                {
                    $result = mysql_fetch_array($query);
                    
                    $array = array("response" => "found", "repairAgentID" => $repairAgentID, "name" => $result['RepairAgentName'], "phone" => $result['RepairAgentPhone'], "email" => $result['RepairAgentEmail'], "unitHouseNumber" => $result['UnitHouseNumber'], "street" => $result['Street'], "suburbCity" => $result['SuburbCity'], "state" => $result['State'], "postcode" => $result['Postcode']);
                    
                    echo json_encode($array);
                }
                else // if query failed send error message back so javascript can display error
                {
                    $array = array("response" => "not-found");
                    echo json_encode($array);
                }
            }
            else
            {
                $array = array("response" => "invaild-repairagent-id");
                echo json_encode($array);
            }
        }
        
        
         // description: Checks inputs data is vaild and then inserts the repair agents details to database
        // input:       Name, Phone, Email, UnitHouseNumber, Street, SuburbCity, State, Postcode, SupplierID
        public function addRepairAgentDatabase($name, $phone, $email, $unitHouseNumber, $street, $suburbCity, $state, $postcode, $supplierID, $claimID)
        {
            $this->setName($name);
            $this->setPhone($phone);
            $this->setEmail($email);
            $this->setUnitHouseNumber($unitHouseNumber);
            $this->setStreet($street);
            $this->setSuburbCity($suburbCity);
            $this->setState($state);
            $this->setPostcode($postcode);
            $this->setSupplierID($supplierID);
            
            // get current date to store in database
            $currentDate = date("Y/m/d");
            
            // insert global variables
            $result = mysql_query(" INSERT INTO RepairAgent (RepairAgentName, RepairAgentPhone, RepairAgentEmail, 
                                    UnitHouseNumber, Street, SuburbCity, State, Postcode, LastUsedDate, SupplierID)
                                    VALUES ('$this->name', '$this->phone', '$this->email', '$this->unitHouse', '$this->street', '$this->suburbCity', '$this->state', '$this->postcode', '$currentDate', $supplierID);")
                                    or 
                        die("<h2> Error - Class RepairAgent - addRepairAgentDatabase() - Query Failed Add Repair Agent.");
            
            if (mysql_affected_rows()  == 0 )
            {
                die("<h2> Error - Could not add the new Repair Agent to the database, please try again later. </h2>");
            }
            
            // add the new repair agents ID to the repair claim
            $repairAgentID = mysql_insert_id();
            
            $result = mysql_query(" UPDATE RepairClaim
                                    SET RepairAgentID = $repairAgentID
                                    WHERE ClaimID = $claimID;")
                                    or 
                        die("<h2> Error - Class RepairAgent - addRepairAgentDatabase() - Query Failed Insert RepairAgentID.");
        }
        
        
        // Description: Updates the repair agents name in the database
        // Inputs:      Name
        public function updateRepairAgentDatabase($repairAgentID, $name, $phone, $email, $unitHouseNumber, $street, $suburbCity, $state, $postcode, $claimID)
        {
            $this->setName($name);
            $this->setPhone($phone);
            $this->setEmail($email);
            $this->setUnitHouseNumber($unitHouseNumber);
            $this->setStreet($street);
            $this->setSuburbCity($suburbCity);
            $this->setState($state);
            $this->setPostcode($postcode);
            
            // get current date to store in database
            $currentDate = date("Y/m/d");
            
            $result = mysql_query(" UPDATE RepairAgent
                                    SET RepairAgentName = '$this->name', 
                                    RepairAgentPhone =  '$this->phone', 
                                    RepairAgentEmail = '$this->email', 
                                    UnitHouseNumber = '$this->unitHouse',      
                                    Street = '$this->street', 
                                    SuburbCity = '$this->suburbCity', 
                                    State = '$this->state', 
                                    Postcode = '$this->postcode', 
                                    LastUsedDate = '$currentDate',
                                    WHERE RepairAgentID = $repairAgentID;
                                    ")
                                    or 
                        die("<h2> Error - Class RepairAgent - updateRepairAgentNameDatabase() - Query Failed Update Repair Agent.");
            
            if (mysql_affected_rows()  == 0 )
            {
                die("<h2> Error - Could NOT update the Repair Agent in the database, please try again later. </h2>");
            }
            
            // set the repair claims repair agent
            $result = mysql_query(" UPDATE RepairClaim
                                    SET RepairAgentID = $repairAgentID
                                    WHERE ClaimID = $claimID;")
                                    or 
                        die("<h2> Error - Class RepairAgent - addRepairAgentDatabase() - Query Failed Insert RepairAgentID.");
        }
        
        
        // checks for vaild supplierID - if invaild displays error
        private function setSupplierID($supplierID)
        {
            if(preg_match($this->supplierIDRegex, $supplierID) == true)
            {
                $this->supplierID = $supplierID;
            }
            else
            {
                die ('<h2> Error - Invaild Supplier ID - Must contain only numbers and be between 1-20 characters in length.');
            }
        }
        
        
        // checks for vaild first name and sets the global variable
        private function setName($name)
        {
            // regex checks to make sure string is between 2-15 characters and only contains letters and white spaces
            if(preg_match($this->nameRegex, $name) == true)
            {
                $this->name = $name;
            }
            else{
                die("<h2> Error - Invaild Name - Must be between 2 and 40 characters in length and contain only letters and white spaces");
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
                $this->unitHouse = $unitHouseNumber;
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
        
        // checks for vaild suburb/City and sets the global variable
        private function setSuburbCity($suburbCity)
        {
            // regex checks to make sure string is between 2-70 characters and only contains letters and white spaces
            if(preg_match($this->suburbCityRegex, $suburbCity) == true)
            {
                $this->suburbCity = $suburbCity;
            }
            else{
                die("<h2> Error - Invaild Suburb/City Name - Must be between 2 and 70 characters in length and contain only letters and white spaces");
            }
        }
        
        // checks for vaild state and sets the global variable
        private function setState($state)
        {
            
            // regex checks to make sure string is between 2-40 characters and only contains letters and white spaces
            if(preg_match($this->stateRegex, $state) == true)
            {
                $this->state = $state;
            }
            else{
                die("<h2> Error - Invaild State Name - Must be between 1 and 40 characters in length and contain only letters and white spaces");
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
        
         // removes any spaces from numbers to keep formatting correct
        private function formatNumber($number)
        {
            $formatedNumber = preg_replace("/\s/", "", $number);
            return $formatedNumber;
        }
        
        // Description: Generates the html required for a Repair Agent Panel and returns it as a string
        // input: none
        // output: HTML:String
        private function generateRepairAgentPanelHTML()
        {
            if ($this->claimType == 'Warranty' || $this->claimType == 'Non-Warranty')
            {
                    $repairAgentOptionsHTML = $this->generateRepairAgentOptionsHTML($this->supplierID);
                
                    return $html = "
                    <fieldset>
                        <legend>Nominated Repair Agent</legend>
                        
                        <div class=\"input-wrapper\">
                            <div class=\"left-wrapper\">
                                <label>Select Repair Agent: </label>
                                <select id=\"claimdetails-repairagent-select\">
                                    $repairAgentOptionsHTML
                                </select>
                            </div>

                            <div class=\"right-wrapper\">

                                <button text=\"Search\" type=\"button\" class=\"claimdetails-repairagent-button\" id=\"claimdetails-repairagent-delete-button\"> Delete </button>
                                <button text=\"Search\" type=\"button\" class=\"claimdetails-repairagent-button\" id=\"claimdetails-repairagent-update-button\"> Update </button>
                                <button text=\"Search\" type=\"button\" class=\"claimdetails-repairagent-button\" id=\"claimdetails-repairagent-new-button\">New</button>
                            </div>
                
                        </div>

                        <div class=\"line\" id=\"claimdetails-repairagent-line\"></div>
            
                        
                        <div class=\"input-wrapper\">
                            <div class=\"left-wrapper\">
                                <label>Repair Agent ID: </label>
                                <input type=\"text\" class=\"input-text-fifty\" name=\"claimdetails-repairagentid-input\" 
                                       id=\"claimdetails-repairagentid-input\" value=\"$this->repairAgentID\" disabled>
                            </div>

                            <div class=\"right-wrapper\">
                                <label>Repair Agent Name: </label>
                                <input type=\"text\" class=\"input-text-fifty\" name=\"claimdetails-repairagentname-input\" 
                                       id=\"claimdetails-repairagentname-input\"  value=\"$this->name\" disabled>
                            </div>
                        </div>

                        <div class=\"input-wrapper\">
                            <div class=\"left-wrapper\">
                                <label>Email: </label>
                                <input type=\"text\" class=\"input-text-fifty\" name=\"claimdetails-repairagentemail-input\"
                                       id=\"claimdetails-repairagentemail-input\" value=\"$this->email\" disabled>
                            </div>
                            <div class=\"right-wrapper\">
                                <label>Phone: </label>
                                <input type=\"text\" class=\"input-text-fifty\" name=\"claimdetails-repairagentphone-input\"
                                       id=\"claimdetails-repairagentphone-input\"  value=\"$this->phone\" disabled>
                            </div>
                        </div>
                        
                        <div class=\"input-wrapper\">
                                <div class=\"left-wrapper\">
                                    <label>Unit/House Number: </label>
                                    <input type=\"text\" class=\"input-text-fifty\" name=\"claimdetails-repairagentunithouse-input\" id=\"claimdetails-repairagentunithouse-input\" value=\"$this->unitHouse\">
                                </div>

                                <div class=\"right-wrapper\">
                                    <label>Street: </label>
                                    <input type=\"text\" class=\"input-text-fifty\" name=\"claimdetails-repairagentstreet-input\" id=\"claimdetails-repairagentstreet-input\" value=\"$this->street\">
                                </div>
                            </div>

                            <div class=\"input-wrapper\">
                                <div class=\"left-wrapper\">
                                    <label>City/Suburb: </label>
                                    <input type=\"text\" class=\"input-text-fifty\" name=\"claimdetails-repairagentsuburbcity-input\" id=\"claimdetails-repairagentsuburbcity-input\" value=\"$this->suburbCity\">
                                </div>

                                <div class=\"right-wrapper\">
                                    <label>State: </label>
                                    <input type=\"text\" class=\"input-text-fifty\" name=\"claimdetails-repairagentstate-input\" id=\"claimdetails-repairagentstate-input\" value=\"$this->state\">
                                </div>
                            </div>

                            <div class=\"input-wrapper\">
                                <div class=\"left-wrapper\">
                                    <label>Postcode: </label>
                                    <input type=\"text\" class=\"input-text-fifty\" name=\"claimdetails-repairagentpostcode-input\" id=\"claimdetails-repairagentpostcode-input\" value=\"$this->postcode\">
                                </div>
                            </div>
                            
                            <input type=\"text\" style =\"display:none;\" class=\"input-text-fifty\" name=\"claimdetails-repairagenthidden-input\" id=\"claimdetails-repairagenthidden-input\" value=\"no-action\">
                            
                    </fieldset>
                                ";
            }
        }
    }
?>