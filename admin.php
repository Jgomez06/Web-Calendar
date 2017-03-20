<?php
ini_set('display_errors','1');
error_reporting(E_ALL);

include 'users-db.php';

if(!isset($user_db))
{
  $user_db = new Users();
}

$edit_mode = False;
$validation_message = $user_db->get_validation_message();

if(isset($_POST['action'])){
  $action = $_POST['action'];

  switch ($action) {
    case 'Edit':
      $acc_id = $_POST['id'];
      $acc_name = $_POST['accname'];
      $username = $_POST['username'];
      $edit_mode = True;
      break;
    case 'Delete':
      $acc_id = $_POST['id'];
      $user_db->delete($acc_id);
      break;
    case 'Create':
      $acc_name = $_POST['accname'];
      $username = $_POST['username'];
      $password = $_POST['password'];
      $user_db->insert($acc_name, $username, $password);
      $validation_message = $user_db->get_validation_message();
      break;
    case 'Update':
      $acc_id = $_POST['id'];
      $acc_name = $_POST['accname'];
      $username = $_POST['username'];
      $original_username = $_POST['original_username'];
      $password = $_POST['password'];
      $user_db->update($acc_id, $acc_name, $username, $original_username, $password);
      $validation_message = $user_db->get_validation_message();
      break;
    default:
      # code...
      break;
  }
}

$users = $user_db->get_users();
include('admin-view.php');
?>
