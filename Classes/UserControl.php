<?php
    class UserControl
    {
        private $databaseConnection;
        private $serverConnection;
        
        public function __construct()
        {
            // connect to MySQL server (host,user,password)
            $db = mysql_connect("localhost","pcsaccess","eFGVyyUxLIfz9oFL!")
                or die("<h1> Error - Could not connect to mysql</h1\n
                    possible privilege problem\n");
            
            // select the database
            $databaseConnection = mysql_select_db("pcs_users")
                or die("<h1> Error - No connection to the database</h1>");
            
            // start session for each page
            $this->checkSession();
            
        }
        
        // makes sure a session is running
        private function checkSession()
        {
            // start session for each page
            if(!isset($_SESSION)) 
            { 
                session_start(); 
            } 
        }
        
        // runs on every page to ensure the session is vaild/user logged in
        public function loginCheck()
        {
            if(!isset($_SESSION['UserLoggedin']))
            {
                // remove all session variables
                session_unset(); 

                // destroy the session 
                session_destroy();
                
                $_SESSION = null;
                
                return false;
            }
            else // return true if logged in
            {
                return true;
            }
        }
        
        
        // kills the session and loads the login page
        public function logout()
        {
                // remove all session variables
                session_unset(); 

                // destroy the session 
                session_destroy();
                
                $_SESSION = null;
                
                // take user to the login page
                header("location: Login.php");
        }
        
        
        // checks for vaild user name and password - if so creates a vaild session
        public function userLogin($username, $password)
        {
            $passwordQuery = mysql_query("SELECT Password FROM Users WHERE UserName = '$username'");
            $databasePassword = mysql_fetch_array($passwordQuery);
            
            
            
            if(password_verify($password, $databasePassword['Password']))
            {
                // create ession
                session_start(); 
                
                // set user login as true
                $_SESSION['UserLoggedin'] = true;
                
                header("location: MainMenu.php");
            }
            else // if they do not match load login page again with failed attempt
            {
                header("location: Login.php?attempt=failed");
            }
        }
    }
?>