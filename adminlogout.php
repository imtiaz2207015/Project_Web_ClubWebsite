<?php
session_start();
$_SESSION = [];
session_destroy();

// Delete the remember cookie
setcookie('admin_remember', '', [
    'expires'  => time() - 3600,
    'path'     => '/',
    'httponly' => true,
    'samesite' => 'Strict'
]);

header("Location: adminlogin.php?msg=logged_out");
exit;
?>