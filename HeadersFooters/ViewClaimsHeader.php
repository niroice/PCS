<?php 
    include("Classes/DatabaseConnection.php");
    include("Classes/StatusNotificationPanel.php");

    // create database connection
    $dbConnection = new DatabaseConnection();

?>
<html lang="en">
<head>
	<title> Big W | Product Claims System | View Claims </title>
	<meta charset="utf-8">
	<style>
	</style>
	<link rel="stylesheet" href="Css/styles.css" media="screen">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <script src="Javascript/javascript.js" type="text/javascript"></script>
    <script src="Javascript/ViewClaim-jQuery.js" type="text/javascript"> </script>
    <meta name="description" content="">
</head>

<body>
    <!--************ pop up confirm button ************-->
    <div class="warning-confirm-container">
        <div class="warning-heading-container">
            <h1 class="warning-heading-type"></h1>
            <h1 class="warning-heading-message"></h1>
        </div>
        <div class="warning-confirm-button-container">
            <button type="button" class="warning-confirm-button">OK</button>
        </div>
    </div>
    <div class="warning-screen-cover"></div>
    
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
            View Claims
    </div>
    
    <div id="body-container" >