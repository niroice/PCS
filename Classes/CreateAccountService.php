<?php

    class CreateAccountService
    {
        private $databaseConnection;
        private $serverConnection;
        private $username;
        private $password;
        private $passwordAgain;
        private $passwordRegex = "/^(?=.*\d)(?=.*[a-zA-Z]).{8,}$/";
        private $passwordVaild = false;
        private $userNameUnique = false;
        private $errorsArray = array();
        
        public function __construct($username, $password, $passwordAgain)
        { 
            // connect to MySQL server (host,user,password)
            $db = mysql_connect("localhost","pcsaccess","eFGVyyUxLIfz9oFL!")
                or die("<h1> Error - Could not connect to mysql</h1\n
                    possible privilege problem\n");
            
            // select the database
            $databaseConnection = mysql_select_db("pcs_users")
                or die("<h1> Error - No connection to the database</h1>");
            
            $this->username = $username;
            $this->password = $password;
            $this->passwordAgain = $passwordAgain;
            
            $this->checkPasswordVaild();
            $this->checkUsernameUnique();
            
        }
        
        // checks make sure passwords match and is vaild type
        private function checkPasswordVaild()
        {
            
            // check passwords match
            if ($this->password == $this->passwordAgain)
            {
                
                //check password meets the password policy - min 8 character, numbers and letters
                if(preg_match($this->passwordRegex, $this->password) == true){
                    
                    // set password as vaild
                    $this->passwordVaild = true;

                }
                else{
                    array_push($this->errorsArray, "password-invaild");
                }
            }
            else{
                array_push($this->errorsArray, "passwords-dont-match");
            } 
        }
        
        
        // checks user name is unique
        private function checkUsernameUnique()
        {
            // check to make sure username is not already in the database
            $userNameQuery = mysql_query("Select UserName from Users WHERE UserName = '$this->username';");
            
            // check the found username found
            if (mysql_num_rows($userNameQuery) == 0){
                
                $this->userNameUnique = true; // no result found - username unique
                
            }
            else // record error message into array
            {
                array_push($this->errorsArray, "username-already-exists");
            }
        }
        
        
        // adds user to database if conditions are met (true)
        public function addUserDatabase()
        {   
            // check to password and username both vaild
            if ($this->passwordVaild == true && $this->userNameUnique == true){
                
                // hash and salt the password
                $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);
                
                // insert the new user and has
                $result = mysql_query("INSERT INTO Users (UserName, Password)
                             VALUES ('$this->username', '$hashedPassword');
                ");
                
                // check to make sure the user was succesfully added
                if(!$result){
                    
                    array_push($this->errorsArray, "failed-add-user-database");
                }
            }
            return $this->errorsArray;
        }
    }
?>