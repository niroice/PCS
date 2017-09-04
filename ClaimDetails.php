<?php

    error_reporting(E_ALL ^ E_DEPRECATED);

    include("Classes/UserControl.php");
    // create user control object
    $userControl = new UserControl();

    // always check to see if the user is logged in, if NOT redirect page to the login page
    if ($userControl->loginCheck() == false)
    {
        header('location: Login.php');
    }

    include("Classes/DatabaseConnection.php");
    include("Classes/ClaimProductPanel.php");
    include("Classes/ClaimDetailsPanel.php");
    include("Classes/SupplierDetailsPanel.php");
    include("Classes/RepairAgentPanel.php");
    include("Classes/CustomerDetailsPanel.php");
    include("Classes/RepairOptionsPanel.php");
    include("Classes/FinaliseRepairPanel.php");
    include("Classes/RepairClaim.php");
    include("Classes/ReturnClaim.php");
    include("Classes/FinancialClaim.php");
    
    // create database connection
    $dbConnection = new DatabaseConnection();
    
    if (isset($_GET['ClaimID']) && isset($_GET['ClaimType']))
    {
        $claimID = $_GET['ClaimID'];
        $claimType = $_GET['ClaimType'];
        
        // save the claimID to the session variable
        $_SESSION['ClaimID'] = $claimID;
        
        // prints the page header to the screen
        include("HeadersFooters/ClaimDetailsHeaderExistingClaim.php");

        $newClaimDetailsPanel = new ClaimDetailsPanel();
        $newClaimDetailsPanel->loadClaim($claimID, $claimType);

        $newClaimProductPanel = new ClaimProductPanel();
        $newClaimProductPanel->loadClaim($claimID, $claimType);

        $newSupplierDetailsPanel = new SupplierDetailsPanel;
        $newSupplierDetailsPanel->loadClaim($claimID, $claimType);


        // only create repair agent panel, customer anel and finalise repair panel
        // if the claim is a repair type - warranty or non-warranty
        if ($claimType == 'Warranty' || $claimType == 'Non-Warranty')
        {
            $newRepairAgentPanel = new RepairAgentPanel();
            $newRepairAgentPanel->printRepairAgentPanel($claimID, $claimType);

            $newCustomerPanel = new CustomerDetailsPanel();
            $newCustomerPanel->loadExistingCustomer($claimID);
            
            $newRepairOptionsPanel = new RepairOptionsPanel();
            $newRepairOptionsPanel->loadClaim($claimID);

            $newFinaliseRepairPanel = new FinaliseRepairPanel();
            $newFinaliseRepairPanel->setRepairDetailsSQL($claimID);
            $newFinaliseRepairPanel->createFinaliseRepairFields();
        }
        
        // prints the page footer to the screen
        include("HeadersFooters/ClaimDetailsFooterExistingClaim.php");

    }
    // if add product is set and keycode provided get product information and print
    // to the screen
    else if (isset($_POST['action']) && $_POST['action'] == "addproduct")
    {
        
        $keycode = $_POST['keycode'];
        $quantity = $_POST['quantity'];

        $newClaimProductPanel = new ClaimProductPanel();
        $newClaimProductPanel->addProductSession($keycode, $quantity);
    }
    else if (isset($_POST['action']) && $_POST['action'] == "removeproduct")
    {
        $keycode = $_POST['keycode'];

        $newClaimProductPanel = new ClaimProductPanel();
        $newClaimProductPanel->removeProductSession($keycode);
    }
    else if (isset($_POST['action']) && $_POST['action'] == "updateproduct")
    {
        $keycode = $_POST['keycode'];
        $quantity = $_POST['quantity'];

        $newClaimProductPanel = new ClaimProductPanel();
        $newClaimProductPanel->updateProductSession($keycode, $quantity );
    }
    else if (isset($_POST['action']) && $_POST['action'] == "get-repairagent-details")
    {
        $repairAgentID = $_POST['repairAgentID'];

        $newRepairAgentPanel = new RepairAgentPanel();
        $newRepairAgentPanel->getRepairAgentDetailsJSON($repairAgentID);
    }
    else if (isset($_POST['action']) && $_POST['action'] == "remove-repairagent")
    {
        $repairAgentID = $_POST['repairAgentID'];

        $newRepairAgentPanel = new RepairAgentPanel();
        $newRepairAgentPanel->removeRepairAgent($repairAgentID);
    }
    else if (isset($_POST['action']) && $_POST['action'] == "change-supplier")
    {
        $keycode= $_POST['keycode'];

        $newSupplierPanel = new SupplierDetailsPanel();
        $newSupplierPanel->getSupplierDetailsJSON($keycode);
    }
    // if submit claim button was clicked
    else if (isset($_POST['claimdetails-submit-button']))
    {
        // if repair type - warrant or non-warranty 
        if ($_POST['claimdetails-claimtype-select'] == 'Warranty' || 
            $_POST['claimdetails-claimtype-select'] == 'Non-Warranty')
        {   
                $repairClaim = new RepairClaim();
                $repairClaim->updateRepairClaimSQL(
                                                    $_SESSION['ClaimID'],
                                                    $_POST['customer-firstname-input'],
                                                    $_POST['customer-lastname-input'],
                                                    $_POST['customer-phone-input'],
                                                    $_POST['customer-email-input'],
                                                    $_POST['customer-unithouse-input'],
                                                    $_POST['customer-street-input'],
                                                    $_POST['customer-suburbcity-input'],
                                                    $_POST['customer-state-input'],
                                                    $_POST['customer-postcode-input'],
                                                    $_POST['claimdetails-claimtype-select'],
                                                    $_POST['claimdetails-claimstatus-select'],
                                                    $_POST['claimdetails-ranumber-input'],
                                                    $_POST['repair-cost-input'],
                                                    $_POST['return-authorized-name-input'],
                                                    $_POST['repair-message-textarea']
                                                    );
            
            // if hidden input has "new", create a new repair agent 
            if (isset($_POST['claimdetails-repairagenthidden-input']) && 
                $_POST['claimdetails-repairagenthidden-input'] == 'new')
            {
                
                $newRepairAgent = new RepairAgentPanel();
                $newRepairAgent->addRepairAgentDatabase(
                    $_POST['claimdetails-repairagentname-input'],
                    $_POST['claimdetails-repairagentphone-input'],
                    $_POST['claimdetails-repairagentemail-input'],
                    $_POST['claimdetails-repairagentunithouse-input'],
                    $_POST['claimdetails-repairagentstreet-input'],
                    $_POST['claimdetails-repairagentsuburbcity-input'],
                    $_POST['claimdetails-repairagentstate-input'],
                    $_POST['claimdetails-repairagentpostcode-input'],
                    $_POST['claimdetails-supplierid-input'],
                    $_POST['claimdetails-claimid-input']);
            }
            else if (isset($_POST['claimdetails-repairagenthidden-input']) && 
                $_POST['claimdetails-repairagenthidden-input'] == 'update')
            {
                $newRepairAgent = new RepairAgentPanel();
                $newRepairAgent->updateRepairAgentDatabase(
                    $_POST['claimdetails-repairagentid-input'],
                    $_POST['claimdetails-repairagentname-input'],
                    $_POST['claimdetails-repairagentphone-input'],
                    $_POST['claimdetails-repairagentemail-input'],
                    $_POST['claimdetails-repairagentunithouse-input'],
                    $_POST['claimdetails-repairagentstreet-input'],
                    $_POST['claimdetails-repairagentsuburbcity-input'],
                    $_POST['claimdetails-repairagentstate-input'],
                    $_POST['claimdetails-repairagentpostcode-input'],
                    $_SESSION['ClaimID']);
            }
            
            
            // if print repair order form was select, go to redirect page
            // that generates repair order form in new window and loads the main page
            if (isset($_POST['print-checkbox']) || $_POST['print-checkbox'] == "print-checkbox" )
            {
                $claimID = $_SESSION['ClaimID'];
                
                // redirect the page the repair form page using claim id
                header("location: RepairOrderForm.php?claimID=$claimID");
            }
            else
            {
                // once the claim is redirect the page to view claims page
                header("Location: ViewClaims.php");
            }
            
            
        }
        // if claim type is existing and a return create return object
        // set as existing and update claim details in database
        else if ($_POST['claimdetails-claimtype-select'] == 'Return')
        {   
                $returnClaim = new ReturnClaim();
            
                $returnClaim->setAsExistingClaim(   $_SESSION['ClaimID'],
                                                    $_POST['claimdetails-claimstatus-select'], 
                                                    $_POST['claimdetails-ranumber-input'],
                                                    $_POST['claimdetails-claimamount-input']
                                                );

                // loop through session 
                // get length of session array
                $arrayLength = count($_SESSION['ProductsInClaim']);

                // loop throught products in claim array stored in session variable
                for ($i = 0; $i < $arrayLength; $i++)
                {
                    $returnClaim->addProduct($_SESSION['ProductsInClaim'][$i]['keycode'],
                                             $_SESSION['ProductsInClaim'][$i]['quantity']);
                }

                $returnClaim->updateClaimDatabaseSQL();

            // once the claim is redirect the page to view claims page
            header("Location: ViewClaims.php");
        }
        // if claim type is existing and a Finanical create a finanical object
        // set as existing and update claim details in database
        else if ($_POST['claimdetails-claimtype-select'] == 'Financial')
        {
                $financialClaim = new FinancialClaim();
            
                $financialClaim->setAsExistingClaim(    $_SESSION['ClaimID'],
                                                        $_POST['claimdetails-claimstatus-select'],
                                                        $_POST['claimdetails-claimamount-input']
                                                );

                // loop through session 
                // get length of session array
                $arrayLength = count($_SESSION['ProductsInClaim']);

                // loop throught products in claim array stored in session variable
                for ($i = 0; $i < $arrayLength; $i++)
                {
                    $financialClaim->addProduct(    $_SESSION['ProductsInClaim'][$i]['keycode'],
                                                    $_SESSION['ProductsInClaim'][$i]['quantity']);
                }

                $financialClaim->updateClaimDatabaseSQL();

            // once the claim is redirect the page to view claims page
            header("Location: ViewClaims.php");
        }
    }
    // if new claim submit button is pressed create a new claim based on type - return or finanical
    else if (isset($_POST['claimdetails-newsubmit-button']))
    {
        if ($_POST['claimdetails-claimtype-select'] == 'Financial')
        {
            $financialClaim = new FinancialClaim();
            
            $financialClaim->setAsNewClaim(     $_POST['claimdetails-claimstatus-select'],
                                                $_POST['claimdetails-claimamount-input']
                                            );

            // loop through session 
            // get length of session array
            $arrayLength = count($_SESSION['ProductsInClaim']);

            // loop throught products in claim array stored in session variable
            for ($i = 0; $i < $arrayLength; $i++)
            {
                $financialClaim->addProduct(    $_SESSION['ProductsInClaim'][$i]['keycode'],
                                                $_SESSION['ProductsInClaim'][$i]['quantity']);
            }

            $financialClaim->addClaimDatabaseSQL();

            // once the claim is redirect the page to view claims page
            header("Location: MainMenu.php");
        }
        else if ($_POST['claimdetails-claimtype-select'] == 'Return')
        {
            $returnClaim = new ReturnClaim();
            
            $returnClaim->setAsNewClaim(        $_POST['claimdetails-claimstatus-select'],
                                                $_POST['claimdetails-ranumber-input'],
                                                $_POST['claimdetails-claimamount-input']
                                            );

            // loop through session 
            // get length of session array
            $arrayLength = count($_SESSION['ProductsInClaim']);

            // loop throught products in claim array stored in session variable
            for ($i = 0; $i < $arrayLength; $i++)
            {
                $returnClaim->addProduct(   $_SESSION['ProductsInClaim'][$i]['keycode'],
                                            $_SESSION['ProductsInClaim'][$i]['quantity']);
            }

            $returnClaim->addClaimDatabaseSQL();

            // once the claim is redirect the page to view claims page
            header("Location: MainMenu.php");
        }
    }
    // if cancel new claim button was clicked, dont save and load the main menu page
    else if (isset($_POST['claimdetails-newcancel-button']))
    {
        // once the claim is redirect the page to view claims page
            header("Location: MainMenu.php");
    }
    // if cancel claim button was clicked, dont save and reload view claims page
    else if (isset($_POST['claimdetails-cancel-button']))
    {
        // once the claim is redirect the page to view claims page
            header("Location: ViewClaims.php");
    }
    else // if new claim setup as return claim (default) - fields blank
    {
        // prints the page header to the screen
        include("HeadersFooters/ClaimDetailsHeaderNewClaim.php");
        
        $newClaimDetailsPanel = new ClaimDetailsPanel();
        $newClaimDetailsPanel->startNewReturnClaim();

        $newClaimProductPanel = new ClaimProductPanel();
        $newClaimProductPanel->startNewClaim();

        $newSupplierDetailsPanel = new SupplierDetailsPanel;
        $newSupplierDetailsPanel->startNewClaim();
        
        // prints the page footer to the screen
        include("HeadersFooters/ClaimDetailsFooterNewClaim.php");
    }


?>
       
    
        