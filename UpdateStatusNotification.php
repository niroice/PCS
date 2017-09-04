<?php
    include("Classes/DatabaseConnection.php");
    include("Classes/StatusNotificationPanel.php");

    // create database connection
    $dbConnection = new DatabaseConnection();

    // on loading the page create new object - used with ajax so it updates
    // when a claim's status has changed
    $newNotificationPanel = new StatusNotificationPanel();
?>