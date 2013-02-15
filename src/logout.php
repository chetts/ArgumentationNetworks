<?php

session_start();
unset($_SESSION['username']);
session_destroy();

// Jump to login page
header('Location: index.php');

?>