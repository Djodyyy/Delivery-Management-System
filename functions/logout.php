<?php
session_start();
session_unset();
session_destroy();
header("Location: /Delivery-Management-System/login.php"); 
exit();
?>
