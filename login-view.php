<?php
session_start();
if($_SESSION['username']) {
  header('location: calendar.php');
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Log in</title>
    <link rel="stylesheet" type="text/css" href="login-style.css">
  </head>

  <body>
    <br/>
    <div id="logo" align="middle">
      <img src="calendar.jpg" alt="calendar" style="width:161px;height:50px;">
    </div>
    <br/>


    <p id="text-one">One calendar. All of your events.</p>
    <p id="text-two">Sign in with your Calendar Account</p>

    <div id="login-container" align="middle">
      <form method="post" action="login.php">
        <br/>
        <img src="profile.jpg" alt="profile">
        <br/><br/>
        <div>
          <?php echo $errors ?>
        </div>
        <div>
          <input id="username" name="Username" type="text" size="20"
                 maxlength="20" placeholder="Enter your username">
        </div>
        <br/>
        <div>
          <input id="password" name="Password" type="password" size="20"
                 maxlength="20" placeholder="Enter your password">
        </div>

        </br>
        <div>
          <input id="submit" class="button blue" width="100%" name="SignIn"
                 type="submit" value="Sign In">
        </div>
      </form>
      <br/><br/>
    </div>
    <br/>
    <p id="text-three">One Calendar Account for everything happening.</p>
  </body>
</html>
