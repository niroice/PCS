<?php
    include('Classes/UserControl.php');
    include('Classes/DatabaseConnection.php');

    // create database connection
    $dbConnection = new DatabaseConnection();

    // create user control object
    $userControl = new UserControl();

    // always check to see if user is logged in already, if redirect page to main menu
    if ($userControl->loginCheck() == true)
    {
        header('location: MainMenu.php');
    }
    // if the login button was pressed check the password and username in the database
    // if correct will load the main menu/if false reload the login page with failed message
    else if (isset($_POST['login-login-button']))
    {
        $username = $_POST['login-employeenumber-input'];
        $password = $_POST['login-password-input'];
        
        $userControl->userLogin($username, $password);
    }
?>

<html lang="en">
<head>
	<title> Big W | Product Claims System | Employee Sign-in </title>
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
            Employee Sign-in 
    </div>
    
    <div id="body-container" >
        
        <!--************ login ************-->
            <div class="login-container">
                <fieldset>
                    <legend>Login</legend>
                    <form action="Login.php" method="post" name="login-form">
                        <div class="input-wrapper">
                            <label>Employee Number:</label>
                            <input type="text" class="login-input" name="login-employeenumber-input" id="login-employeenumber-input">
                        </div>
                        <div class="input-wrapper">
                            <label>Password:</label>
                            <input type="password" class="login-input" name="login-password-input" id="login-password-input">
                        </div>
                        <div class="main-menu-container">
                                <button text="Search" type="submit" class="search-button" name="login-login-button">Login</button>
                        </div>
                        
                        <?php
                        
                        // if failed to login
                        if (isset($_GET['attempt']) && $_GET['attempt'] == 'failed')
                        {
                            echo "
                            <div id=\"login-error-container\">
                                <h2 class=\"error-list\"> Error - Invaild Username/Password.</h2>
                                <h2 class=\"error-list\"> Please try again.</h2>
                            </div>
                            ";
                        }
                        ?>
                    </form>
                </fieldset>
                
            </div>
    
    </div>
</body>
</html>