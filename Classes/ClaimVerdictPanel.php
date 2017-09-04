<?php

    class ClaimVerdictPanel
    {
        private $numberPendingClaims;
        private $NOTIFICATION = " CLAIMS STILL PENDING"; 
        private $numberRegex = '/^[0-9]{1,20}$/';
        
        // possible queries for view by options
        private $outstandingClaimsQuery = " SELECT COUNT(ClaimID) as 'number'
                                            FROM AllClaims_ViewClaimsPage
                                            WHERE ClaimStatus != 'Cancelled'
                                            AND ClaimStatus != 'Complete';";
        
        public function __construct()
        {          
            $this->setPendingClaimSQL();
            $this->printHTML();
        }
        
        private function setPendingClaimSQL()
        {
            $result = mysql_query($this->outstandingClaimsQuery)
                or
                die ("<h2> Error - Query failed for finding number of oustanding claims in Class ClaimVerdictPanel</h2>");
                
            $row = mysql_fetch_array($result);
            
            $this->$numberPendingClaims = $row['number'];
        }
        
        // result will be displayed by claimID
        private function printHTML()
        {
           echo "
                <div id="yes-pending-container">
                    <h1> $this->$numberPendingClaims $NOTIFICATION</h1>
                </div>
           ";  
        }
    }
?>