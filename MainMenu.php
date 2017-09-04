<?php
    include('Classes/UserControl.php');

    // create user control object
    $userControl = new UserControl();

    // always check to see if user is logged in, if NOT redirect page to the login page
    if ($userControl->loginCheck() == false)
    {
        header('location: Login.php');
    }
    // if logout button was pressed, reset the session and got to login page
    else if(isset($_POST['mainmenu-logout-button']))
    {
        $userControl->logout();
    }
    // if customer claim button was clicked load customer claim page
    else if (isset($_POST['mainmenu-customerclaim-button']))
    {
        header('location: CustomerClaim.php');
    }
    // if the store claim button was clicked load claim details
    else if (isset($_POST['mainmenu-storeclaim-button']))
    {
        header('location: ClaimDetails.php');
    }
    // if view claims button was clicked load the view claims page
    else if (isset($_POST['mainmenu-viewclaims-button']))
    {
        header('location: ViewClaims.php');
    }
?>

<html lang="en">
<head>
	<title> Big W | Product Claims System | Main Men </title>
	<meta charset="utf-8">
	<style>
	</style>
	<link rel="stylesheet" href="Css/styles.css" media="screen">
    <script src="javascript/javascript.js" type="text/javascript"> </script>
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
            Main Menu
    </div>
    
    <div id="body-container" >

        <!--************ Main Menu Panel ************-->
        <div class="main-menu-container">
            <form action="MainMenu.php" method="post">
                <button type="submit" class="main-menu-button" name="mainmenu-customerclaim-button">New Customer Claim</button>
                <button type="submit" class="main-menu-button" name="mainmenu-storeclaim-button">New Store Claim</button>

                <button type="submit" class="main-menu-button" name="mainmenu-viewclaims-button">View/Modify Claims</button>

                <button type="submit" class="main-menu-button" name="mainmenu-logout-button">Logout</button>
            </form>
        </div>
    
    </div>
</body>
</html>