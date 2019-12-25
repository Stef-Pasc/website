<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$user = $_SESSION["username"];
 
// Processing form data when form is submitted
    $sql = "SELECT id, company, owner, quantity FROM tokens WHERE owner = '$user'";
    if($stmt = mysqli_prepare($link, $sql)){
        if(mysqli_stmt_execute($stmt)){
            // Store result
            mysqli_stmt_store_result($stmt);
            // Check if username exists, if yes then verify password
            if(mysqli_stmt_num_rows($stmt) == 1){  //ATTENZIONE : VALIDO SOLO SE C'E UNA SOLA RIGA CON QUESTO SELLER --> DA RIVEDERE                  
                // Bind result variables
                mysqli_stmt_bind_result($stmt, $id, $company ,$seller, $balance);
                //echo "$balance";
                if(mysqli_stmt_fetch($stmt)){
                    echo "$balance";
                } 
                else{
                    // Display an error message 
                    $ok_for_balance = "The balance name was not valid.";
                    }
            }

        }
    }

?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <title>Homepage</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
<div class="page-header">
        <h1>Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Welcome to our site as <?php echo htmlspecialchars($_SESSION["user"]); ?>.</h1>
</div>
    <p>
        <a href="reset-password.php" class="btn btn-warning">Reset Your Password</a>
        <a href="logout.php" class="btn btn-danger">Sign Out of Your Account</a>
    </p>
    <div class="wrapper">
        <h2>Actions</h2>
        <p>Please choose your action to continue.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

        <div class="form-group">
                <a href="create_tokens.php" class="btn btn-warning">Create tokens</a>
            </div>

            <div class="form-group">
            <a href="transfer_tokens.php" class="btn btn-warning">Transfer tokens</a>
            </div>

        </form>
    </div>    
</body>
</html>
