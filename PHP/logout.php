<?php
session_start();

$_SESSION['username'] = null;
$_SESSION['usertype'] = null;
unset($_SESSION['username'] );
unset($_SESSION['usertype'] );

header("location:index.php");

?>