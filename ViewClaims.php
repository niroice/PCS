<?php
    include("Classes/UserControl.php");
    // create user control object
    $userControl = new UserControl();

    // always check to see if the user is logged in, if NOT redirect page to the login page
    if ($userControl->loginCheck() == false)
    {
        header('location: Login.php');
    }

    include("HeadersFooters/ViewClaimsHeader.php");
?>
        <!--************ verdict Panel ************-->
        <div class="input-wrapper" id="notification-wrapper">
        <?php
                $newNotificationPanel = new StatusNotificationPanel();
        ?>
        </div>
        <!--************ Claims view container and headings ************-->
        <fieldset>
            <legend>Claims Viewer</legend>
            
            <form action="Classes/GenerateClaimsList.php" method="post" name="claimlist-view-form">
                <div class="input-wrapper">
                    <div class="left-wrapper">
                        <label>View:</label>
                        <select name="viewby-dropdown-viewclaims" id="viewby-dropdown-viewclaims">
                            <option value="outstanding-claims">All Outstanding Claims</option>
                            <option value="all-claims">All Claims</option>
                            <option value="repair-claims">Repair Claims</option>
                            <option value="return-claims">Return Claims</option>
                            <option value="finacial-claims">Financial Claims</option>
                        </select>
                    </div>

                    <div class="right-wrapper">
                        <label>Search by Claim No: </label>
                        <input type="text" class="input-text-fifty" name="claimidsearch-input-viewclaims" id="claimidsearch-input-viewclaims">
                    </div>
                </div>

                <div class="input-wrapper">
                    <div class="left-wrapper">
                        <label>Sort By:</label>
                        <select name="sortby-dropdown-viewclaims" id="sortby-dropdown-viewclaims">
                            <option value="date">Date Created</option>
                            <option value="status">Claim Status</option>
                            <option value="name">Supplier/Customer</option>
                            <option value="type">Claim Type</option>
                            <option value="claim-number">Claim Number Desending</option>
                        </select>
                    </div>

                    <div class="right-wrapper">
                        <button text="Search" type="button" class="search-button" name="searchby-button-viewclaims" id="searchby-button-viewclaims" >Search</button>
                    </div>
                </div>
            </form>
        <!--************ Results headings  ************--> 
            <label>Results:</label>
            <div class="results-container">
                
                <div class="input-wrapper" style="padding-top:5px">
                    <div class="claim-number-container">Claim NO.</div>
                    <div class="claim-type-container">Claim Type</div>
                    <div class="supplier-customer-container">Supplier/Customer Name</div>
                    <div class="date-container">Date</div>
                    <div class="claim-status-heading-container">
                        Claim Status
                    </div>
                </div>
        
                <div class="line" id="content-begin-line"></div>
                
                <!--************ Claims list  ************--> 
                <div class="input-wrapper" id="claimlist-wrapper">
                    <?php

                    include("Classes/GenerateClaimsList.php"); 

                    ?>
                </div>
        </fieldset>
                
        <!--************ Buttons ************-->
        <div class="input-wrapper-last" style="margin-top:30px">
            
            <div class="left-wrapper">
                <button text="Search" type="button" class="search-button" style="float:left" onclick="location.href='MainMenu.php';">Main Menu</button>
            </div>

            <div class="right-wrapper">
                <button text="Search" type="button" class="search-button" id="savechanges-button-claimsview" >Save Changes</button>
            </div>
        </div>
    
    </div>
</body>
</html>