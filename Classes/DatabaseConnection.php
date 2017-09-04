<?php
    
    class DatabaseConnection
    {
        public $db;
        public $er;
        
        public function __construct()
        {
            // connect to MySQL server (host,user,password)
            $db = mysql_connect("localhost","pcsaccess","eFGVyyUxLIfz9oFL!")
                or die("<h1> Error - Could not connect to mysql</h1\n
                    possible privilege problem\n");

            // select the database
            $er = mysql_select_db("ProductClaimSystem")
                or die("<h1> Error - No connection to the database</h1>");
            
            // start session for each page
            if(!isset($_SESSION)) 
            { 
                session_start(); 
            } 
        }
    }
?>
