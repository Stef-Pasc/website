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
$username = $_SESSION["username"];
$user = $_SESSION["user"];
 
// Processing form data when form is submitted
    $sql = "SELECT id, company, owner, quantity FROM tokens WHERE owner = '$username'";
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
   
    mysqli_stmt_close($stmt);
    }

    // selecting data from investor or company
    if($user==='Investor'){
        $sentence = "Your investments are : ";
        $sql = "SELECT companies.company_name, tokens.quantity FROM tokens JOIN companies ON tokens.company_id = companies.company_id JOIN investors ON tokens.investor_id = investors.investor_id WHERE investors.investor_name='$username'";
        if($stmt = mysqli_prepare($link, $sql)){
            if(mysqli_stmt_execute($stmt)){

                /* bind result variables */
                mysqli_stmt_bind_result($stmt, $company_name, $quantity);
                /* fetch values */
                $j=0;
                while (mysqli_stmt_fetch($stmt)) {
                    $data[$j]=array($company_name,$quantity);
                    //printf ("%s (%s)\n", $company_name, $quantity);
                    ++$j;
                }     
                //for ($j=0;$j<count($data);++$j){printf (" %s %s\n",$data[$j][0],$data[$j][1] );}
            }
        }
        mysqli_stmt_close($stmt);
    }
    // selecting data from investor or company
    elseif($user==='Company'){
        $sentence = "Your investors are : ";
        $sql = "SELECT investors.investor_name, tokens.quantity FROM tokens JOIN investors ON tokens.investor_id = investors.investor_id JOIN companies ON tokens.company_id = companies.company_id WHERE companies.company_name='$username' ORDER BY tokens.quantity DESC";
        if($stmt = mysqli_prepare($link, $sql)){
            if(mysqli_stmt_execute($stmt)){

                /* bind result variables */
                mysqli_stmt_bind_result($stmt, $company_name, $quantity);
                /* fetch values */
                $j=0;
                while (mysqli_stmt_fetch($stmt)) {
                    $data[$j]=array($company_name,$quantity);
                    //printf ("%s (%s)\n", $company_name, $quantity);
                    ++$j;
                }     
                //for ($j=0;$j<count($data);++$j){printf (" %s %s\n",$data[$j][0],$data[$j][1] );}
            }
        }
        mysqli_stmt_close($stmt);
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
<div class="page-header">
        <h1> <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b><?php echo htmlspecialchars($sentence); ?></b> </h1>
</div>
<div class="page-header">
        <h1> <b><?php for ($j=0;$j<count($data);++$j){printf (" %s %s \n",$data[$j][0],$data[$j][1] );} ?></b></h1>
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
