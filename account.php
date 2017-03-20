<?php
// ini_set('display_errors','1');
// error_reporting(E_ALL);

class Accounts
{
  private $con;
  public $errors;

  public function __construct( ) {

    $this->con = new mysqli('egon.cs.umn.edu','C4131F16U10',4533771,
                           'C4131F16U10',3307);

    if (mysqli_connect_errno())
    {
      echo 'Failed to connect to MySQL:' . mysqli_connect_error();
      exit(1);
    }

    $this->errors = "";
  }

  public function is_valid_username($username)
  {
    if($username && !preg_match("/[\^£$%&*()}{@#~?!>\s<>|=_+¬-]/", $username)) {
      return True;
    } else {
      return False;
    }
  }

  public function is_valid_password($password)
  {
    if($password && !preg_match("/[\^'\"()}{~>\s<>|=_+¬-]/", $password)) {
      return True;
    } else {
      return False;
    }
  }

  private function generate_err_msg($val_user, $val_pass, $cor_user, $cor_pass)
  {
    if(!$val_user)
      $this->errors = "<span>\nPlease enter a valid value for Login Name Field.
             \n</span><br/>";
    else if(!$cor_user)
      $this->errors = "<span>\nUsername is incorrect: User does not exist. Please
            check the login details and try again.\n</span><br/>";

    if(!$val_pass)
      $this->errors .= "<span>\nPlease enter a valid value for Password Field
                 \n</span><br/>";
    else if($cor_user && !$cor_pass)
      $this->errors .= "<span>\nPassword is incorrect: Please check the password and
                    try again.\n</span><br/>";
  }

  public function validate_user($username, $password)
  {
    $this->errors = "";
    $correct_username = False;
    $correct_password = False;

    $valid_username = $this->is_valid_username($username);
    $valid_password = $this->is_valid_password($password);

    if($valid_username && $valid_password)
    {
      $query = "SELECT * FROM tbl_accounts WHERE acc_login = '$username';";
      $result = $this->con->query($query);
      if($result->num_rows > 0) {
        $correct_username = True;
      }

      if($correct_username) {
        $hash = sha1($password);
        $query = "SELECT * FROM tbl_accounts WHERE acc_login = '$username' AND
                   acc_password = '$hash';";
        $result = $this->con->query($query);
        if($result->num_rows > 0) {
          $correct_password = True;
          session_start();
          $_SESSION['username'] = $username;
        }
      }
    }
    $this->generate_err_msg($valid_username, $valid_password, $correct_username, $correct_password);
    return empty($this->errors);
  }

}
?>
