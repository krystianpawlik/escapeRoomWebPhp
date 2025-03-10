<?php
session_start();
session_destroy();
setcookie("auth", "", time() - 3600, "/"); // Delete the cookie

header("Location: login.php");
exit();
?>