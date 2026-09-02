<?php 
session_start();
$_SESSION = []; 
session_destroy(); 
header("Location: /kwasu_demo/login"); 
exit; 
?>