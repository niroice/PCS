<?php
    include("ClaimsListPanel.php");
    
    // if claimID not empty search by claimID
     if(isset($_POST['claimID']) || !empty($_POST['claimID']))
    {
        include("DatabaseConnection.php");
        
        // create database connection again for new query
        $dbConnection2 = new DatabaseConnection();
         
        $claimsList = new ClaimsListPanel();
        $claimsList->searchByClaimID($_POST['claimID']);
    }
    // if view by dropdown was posted search by view by and sort by drop downs
    else if(!empty($_POST['ViewClaimBy']))
    {
        include("DatabaseConnection.php");
        
        // create database connection again for new query
        $dbConnection2 = new DatabaseConnection();
        
        $claimsList = new ClaimsListPanel();
        $claimsList->searchByDropDown($_POST['ViewClaimBy'], $_POST['SortClaimBy']);
    }
    else // if no post exist set as default - all oustanding claims
    {
        $claimsList = new ClaimsListPanel();
        $claimsList->searchByDropDown("outstanding-claims", "date");
    }
    
?>