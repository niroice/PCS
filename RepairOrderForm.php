<?php
    error_reporting(E_ALL ^ E_DEPRECATED);

    // included classess
    include("Classes/DatabaseConnection.php");

    // create database connection
    $dbConnection = new DatabaseConnection();

    // load the repair form based on claim number provided by get
    if (isset($_GET['claimID']))
    {
        $newRepair = new RepairOrderForm($_GET['claimID']);
    }
    else{
        echo "<h1> Error - No Claim Number Provided</h1>";
    }

    class RepairOrderForm
    {
        private $searchResult;
        private $claimID;
        private $claimDate;
        private $claimIDRegex = "/^[0-9]{1,20}$/";

        public function __construct($claimID)
        {
            //$this->currentDate = date("d/m/Y");
            $this->setClaimID($claimID);
            
            $this->setDate();

            // print the pages html
            $this->printHTML();
        }
        
        // 
        private function setDate()
        {
                    // check to make sure the keycode provided is vaild/in database
                    $query = mysql_query(  "
                                            SELECT CreatedDate
                                            FROM Claim
                                            WHERE ClaimID = $this->claimID;
                                            ")
                        or die("<h2> Error - Invaild get claim date query");

                    $result = mysql_fetch_array($query);
                    $this->claimDate = date('d/m/y', strtotime($result['CreatedDate']));
            
                    
        }

        private function setClaimID($claimID)
        {
            if(preg_match($this->claimIDRegex, $claimID) == true)
                {
                    // check to make sure the keycode provided is vaild/in database
                    $result = mysql_query(  "
                                            SELECT FirstName, LastName, CustomerPhone, Customer.HouseNumber, Customer.Street, Customer.SuburbCity, Customer.State, Customer.Postcode, SupplierName, SupplierAddress, RepairMessage, Type, RepairCost, RepairClaim.Keycode, ProductName 
                                            FROM Customer, ProductSupplier_VIEW, RepairClaim WHERE RepairClaim.ClaimID = $claimID AND RepairClaim.Keycode = ProductSupplier_VIEW.Keycode AND RepairClaim.CustomerID = Customer.CustomerID;
                                            ")
                        or die("<h2> Error - Invaild get claim details query.");

                    $this->searchResult = mysql_fetch_array($result);
                
                    // set the global variable if found
                    $this->claimID = $claimID;
                }
                else
                {

                    die("<h2> Error - Invaild ClaimID for repair order form - Must contain only numbers and be between 1-20 characters in length.");
                }
        }

        private function printHTML()
        {
            echo "<html lang=\"en\">
            <head>
                <title> Big W | Product Claims System | New Customer Claim </title>
                <meta charset=\"utf-8\">
                <style>
                </style>
                <link rel=\"stylesheet\" href=\"Css/PrintPageStyle.css\">
                
                <script src=\"Javascript/RepairOrder.js\" type=\"text/javascript\" media=\"print\"></script>
            </head>

            <body onload=\"window.print()\">
                <div id='printform-topmenu-bar'>
                    <div id='heading-navbar'> Repair Order Form </div>
                    <button class='search-button' id='mainmenu-button' 
                    onclick='loadMainMenuPage()'> Main Menu </button>
                    
                    <button class='search-button' id='printagain-button' 
                    onclick='window.print()'> Print Again </button>
                </div>
                
                <div id='printform-content-wrapper'>
                
                    <div id='printform-margintop'></div>
                    <div class=\"bigw-logo-container\">
                        <img src=\"Images/print-bigw-logo.jpg\" width=\"100%\">
                    </div>

                    <div id=\"bigw-details-container\">
                        <div class=\"bigw-details-line\">
                            1 Woolworths Way, Bella Vista, NSW 2153 Australia
                        </div>
                        <div class=\"bigw-details-line\">
                            PO Box 8000, Baulkham Hills, NSW 2153
                        </div>
                        <div class=\"bigw-details-line\">
                            Phone (02) 8885 8000 Fax (02) 8885 0336
                        </div>
                        <div class=\"bigw-details-line\">
                            A Division of Woolworths Limited
                        </div>
                        <div class=\"bigw-details-line\">
                            ABN 88 000 014 675
                        </div>
                    </div>

                    <div class=\"column-wrapper\">
                        <div id=\"assessment-heading-container\">
                            <h1>Assessment/Repair Order</h1>
                        </div>

                        <div id=\"ordernumber-heading-container\">
                            <h2>Order No: $this->claimID </h2>
                            <h3>Date: $this->claimDate </h3>
                        </div>
                    </div>


                    <div class=\"column-wrapper\">
                        <div class=\"address-container\">
                            <h2>TO:</h2>
                            <h3>".$this->searchResult['SupplierName']."</h3>
                            <h3>".$this->searchResult['SupplierAddress']."</h3>
                        </div>

                        <div class=\"address-container\">
                            <h2>FROM:</h2>
                            <h3>259 BIGW Stafford</h3>
                            <h3>Stafford Road</h3>
                            <h3>Stafford Queensland 4053</h3>
                        </div>
                    </div>

                    <div id=\"customer-details-container\">
                        <h2>CUSTOMER DETAILS:</h2>
                        <h3>Name: ".$this->searchResult['FirstName']." ".$this->searchResult['LastName']."</h3>
                        <h3>Phone: ".$this->searchResult['CustomerPhone']."</h3>
                        <h3>Address: ".$this->searchResult['HouseNumber']." ".$this->searchResult['Street']." ".$this->searchResult['SuburbCity']." ".$this->searchResult['State']." ".$this->searchResult['Postcode']."</h3>
                    </div>

                    <div id=\"item-details-container\">
                        <table id=\"item-table\" style=\"width: 90%\">
                          <tr>
                            <th style=\"min-width: 20%\">Keycode</th>
                            <th style=\"min-width: 60%\">Description</th> 
                            <th style=\"min-width: 20%\">Units</th>
                          </tr>
                          <tr>
                            <td>".$this->searchResult['Keycode']."</td>
                            <td>".$this->searchResult['ProductName']."</td>
                            <td>1</td>
                          </tr>
                        </table>
                    </div>

                    <div id=\"work-required-container\">
                        <h2>WORK REQUIRED:</h2>
                        <h3>".$this->searchResult['RepairMessage']."</h3>
                    </div>

                    <div id=\"type-container\">
                        <h2>".$this->searchResult['Type']."</h2>
                    </div>

                    <div id=\"type-container\">
                        <h2>AMOUNT TO PAY: ".$this->searchResult['RepairCost']."</h2>
                    </div>

                    <div class=\"column-wrapper\">
                        <div id=\"signiture-container\">
                            <h2>Customer Signature</h2>
                        </div>
                    </div>

                    <div id=\"notes-container\">

                        <h2>IMPORTANCE NOTICE - PLEASE READ
                </h2>

                        <h4> - Big W reserves the right to pass on the manufacture or repair ageents service or inspection fee to where there is no fault found during the product assessment to process or where it is determined that the product fault has been caused by misuse or abuse. This charge is typically between $30-$50.</h4>

                        <h4> - If your product contains user generated data, assessement of this product may result in loss of this data. Goods presented for repair may be replaced refurbished goods of the same type rather than being repaired. Refurbished parts may be used to repair goods.</h4>

                        <h4> - The maximum period for which we are able to hold repaired good or goods left for quotation is six months.</h4>

                        <h4> - No responsbility will be taken for the deliever of goods left longer than six months.</h4>

                        <h4> - Property unclaimed within the period specified will be disposed of in the order to free sotrage space and recover any repair expenses.</h4>
                    </div>
                </div>

            </body>
            </html> ";
        }

    }

?>