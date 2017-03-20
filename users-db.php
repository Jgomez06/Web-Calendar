<?php
ini_set('display_errors','1');
error_reporting(E_ALL);

include 'account.php';

class Users
{
  private $account;
  private $con;
  private $validation_message;

  public function __construct( )
  {
    $this->con = new mysqli('egon.cs.umn.edu','C4131F16U10',4533771,
                           'C4131F16U10',3307);

    $this->account = new Accounts();
    $this->validation_message = "";

    if (mysqli_connect_errno())
    {
      echo 'Failed to connect to MySQL:' . mysqli_connect_error();
      exit(1);
    }
  }

  public function get_users()
  {
    return $this->con->query("SELECT acc_id, acc_name, acc_login
                              FROM tbl_accounts;");
  }

  private function user_exists($username)
  {
    $result = $this->con->query("SELECT *
                                 FROM tbl_accounts
                                 WHERE acc_login = '$username';");
    return $result->num_rows > 0;
  }

  public function update($acc_id, $acc_name, $username, $original_username, $password)
  {
    if($this->valid_user_details($username, $original_username, $password))
    {
      $hash_password =  sha1($password);
      $this->con->query("UPDATE tbl_accounts
                         SET acc_password = '$hash_password' ,
                             acc_name = '$acc_name',
                             acc_login = '$username'
                         WHERE acc_id = $acc_id;");
    }
  }

  private function valid_user_details($username, $original_username, $password)
  {
    $valid_username = $this->account->is_valid_username($username);
    $valid_password = $this->account->is_valid_password($password);
    $new_username = $username != $original_username;

    if($valid_username && $valid_password)
    {
      if($new_username)
      {
        if(!$this->user_exists($username))
        {
          return True;
        } else {
          $this->validation_message = "Username already exists. Try another one.";
        }
      } else {
        return True;
      }
    } else {
      $this->validation_message = "Invalid username or password";
    }

    return False;
  }

  public function delete($acc_id)
  {
    $this->con->query("DELETE FROM tbl_accounts
                       WHERE acc_id = $acc_id;");
  }

  public function insert($acc_name, $acc_login, $acc_password)
  {
    if($this->valid_user_details($acc_login, NULL, $acc_password))
    {
      $this->con->query("INSERT INTO tbl_accounts (acc_name, acc_login, acc_password)
                         VALUES ('$acc_name', '$acc_login', '". sha1($acc_password)."');");
    }
  }

  public function get_validation_message()
  {
    return $this->validation_message;
  }
}
?>
