<?php

    include('Classes/UserControl.php');
    include('Classes/DatabaseConnection.php');
    include('Classes/CreateAccountService.php');
    
    $userNameUnique = false;
    $passwordsMatch = false;
    $passwordMeetsPolicy = false;
    $passwordMatchErrorMessage = "<h2 class=\"error-list\"> Error - Passwords do not match</h2>";
    $passwordFailedPolicyMessage = "<h2 class=\"error-list\"> Error - Passwords must be 8 characters 
                                    and contain both numbers and letters.</h2>";
    $usernameExistErrorMessage = "<h2 class=\"error-list\"> Error - Username already exists. Try another.</h2>";
    $userAccountService;
    

    // create database connection
    $dbConnection = new DatabaseConnection();

    // create user control object - checks login status
    $userControl = new UserControl();

    // always check to see if the user is logged in, if NOT redirect page to the login page
    if ($userControl->loginCheck() == false)
    {
        header('location: Login.php');
    }


    // if the login button was pressed check the password and username in the database
    // if correct will load the main menu/if false reload the login page with failed message
    else if (isset($_POST['signup-create-button']))
    {
        $username = $_POST['signup-employeenumber-input'];
        $password = $_POST['signup-password-input'];
        $passwordAgain = $_POST['signup-passwordagain-input'];
        
        $userAccountService = new CreateAccountService($username, $password, $passwordAgain);
        $errorsArray = $userAccountService->addUserDatabase();
        
         // if errors array is empty - load main menu page
        if(empty($errorsArray)){
            
            header("location: MainMenu.php");
        }
        
       
        
    }
?>

<html lang="en">
    <head>
        <title> Big W | Product Claims System | Create User Account </title>
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
                Create Account 
        </div>

        <div id="body-container" >

            <!--************ login ************-->
                <div class="login-container">
                    <fieldset>
                        <legend>Enter Account Details</legend>
                        <form action="signup.php" method="post" name="login-form">
                            <div class="input-wrapper">
                                <label>Employee ID:</label>
                                <input type="text" class="login-input" name="signup-employeenumber-input" id="signup-employeenumber-input">
                            </div>
                            <div class="input-wrapper">
                                <label>Password:</label>
                                <input type="password" class="login-input" name="signup-password-input" id="signup-password-input">
                            </div>
                            <div class="input-wrapper">
                                <label>Password Again:</label>
                                <input type="password" class="login-input" name="signup-passwordagain-input" id="signup-passwordagain-input">
                            </div>
                            <div class="main-menu-container">
                                    <button text="Search" type="submit" class="search-button" name="signup-create-button">Create Account</button>
                            </div>

                            <?php

                                // user clicked create account button
                                if (isset($_POST['signup-create-button']))
                                {
                                    echo "<div id=\"login-error-container\">";

                                    foreach($errorsArray as $error){

                                        echo $error;
                                    }

                                    echo "</div>";
                                }
                            ?>
                        </form>
                    </fieldset>

                </div>

        </div>
    </body>
</html>