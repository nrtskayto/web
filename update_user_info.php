<?php
include 'dbh.php';
include 'auth.php';

    if (isset($_POST)) {
        
        $username1 = $_POST['username1'];
        $new_pwd1 = $_POST['pwd1'];
        $new_pwd2 = $_POST['pwd2'];
        $password1 = $_POST['password'];

       $curr_user = json_encode($user);

        if (strlen($password1) > 0) {
            if ( $new_pwd1 !== $new_pwd2) {                
                    echo "Unmatching new passwords";           
            }else{
              $query = "UPDATE users SET user_name = '$username1', user_password = '$new_pwd1' WHERE user_name = $curr_user";    
              $updateUserAndPasswords = true;
              if(mysqli_query($conn, $query)){
                  $curr_user = $username1;
                  echo "ok";
              }else{
                  echo "error $sql." . mysqli_error($conn)."<br>";
              }
            } 
        }
    }
?>