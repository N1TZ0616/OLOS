<?php
/**
 * Logout Script
 * Safely destroys user session and redirects to homepage
 */

session_start();

/* Clear all session variables */
$_SESSION = [];

/* Destroy the session */
session_destroy();

/* Redirect to home / login page */
header("Location: index.php");
exit;
