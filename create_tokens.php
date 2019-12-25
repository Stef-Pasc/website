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
$company = $quantity = "";
$company_err = $quantity_err = $ok_for_creation = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if company is empty
    if(empty(trim($_POST["company"]))){
        $company_err = "Please enter company name.";
    } else{
        $company = trim($_POST["company"]);
    }
    
    // Check if quantity is empty
    if(empty(trim($_POST["quantity"]))){
        $quantity_err = "Please enter your quantity.";
    } else{
        $quantity = trim($_POST["quantity"]);
    }
 
    // Validate credentials
    if(empty($company_err) && empty($quantity_err)){
        // Prepare a select statement
        $sql = "SELECT token_id, company_id FROM tokens WHERE company_id = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_company);
            
            // Set parameters
            $param_company = $company;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                
                // Check if username exists, if yes then verify password
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    // Bind result variables
                    //echo "OK";     
                    mysqli_stmt_bind_result($stmt, $id, $company,$user_create_tokens);
                    if(mysqli_stmt_fetch($stmt)){
                        //echo "$user_create_tokens";
                        if($user_create_tokens=="Company"){
                            $ok_for_creation=1;
                            // Redirect user to congrats page
                            //header("location: congrats.php");
                        } else{
                            // Display an error message 
                            $ok_for_creation = "The company name was not valid.";
                        }
                    }
                } else{
                    // Display an error message if username doesn't exist
                    $company_err = "No company found with that name.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        
        // Close statement
        mysqli_stmt_close($stmt);
    }


    if(empty($company_err) && empty($quantity_err) && $ok_for_creation){
            
        // Prepare an insert statement
        $sql = "INSERT INTO tokens (company, owner, quantity) VALUES (?, ?, ?)";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sss", $param_company, $param_company,$param_quantity);
            
            // Set parameters
            $param_company = $company;
            $param_quantity = $quantity;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Redirect to login page
                header("location: congrats.php");
            } else{
                echo "Something went wrong. Please try again later.";
            }
        }
    }
    // Close connection
    mysqli_close($link);
}
?>



 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; text-align: center; }
    </style>
</head>
<body>
    <div class="page-header">
        <h1>Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Welcome to our site.</h1>
    </div>
    <p>
        <a href="reset-password.php" class="btn btn-warning">Reset Your Password</a>
        <a href="logout.php" class="btn btn-danger">Sign Out of Your Account</a>
    </p>

    <div class="wrapper">
        <h2>Create tokens</h2>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($company)) ? 'has-error' : ''; ?>">
                <label>Company</label>
                <input type="text" name="company" class="form-control" value="<?php echo $company; ?>">
                <span class="help-block"><?php echo $company_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($quantity_err)) ? 'has-error' : ''; ?>">
                <label>Quantity</label>
                <input type="text" name="quantity" class="form-control">
                <span class="help-block"><?php echo $quantity_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Create tokens">
            </div>

        </form>
    </div>    
</body>

</html>
