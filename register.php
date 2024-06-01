<?php

include 'config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;


if(isset($_POST['submit'])){

   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $pass = mysqli_real_escape_string($conn, md5($_POST['password']));
   $cpass = mysqli_real_escape_string($conn, md5($_POST['cpassword']));

   $select_users = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'") or die('query failed');


   if(mysqli_num_rows($select_users) > 0){
      $message[] = 'User already exist!';
   }else if($pass != $cpass){
      $message[] = 'Passwords do not match!';
   }else{
      function sendEmail($email,$v_code){

         require 'PHPMailer/PHPMailer.php';
         require 'PHPMailer/SMTP.php';
         require 'PHPMailer/Exception.php';
         
         $mail = new PHPMailer(true);

         try {

            $mail->isSMTP();                                          //Send using SMTP
            $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                 //Enable SMTP authentication
            $mail->Username   = 'ume.cooray@gmail.com';               //SMTP username
            $mail->Password   = 'lyls fcil vggu mcvb';                          //SMTP password
            $mail->SMTPSecure =  PHPMailer::ENCRYPTION_SMTPS;        //Enable implicit TLS encryption //PHPMailer::ENCRYPTION_SMTPS;  
            $mail->Port       = 465;                                  //TCP port to connect to; use 587 if you have set SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS
         
               //Recipients
               $mail->setFrom('ume.cooray@gmail.com', 'XOXO');
               $mail->addAddress($email);     //Add a recipient
         
               //Content
               $mail->isHTML(true);                                  //Set email format to HTML
               $mail->Subject = 'Email Verification from XOXO';
               $mail->Body    = "Thanks for registration!
               Click below link to verify the email address. 
               <a href='http://localhost/XOXO/verify.php?email=$email&v_code=$v_code'>Verify</a>";
            
               $mail->send();
               return true;
            } catch (Exception $e) {
               return false;
            }
      }
      
      
      $v_code = bin2hex(random_bytes(16));
      $is_send = sendEmail($email, $v_code);
      if ($is_send) {
         mysqli_query($conn, "INSERT INTO users(name, email, password, user_type, verification_code, is_verified) VALUES('$name', '$email', '$cpass', 'user','$v_code','0')") or die('query failed');
         $message[] = 'Registered successfully! Check your email to verify the email address.';

         // Delay the redirection by 3 seconds (3000 milliseconds)
         echo '<script>
                  setTimeout(function(){
                     window.location.href = "login.php";
                  }, 10000);
               </script>';
      }

   } 
}

if(isset($message)){
   foreach($message as $message){
      echo '
      <div class="message">
         <span>'.$message.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Register</title>
   <link rel="icon" type="image/x-icon" href="images/XOXO.png">


   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">


   <link rel="stylesheet" href="css/user_style.css">

</head>
<body>

<div class="form-container-log">

   <form action="" method="post">
      <h3>Register now</h3>
      <input type="text" name="name" placeholder="Enter your name" required pattern="[A-Za-z\s]{3,}" title="Name must contain only letters and have at least 3 characters" class="box">
      <input type="email" name="email" placeholder="enter your email" required class="box">
      <input type="password" name="password" placeholder="enter your password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" required class="box">
      <input type="password" name="cpassword" placeholder="confirm your password" required class="box">
      <!-- <select name="user_type" class="box">
         <option value="user">user</option>
         <option value="admin">admin</option>
      </select> -->
      <input type="submit" name="submit" value="register now" class="btn">
      <p>Already have an account? <a href="login.php">Login now</a></p>
   </form>

</div>

</body>
</html>