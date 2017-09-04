<?php
    include("Classes/DatabaseConnection.php");
    include("Classes/ClaimProductPanel.php");
    include("Classes/ClaimDetailsPanel.php");
    
    // create database connection
    $dbConnection = new DatabaseConnection();

?>
<html lang="en">
<head>
	<title> Big W | Product Claims System | Modify Claim </title>
	<meta charset="utf-8">
	<style>
	</style>
	<link rel="stylesheet" href="Css/styles.css" media="screen">
    <script src="Javascript/javascript.js" type="text/javascript"> </script>
    <meta name="description" content="">
</head>

<body>
    <div class="background-gradient"></div>
    <!--************ Logo and heading ************-->
    <div class="banner-container">
        <div class="bigw-logo-container">
            <img src="Images/bigw-logo-cut.png" height="120">
        </div>
        
        <div class="pcs-logo-container">
            <img src="Images/psc-heading.png" height="45">
        </div> 
    </div> 
    
    <!--************ Page heading ************-->
    <div class="page-heading-container">
            Modify Claim
    </div>
    
    <div id="body-container" >
        <!--************ Product Details Panel ************-->
        <?php 
                $newClaimProductPanel = new ClaimProductPanel();
                $newClaimProductPanel->loadClaim(10000202, 'Warranty');
        
        
                $newClaimDetailsPanel = new ClaimDetailsPanel();
                $newClaimDetailsPanel->loadClaim(10000202, 'Warranty');
        
                
        ?>
        
        <!--************ Claim details Panel ************-->
        
    
        <!--************ Supplier Details Panel ************-->

        <fieldset>
            <legend>Supplier Details</legend>
            <div class="input-wrapper">
                <div class="left-wrapper">
                    <label>Supplier ID: </label>
                    <input type="text" class="input-text-fifty" name="" value="100023" disabled>
                </div>

                <div class="right-wrapper">
                    <label>Supplier Name: </label>
                    <input type="text" class="input-text-fifty" name="" value="AWA electronics" disabled>
                </div>
            </div>
            
            <div class="input-wrapper">
                <div class="left-wrapper">
                    <label>Email: </label>
                    <input type="text" class="input-text-fifty" name="" value="contact@awa.com.au" disabled>
                </div>
                <div class="right-wrapper">
                    <label>Phone: </label>
                    <input type="text" class="input-text-fifty" name="" value="1800 400 330" disabled>
                </div>
            </div>
            
            <div class="input-wrapper">
                <label>Address: </label>
                <input type="text" class="input-text-full" name="" value="33 Pickering Street Melbourne Australia 9983" disabled>
            </div>
            
        </fieldset>
        
        <!--************ Repair Agent Details Panel ************-->

        <fieldset>
            <legend>Nominated Repair Agent</legend>
            <div class="input-wrapper">
                <div class="left-wrapper">
                    <label>Repair Agent ID: </label>
                    <input type="text" class="input-text-fifty" name="" value="323" disabled>
                </div>

                <div class="right-wrapper">
                    <label>Repair Agent Name: </label>
                    <input type="text" class="input-text-fifty" name="" value="JM Repairs" disabled>
                </div>
            </div>
            
            <div class="input-wrapper">
                <div class="left-wrapper">
                    <label>Email: </label>
                    <input type="text" class="input-text-fifty" name="" value="enquiry@JMrepairs.com.au" disabled>
                </div>
                <div class="right-wrapper">
                    <label>Phone: </label>
                    <input type="text" class="input-text-fifty" name="" value="07 3351 2345" disabled>
                </div>
            </div>
            
            <div class="input-wrapper">
                <label>Address: </label>
                <input type="text" class="input-text-full" name="" value="22 stafford Rd Stafford Brisbane Queensland 4053" disabled>
            </div>
            
        </fieldset>
        
        <!--************ Customer details Panel ************-->
        <fieldset>
            <legend> Customer Details </legend>
            <div class="input-wrapper">
                <div class="left-wrapper">
                    <label>First Name: </label>
                    <input type="text" class="input-text-fifty" name="" value="Emily">
                </div>

                <div class="right-wrapper">
                    <label>Last Name: </label>
                    <input type="text" class="input-text-fifty" name="" value="Thompson">
                </div>
            </div>
            
            <div class="input-wrapper">
                <div class="left-wrapper">
                    <label>Phone: </label>
                    <input type="text" class="input-text-fifty" name="" value="07 3851 0707">
                </div>

                <div class="right-wrapper">
                    <label>Email: </label>
                    <input type="text" class="input-text-fifty" name="" value="e.thompson@gmail.com">
                </div>
            </div>
            
            <div class="input-wrapper">
                <div class="left-wrapper">
                    <label>Unit/House Number: </label>
                    <input type="text" class="input-text-fifty" name="" value="33">
                </div>

                <div class="right-wrapper">
                    <label>Street: </label>
                    <input type="text" class="input-text-fifty" name="" value="Falconglen Place">
                </div>
            </div>
            
            <div class="input-wrapper">
                <div class="left-wrapper">
                    <label>City/Suburb: </label>
                    <input type="text" class="input-text-fifty" name="" value="Ferny Grove">
                </div>

                <div class="right-wrapper">
                    <label>State: </label>
                    <input type="text" class="input-text-fifty" name="" value="Queensland">
                </div>
            </div>
            
            <div class="input-wrapper">
                <div class="left-wrapper">
                    <label>Postcode: </label>
                    <input type="text" class="input-text-fifty" name="" value="4053">
                </div>
            </div>
        </fieldset>
        
        
         <!--************ Finalise Repair Panel ************-->
        <fieldset>
            <legend> Finalise Repair </legend>
            <div class="input-wrapper">
                <div class="left-wrapper">
                    <label>Repair Cost: </label>
                    <input type="text" class="input-text-fifty" name="">
                </div>

                <div class="right-wrapper">
                    <label>Authorised By: </label>
                    <input type="text" class="input-text-fifty" name="" value="Sarah">
                </div>
            </div>
            
            <div class="input-wrapper">
                <div class="left-wrapper" style="width:330px">
                    <input type="checkbox">
                    <label>Print Repair Order Form/s </label>
                    
                </div>

                <div class="left-wrapper">
                    <label>Quanity: </label>
                    <input type="text" id="quantity-input" class="input-text-fifty" name="" value="2">
                </div>
            </div>
            
            <div class="input-wrapper">
                <div class="left-wrapper" style="width:100%">
                    <input type="checkbox" checked="yes">
                    <label> Send request for "Return Authorisation Number" now (recommended) </label>
                </div>
            </div>
        </fieldset>
    
        <!--************ Buttons ************-->
        <div class="input-wrapper-last" style="margin-top:30px">
            <a href="view-claims.html">
                <div class="left-wrapper">
                    <button text="Search" type="button" class="search-button" style="float:left">Cancel</button>
                </div>
            </a>
            
            <a href="view-claims.html">
                <div class="right-wrapper">
                    <button text="Search" type="button" class="search-button" >Submit Claim</button>
                </div>
            </a>
            
        </div>
    
    </div>
</body>
</html>