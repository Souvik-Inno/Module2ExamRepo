<?php

  /**
   *  Unset the login credentials and Logout.
   *  Redirect to loginPage.
   */
  session_start();
  unset($_SESSION['logged']);
  unset($_SESSION['email']);
  session_destroy();
  header("Location: loginPage.php")
?>
