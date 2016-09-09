<?php 
session_start();
$errormessage = $errormessage1 = ' ';
require_once 'config.php';
if (isset($_POST['signup'])) {
    //Initialize variables
    $error = 0;

    //Receive input values
    $firstname = htmlspecialchars($_POST['firstname']);
    $lastname = htmlspecialchars($_POST['lastname']);
    $email = htmlspecialchars($_POST['email']);

    //validate email ID
    $emailCheck = mysqli_query($dbConn, "SELECT * FROM customer WHERE email = '$email'");
    if (mysqli_num_rows($emailCheck) > 0) {
        $errormessage .=  "Email ID already exists<br>";
        $error = 1;
    }

    $password = htmlspecialchars($_POST['password']);
    $conpass = htmlspecialchars($_POST['conpass']);

    //validate password and confirm password
    if (strcmp($password,$conpass) !== 0) {
        $errormessage .= "Password and Confirm password do not match<br>";
        $error = 1;
    }

    $mobile = htmlspecialchars($_POST['mobile']);
    $output = array();
    $file_pointer = fopen("errorlog.txt", "a");
            $errorlogmessage = '';
    //process if validation passes
    if ($error == 0) {

        $insertSQL = "INSERT INTO customer (first_name_txt, last_name_txt, email, mobile_txt, password, created_dt, updated_dt) VALUES ('$firstname','$lastname','$email','$mobile','$password',now(), now())";
        $insertRes = mysqli_query($dbConn, $insertSQL);

        if ($insertRes) {
            $errorlogmessage .= "result success: ";
            $_SESSION['customer_id'] = mysqli_insert_id($dbConn);
            $_SESSION['customer_name'] = $firstname;        
            header("Location: home.php");
        } else {
            $errorlogmessage .= "result failure: ";
            $errormessage = "Sign Up error.  Please report to test@test.com";
            $errorlogmessage = "\n------------".date('m/d/Y h:i:s a', time())."---------------\nMysqli error: ".  mysqli_error($dbConn)." \n While executing ".$insertSQL."\n------------------------";
            $errorlogmessage .= "Error while inserting into customers table";
            $file_pointer = fopen("errorlog.txt", "a");
            fwrite($file_pointer, $errorlogmessage);			
            fclose($file_pointer);
        }
    } 
} else if (isset ($_POST['signin'])) {
    //Receive input values
    $email1 = htmlspecialchars($_POST['email']);
    $password1 = htmlspecialchars($_POST['password']);

    $selectSQL = "SELECT * FROM customer WHERE email = '$email1' and password = '$password1'";
    $selectRes = mysqli_query($dbConn, $selectSQL);

    $output = array();

    if(mysqli_num_rows($selectRes) > 0) {
        $customer = mysqli_fetch_assoc($selectRes);
        $_SESSION['customer_id'] = $customer['customer_id'];
        $_SESSION['customer_name'] = $customer['first_name_txt'];
        header("Location: home.php");
    } else {
        $errormessage1 = "Login unsuccessful.  Incorrect username and/or password.";
    }
}else if (isset ($_GET['logout'])) {
    if ($_GET['logout'] === "true") {
        unset($_SESSION['customer_id']);
        $message = "Logged out successfully";
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
        <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
        <title>Food-e-Comm</title>
    </head>
    <body>
        <h2 class="text-center">Food-e-Comm Online Restaurant</h2>
        <div class="container">
            <div class="row">
                <div class="col-sm-offset-3 col-md-6 col-sm-offset-3" ><br />
                    <h4 style="line-height:150%">GREETINGS! It's a pleasure to welcome you to The Online Restaurant.  Here on our website you will find extensive varities of dishes to order.  Enjoy eating and feel happy. </h4>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <div class="well">
                        <h2>Sign In</h2>
                        <hr>
                        <form id="signinForm" role="form" method="POST">
                            <input type="text" class="form-control" name="email" required="required" <?php echo isset($email1)? 'value="'.$email1.'"' : 'placeholder="Email Address" '?>><br>
                            <input type="password" class="form-control" name="password" required="required" <?php echo isset($password1)? 'value="'.$password1.'"' : 'placeholder="Password" '?>><br>
                            <button type="submit" name="signin" class="btn btn-primary">Sign in</button>
                            <div class="text-danger"><strong><?php echo $errormessage1;?></strong></div>
                        </form>
                    </div>
                </div>
                
                <div class="col-sm-4 pull-right">
                    <div class="well">
                        <h2>Sign Up</h2>
                        <hr>
                        <form id="signupForm" role="form" method="POST" action="index.php">
                            <div class="text-danger"><strong><?php echo $errormessage;?></strong></div>
                            <input type="text" class="form-control" name="firstname"  required="required" <?php echo isset($firstname)? 'value="'.$firstname.'"' : 'placeholder="FirstName" '?>><br>
                            <input type="text" class="form-control" name="lastname" required="required" <?php echo isset($lastname)? 'value="'.$lastname.'"' : 'placeholder="LastName" '?>><br>
                            <input type="text" class="form-control" name="email"  required="required" <?php echo isset($email)? 'value="'.$email.'"' : 'placeholder="Email Address" '?>><br>
                            <input type="text" class="form-control" name="mobile"  required="required" <?php echo isset($mobile)? 'value="'.$mobile.'"' : 'placeholder="Mobile Number" '?>><br>
                            <input type="password" class="form-control" name="password"  required="required" <?php echo isset($password)? 'value="'.$password.'"' : 'placeholder="Password" '?>><br>
                            <input type="password" class="form-control" name="conpass"  required="required" <?php echo isset($conpass)? 'value="'.$conpass.'"' : 'placeholder="Confirm Password" '?>><br>
                            <button type="submit" name="signup" class="btn btn-primary">Sign Up</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
<!--        <script src="js/customer.js"></script>-->
    </body>
</html>
