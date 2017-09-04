<?php

    class ClaimsListPanel
    {
        private $view; // what user wants to view from database
        private $querySQL; // main query that will be sent to database
        private $sortBy; // what the suer want to sort the results by
        private $retrievedClaims; // the results from databse query will be stored here
        
        private $financialStatus = array('Created','Claim Raised','Complete','Cancelled');
        private $repairStatus = array('Created','RA Requested','Complete','Cancelled');
        private $returnStatus = array('Created','Shipped','Complete','Cancelled');
        
        // possible queries for view by options
        private $outstandingClaimsQuery = " SELECT * 
                                            FROM AllClaims_ViewClaimsPage
                                            WHERE ClaimStatus != 'Cancelled'
                                            AND ClaimStatus != 'Complete'";
        
        private $allClaimsQuery =   "SELECT * 
                                    FROM AllClaims_ViewClaimsPage";
        
        private $repairClaimsQuery = "Select *
                                    From RepairClaim_ViewClaimsPage";
        
        private $returnClaimQuery = "SELECT *
                                    FROM ReturnClaim_ViewClaimsPage";
        
        private $financialClaimQuery = "SELECT *
                                        FROM FinancialClaim_ViewClaimsPage";
        
        private $claimIDQuery = "SELECT *
                                FROM AllClaims_ViewClaimsPage
                                WHERE ClaimID = ";
        
        // order by query/options for the main queries above
        private $statusSortby = "   ORDER BY CASE 
                                        WHEN ClaimStatus = \"Created\" THEN 0
                                        WHEN ClaimStatus = \"RA Requested\" THEN 1
                                        WHEN ClaimStatus = \"Shipped\" THEN 2
                                        WHEN ClaimStatus = \"Claim Raised\" THEN 3
                                        WHEN ClaimStatus = \"Complete\" THEN 4
                                        WHEN ClaimStatus = \"Cancelled\" THEN 5
                                    END
                                    LIMIT 0,900000;";
        
        private $dateSortby = "ORDER BY CreatedDate DESC LIMIT 0,900000;";
        
        private $nameSortby = "ORDER BY Name, CreatedDate LIMIT 0,900000;";
        
        private $claimTypeSortby = "ORDER BY ClaimType, Name, CreatedDate LIMIT 0,900000;";
        
        private $claimNumberDescSortby = "ORDER BY ClaimID DESC LIMIT 0,900000;";
        
        private $claimIDRegex = '/^[0-9]{1,20}$/';
        
        public function __construct()
        {          
            // do nothing
        }
        
        // result will be displayed by claimID
        public function searchByClaimID($claimID)
        {
            $fullQuery = $this->claimIDQuery . ' ' . $claimID . ';';
            
            // check to make sure a vaild keycode type has been provided - stop sql inject
            if (preg_match($this->claimIDRegex, $claimID) == true)
            {
                $this->retrievedClaims = mysql_query($fullQuery)
                 or
                 die ("<div class=\"input-wrapper\" id=\"notfound-claimlist\"> Error - There was a problem with the query.</div>");
            
                // check to make sure there is claim ID - if not display message
                if(mysql_num_rows($this->retrievedClaims) == 0)
                {
                    echo "<div class=\"input-wrapper\" id=\"notfound-claimlist\"> Zero Results found for ClaimID '$claimID' </div>";
                }
                else // claim found print to screen
                {   
                    // print the claim to the screen
                    $this->printHTML();
                }
            
            }
            else
            {
                echo "<div class=\"input-wrapper\" id=\"notfound-claimlist\">
                Invaild Claim ID '$claimID'. It must contain numbers only.</div>";
            }
            
        }
        
        // allows results to be displayed by dropdown conditions
        public function searchByDropDown($view, $sortBy)
        {
            // to create query correctly setView() should always run first before setViewBy
            $this->setView($view);
            $this->setViewBy($sortBy);
            
            // create full query and get results from database
            $this->getClaimsSQL();
            
            // print the claims to the screen
            $this->printHTML();
        }
        
        // checks correct view by was given a
        private function setView($view)
        {
            switch($view)
            {
                case "outstanding-claims":
                    $this->querySQL .= $this->outstandingClaimsQuery;
                    break;
                case "all-claims":
                    $this->querySQL .= $this->allClaimsQuery;
                    break;
                case "repair-claims":
                    $this->querySQL .= $this->repairClaimsQuery;
                    break;
                case "return-claims":
                    $this->querySQL .= $this->returnClaimQuery;
                    break;
                case "finacial-claims":
                    $this->querySQL .= $this->financialClaimQuery;
                    break;
                default: // if no vaild claim type provided produce an error
                    die("<h2> Error - Invaild View By Type - must contain a vaild claim type; ethier 'outstanding-claims' or 'all-claims' or 'repair-claims' or 'return-claims' or 'finacial-claims'.</h2>");
            }
        }
        
        // checks correct sort by was given a
        private function setViewBy($sortBy)
        {
            // add a space before adding order part of query
            $this->querySQL .= ' ';
                
            switch($sortBy)
            {
                case "status":
                    $this->querySQL .= $this->statusSortby;
                    break;
                case "date":
                    $this->querySQL .= $this->dateSortby;
                    break;
                case "name":
                    $this->querySQL .= $this->nameSortby;
                    break;
                case "type":
                    $this->querySQL .= $this->claimTypeSortby;
                    break;
                case "claim-number":
                    $this->querySQL .= $this->claimNumberDescSortby;
                    break;
                default: // if a invaild sort by provided produce an error
                    die("<h2> Error - Invaild Sort By Option - must contain a vaild sort by option; ethier 'status' or 'date' or 'name' or 'type' or 'claim-number'.</h2>");
            }
        }
        
        // retrieves the required claims from the database
        private function getClaimsSQL()
        {   
            // search database and store the results
            $this->retrievedClaims = mysql_query($this->querySQL)
                or die("<h2> Error - Could not retrieve results from database - Check ClaimListPanel Class for errors in query.</h2>");
        }
        
        // creates a drop down for the claims status
        private function createStatusDropdown($claimType, $currentStatus)
        {
            $statusDropdown;
            
            // if claim type is a return, cycle through the return status's in the array and 
            // store into a variable and return the variable with method.
            if ($claimType == "Return")
            {
                // store the current claim type as first value so it appears first in the dropdown
                $statusDropdown = "<option value=\"$currentStatus\">$currentStatus</option>";
                
                //cycle through types and ignore the current claimtype
                foreach($this->returnStatus as $status)
                {
                    if($status != $currentStatus)
                    {
                        $statusDropdown .= "<option value=\"$status\">$status</option>";
                    }
                }
                return $statusDropdown; // return code required for the dropdown of status
            }
            
            // checks for type that is repair - ethier warranty or non-warranty type
            else if ($claimType == "Warranty" || $claimType == "Non-Warranty")
            {
                // store the current claim type as first value so it appears first in the dropdown
                $statusDropdown = "<option value=\"$currentStatus\">$currentStatus</option>";
                
                //cycle through types and ignore the current claimtype
                foreach($this->repairStatus as $status)
                {
                    if($status != $currentStatus)
                    {
                        $statusDropdown .= "<option value=\"$status\">$status</option>";
                    }
                }
                return $statusDropdown; // return code required for the dropdown of status
            }
            
            // checks for type that is Finanical 
            else if ($claimType == "Financial")
            {
                // store the current claim Status as first value so it appears first in the dropdown
                $statusDropdown = "<option value=\"$currentStatus\">$currentStatus</option>";
                
                //cycle through types and ignore the current claimtype
                foreach($this->financialStatus as $status)
                {
                    if($status != $currentStatus)
                    {
                        $statusDropdown .= "<option value=\"$status\">$status</option>";
                    }
                }
                return $statusDropdown; // return code required for the dropdown of status
            }
            else
            {
                die("<h2> Error - Could not create claim status dropdown due to claim type not found - Check ClaimListPanel Class, createStatusDropdown().</h2>");
            }
        }
        
        // prints the selected claims to the screen
        private function printHTML()
        {
            // loop through each claim in the results returned and print to the screen
            while($row = mysql_fetch_array($this->retrievedClaims))
            {   
                // store all the value into variables
                $claimID =  $row['ClaimID'];
                $claimType = $row['ClaimType'];
                $name =  $row['Name'];
                $date = date("d/m/Y", strtotime($row['CreatedDate']));
                $status = $row['ClaimStatus'];
                
                // generate status drop down required and return as string
                $statusDropdown = $this->createStatusDropdown($claimType, $status);

            echo "
                    <div class=\"input-wrapper\">
                    <a href=\"ClaimDetails.php?ClaimID=$claimID&ClaimType=$claimType\" class=\"claim-link\" id=\"$claimID\" >
                        <div class=\"claim-number-container\">$claimID</div>
                        <div class=\"claim-type-container\">$claimType</div>
                        <div class=\"supplier-customer-container\">$name</div>
                        <div class=\"date-container\">$date</div>
                    </a>
                    <div class=\"claim-status-container\">
                        <select class=\"claim-status-select\" data-claimtype=\"$claimType\" id=\"select-$claimID\">
                            $statusDropdown
                        </select>
                    </div>
                </div>
                ";
            }
        }
        
    
    }
?>