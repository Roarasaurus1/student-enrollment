<?php
        // Start the session
        session_start();

        // Checks if user is not logged in.
        if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != TRUE){
			$_SESSION['loggedin'] = FALSE;

			// Redirect to login page if not already there.
			if($_SERVER['REQUEST_URI'] != '/login.php') {
				header("location: login.php");
				die();
			}
        }
?>
