<?php 
session_start();
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $user = htmlspecialchars($_POST['user']);
    $pass = htmlspecialchars($_POST['pass']);
    
    require_once ('../config.php');
    
    $sql1 = "SELECT user_id, first_name_txt, role_ind FROM users WHERE email= '" .$user ."' and password='" . $pass."'";
    $result1 = mysqli_query($dbConn, $sql1);
    
    if(mysqli_num_rows($result1) > 0) {
        $row = mysqli_fetch_assoc($result1);
        $_SESSION['user_name'] = $row['first_name_txt'];
        $_SESSION['user_id'] = $row['user_id'];
        header("Location: adminHome.php");
    } else {
        $message = "Login unsuccessful.  Incorrect username and/or password.";
    }
} else if (isset ($_GET['logout'])) {
    if ($_GET['logout'] === "true") {
        unset($_SESSION['user_id']);
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
        <title>Online Restaurant</title>
    </head>
    <body>
        <h2 class="text-center">Online Restaurant</h2>
        <div class="container">
            <div class="row">
                <div class="col-md-6" ><br />
                    <h4 style="line-height:200%">GREETINGS! Welcome to the admin panel of online restaurant website. </h4>
                </div>
                <div class="col-md-5"><br />
                    <div class="panel panel-default">
                        <div class="panel-heading text-center">
                            <span class="panel-title glyphicon glyphicon-user"></span>
                        </div>
                        <div class="panel-body">
                            <form class="form-horizontal" role="form" id="login-form" method="post" action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>>
                                <div class="col-sm-12">
                                    <input type="text" class="form-control" name="user" placeholder="User name" autofocus="autofocus" required="required"/> <br />
                                </div>
                                <div class="col-sm-12">
                                    <input type="password" name="pass" class="form-control" placeholder="Password" required="required"/><br />
                                </div>
                                <div class="col-sm-12">
                                <button type="submit" class="btn btn-primary btn-block">Sign in</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="well">
                        Demo admin Username/Password: admin@test.com/admin123<br />
                    </div>
                </div><div class="clearfix"></div>
                <div class="text-center text-error"><?php echo $message; ?></div>
            </div>
        </div>
            </body>
</html>
