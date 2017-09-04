<?php 
    include("Classes/UserControl.php");
    // create user control object
    $userControl = new UserControl();

    // always check to see if the user is logged in, if NOT redirect page to the login page
    if ($userControl->loginCheck() == false)
    {
        header('location: Login.php');
    }

    // included classess
    include("Classes/DatabaseConnection.php");
    include("Classes/SearchPanel.php");
    include("Classes/RepairClaim.php");
    include("Classes/FinancialClaim.php");


        // create database connection
        $dbConnection = new DatabaseConnection();

        

        // always print the page header to the screen
        include("HeadersFooters/CustomerClaimHeader.php");

        // if submit claim button was clicked process claim
        if(!empty($_POST['submit-claim-button']))
        {
            $claimStatus = "Created"; // default claim status starts at created
            $repairCost;

            // if repair claim type create claim object to save claim to database, save claim id and 
            // print repair form
            if( $_POST['claimtype-input-hidden'] == "repair-warranty" || 
                $_POST['claimtype-input-hidden'] == "repair-no-warranty" ||
                $_POST['claimtype-input-hidden'] == "repair-warranty" ||
                $_POST['claimtype-input-hidden'] == "repair-no-transaction")
            {
                // check to see if ra-check box was ticked; which means the RA was sent - if so
                // change the claim status to RA Requested
                if(isset($_POST['ra-checkbox']) && $_POST['ra-checkbox'] == "ra-checkbox" )
                {
                    $claimStatus = "RA Requested";
                }
                
                // check repair cost input, if null set to zero
                if ($_POST['repair-cost-input'] == null){
                    $repairCost = 0;
                }
                else{
                    $repairCost = $_POST['repair-cost-input'];
                }
                
                echo "<h1> repair cost =  $repairCost</h1>";

                //create a new repair claim object to save the claim to database
                $newRepairClaim = new RepairClaim();
                $newRepairClaim->addRepairClaimSQL( 
                $_POST['keycode-input-hidden'], $_POST['transactionid-input-hidden'], $_POST['repairtype-radio'], $claimStatus, $_POST['customer-firstname-input'], $_POST['customer-lastname-input'], $_POST['customer-phone-input'], $_POST['customer-email-input'], $_POST['customer-unithouse-input'], $_POST['customer-street-input'], $_POST['customer-suburbcity-input'],
                $_POST['customer-state-input'], $_POST['customer-postcode-input'], $repairCost,
                $_POST['return-authorized-name-input'], $_POST['repair-message-textarea']
                );

                // if repair was successful create new session variable to store the last claimID from repair claim object, to use in new window
                $claimID = $newRepairClaim->getClaimID();

                // if print repair order form was select, go to redirect page
                // that generates repair order form in new window and loads the main page
                if (isset($_POST['print-checkbox']) || $_POST['print-checkbox'] == "print-checkbox" )
                {
                    // redirect the page
                    header("location: RepairOrderForm.php?claimID=$claimID");
                }
                else // if no to print repair order form go to main menu page
                {
                    header("location: MainMenu.php");
                }

            }
            else if($_POST['claimtype-input-hidden'] == "financial-warranty")
            {
                $fClaim = new FinancialClaim();
                
                // always set as claim status as 'Claim Raised' for a new customer claims
                // as the supplier will be automtically be emailed.
                $fClaim->setAsNewClaim('Claim Raised', $_POST['customerclaim-productcostprice-input']);
                $fClaim->addProduct($_POST['keycode-input-hidden'], 1);
                $fClaim->addClaimDatabaseSQL();

                header("location: MainMenu.php");
            }


        }
        // if cancel button was clicked load the main menu
        else if(!empty($_POST['cancel-claim-button']))
        {
             header("location: MainMenu.php");
        }
        // if keycode from input has been posted - provide with the created of the search panel
        // as well as the transaction number
        else if(isset($_POST['keycode-input']) && !empty($_POST['keycode-input']))
        {
            // Seach Panel object
            $search = new SearchPanel($_POST['keycode-input'] ,$_POST['transactionid-input']);

        }
        // if keycode is not available it means its first time page has loaded - bring in blank strings
        else
        {
             $search = new SearchPanel(null,null);
        }
    ?>
        
    
    </div>
</body>
</html>