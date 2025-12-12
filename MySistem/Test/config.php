<?php
    $conn = mysqli_connect('localhost', 'root', '', 'welcome');
    
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
?>