<?php
  session_start();
  if(!$_SESSION['username']) {
    header('location: login.php');
  }
?>
<!DOCTYPE HTML>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta title="Admin">
    <link rel="stylesheet" type="text/css" href="style.css">
  </head>

  <body>
    <div class="nav">
			<nav>
				<a href="calendar.php">Calendar</a>
				<a href="input.php">Input</a>
        <a href="admin.php">Admin</a>
        <a href="logout.php">Log out</a>
			</nav>
		</div>

    <p align="center" style="font-size: 25px;color: white; font-weight: bold;">
      Logged  in as <?php echo $_SESSION['username']?></p>

    <div id="error-msg-container">
      <span><?php echo $validation_message; ?></span>
    </div>

    <div id="users-container">
      <table class="content-table">
        <caption><h1> List of users</h1></caption>
        <tr>
          <th class="table-header">ID</th>
          <th class="table-header">Name</th>
          <th class="table-header">Login</th>
          <th class="table-header">New Password</th>
          <th class="table-header">Action</th>
        </tr>
        <?php while ($user = mysqli_fetch_array($users, MYSQLI_ASSOC)) { ?>
        <tr>
          <?php if($edit_mode && $user['acc_id'] == $acc_id) { ?>
            <form action="admin.php" method="post">
            <td><?php echo $user['acc_id'];?></td>
            <td>
              <input name="accname" type="text" value="<?php echo $acc_name; ?>" size=25 maxlength="25">
            </td>
            <td>
              <input name="username" type="text" value="<?php echo $username; ?>" size=25 maxlength="25">
              <input name="original_username" type="hidden" value="<?php echo $username; ?>">
            </td>
            <td>
              <input name="password" type="text" size=25 maxlength="25">
            </td>
            <td>
                <input type="hidden" name="id" value="<?php echo $user['acc_id'];?>">
                <input type="submit" value="Update">
                <input type="hidden" name="action" value="Update">
            </form>
              <form action="admin.php" method="post">
                <input type="submit" value="Cancel">
                <input type="hidden" name="action" value="Cancel">
              </form>
            </td>
          <?php } else { ?>
          <td><?php echo $user['acc_id'];?></td>
          <td><?php echo $user['acc_name']; ?></td>
          <td><?php echo $user['acc_login']; ?></td>
          <td></td>
          <td>
            <form action="admin.php" method="post">
              <input type="submit" value="Edit">
              <input type="hidden" name="action" value="Edit">
              <input type="hidden" name="id" value="<?php echo $user['acc_id']; ?>">
              <input type="hidden" name="username" value="<?php echo $user['acc_login']; ?>">
              <input type="hidden" name="accname" value="<?php echo $user['acc_name']; ?>">
            </form>
            <form action="admin.php" method="post">
              <input type="submit" value="Delete">
              <input type="hidden" name="action" value="Delete">
              <input type="hidden" name="id" value="<?php echo $user['acc_id']; ?>">
            </form>
          </td>
        </tr>
        <?php }} ?>
      </table>
    </div>

    <form action="admin.php" method="post">
      <div id="form-container">
        <table class="content-table">
          <caption><h1>Add a new user</h1></caption>
          <tr>
            <td>Account Name:</td>
            <td>
              <input name="accname" type="text" size=25 maxlength="25">
            </td>
          </tr>
          <tr>
            <td>Username:</td>
            <td>
              <input name="username" type="text" size=25 maxlength="25">
            </td>
          </tr>
          <tr>
            <td>Password:</td>
            <td>
              <input name="password" type="text" size=25 maxlength="25">
            </td>
          </tr>
          <tr>
            <td colspan="2" class="center">
              <input type="hidden" name="action" value="Create">
              <input type="submit" value="Add">
            </td>
          </tr>
        </table>
      </div>
    </form>

  </body>
</html>
