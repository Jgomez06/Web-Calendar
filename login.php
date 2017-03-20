<?php
include 'account.php';

if(!isset($db))
{
  $db = new Accounts();
}

if(isset($_POST['SignIn'])){
  $action = $_POST['SignIn'];
  $username = $_POST['Username'];
  $password = $_POST['Password'];
  if($db->validate_user($username, $password))
    header('Location: calendar.php');
}

$errors = $db->errors;
include('login-view.php');
?>
