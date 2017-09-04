<?php
    include("DatabaseConnection.php");

    // create database connection
    $dbConnection = new DatabaseConnection();

    // create new process claim object
    $newProccessClaimStatus = new ProcessClaimStatus();
    
    // check to see if post is not empty
    if(!empty($_POST['StatusArray']))
    {
        $numberClaims = count($_POST['StatusArray']);
        
        for ($i = 0; $i < $numberClaims; $i++)
        {
            $claimID = $_POST['StatusArray'][$i][0];
            $claimStatus = $_POST['StatusArray'][$i][1];
            $claimType = $_POST['StatusArray'][$i][2];
            echo "<h2> claimid - $claimID </h2>";
            echo "<h2> status -  $claimStatus</h2>";
            echo "<h2> claimtype - $claimType </h2>";
            $newProccessClaimStatus->updateClaimStatusSQL($claimID, $claimStatus, $claimType);
                                               
            echo "<h2> object created </h2>";
        }
    }
    

    class ProcessClaimStatus
    {
        private $claimIDRegex = '/^[0-9]{1,20}$/';
        private $statusRegex = '/^[a-zA-Z\s]{1,20}$/';
        private $typeRegex = '/^[a-zA-Z\s]{1,20}$/';
        
        public function __construct()
        {          
            // do nothing
        }
        
        // updates the claim status provded to the database
        public function updateClaimStatusSQL($claimID, $status, $type)
        {
            // check to make sure vaild types are provided
            $this->checkClaimID($claimID);
            $this->checkStatus($status);
            $this->checkType($type);
            
            if($type == 'Return')
            {
                // claimID can exist in three different tables, try each tabke
                // unitl found - false means it failed to insert
                $result = mysql_query(" UPDATE ReturnClaim
                                        SET ClaimStatus ='$status'
                                        WHERE ClaimID= $claimID;"
                                     )
                    or die("<h2> Error - Failed to to update the claim status, check the query in the class</h2>");

                echo "<h2> return - $result</h2>";
            }
            else if ($type == 'Financial')
            {
                $result = mysql_query(" UPDATE FinancialClaim
                                        SET ClaimStatus ='$status'
                                        WHERE ClaimID= $claimID;"
                                     )
                    or die("<h2> Error - Failed to to update the claim status, check the query in the class</h2>");
                echo "<h2> Financial - $result</h2>";
            }
            else if ($type == 'Warranty' || $type == 'Non-Warranty')
            {
                $result = mysql_query(" UPDATE RepairClaim
                                    SET ClaimStatus ='$status'
                                    WHERE ClaimID= $claimID;"
                                    )
                    or die("<h2> Error - Failed to to update the claim status, check the query in the class</h2>");
                echo "<h2> repair - $result</h2>";
            }
            else
            {
                die ("<h2> Error - Invaild claim type provided for  - updateClaimStatusSQL() </h2>");
            } 
        }
        
        // check for vaild claim ID type was provided - if not stop all actions
        private function checkClaimID($claimID)
        {
           if(preg_match($this->claimIDRegex, $claimID) == false)
           {
               die ("<h2> Error - Invaild ClaimID provided for class 'ProcessClaimStatus - updateClaimStatusSQL()'. Must contain only letters and maxmium of 20 characters. </h2>");
           }
        }
        
        // check for vaild claim status type was provided - if not stop all actions
        private function checkStatus($status)
        {
           if(preg_match($this->statusRegex, $status) == false)
           {
               die ("<<h2> Error - Invaild Status provided for class 'ProcessClaimStatus - updateClaimStatusSQL()'. Must contain only letters and maxmium of 20 characters. </h2>");
           }
        }
        
        // check for vaild claim status type was provided - if not stop all actions
        private function checkType($type)
        {
            if($type == 'Warranty' || $type == 'Non-Warranty' || $type == 'Financial' || $type == 'Return' )
            {
                // do nothing
            }
            else
            {
                die ("<<h2> Error - Invaild Type provided for class 'ProcessClaimStatus - updateClaimStatusSQL()'. Must be ethier 'Warranty' or 'Non-Warranty' or 'Financial' or 'Return'.</h2>");
            }
        }
    }
?>