<?php
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$username = $password = $confirm_password = $user = "";
$username_err = $password_err = $confirm_password_err = $user_err = $register_err="";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } else{
        // Prepare a select statement
        $sql = "SELECT user_id FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = trim($_POST["username"]);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "This username is already taken.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have atleast 6 characters.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm password.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }

    // Take user type
    if(empty($_POST["user"])){
        $user_err = "Please insert type of user.";     
    } else{
        $user = trim($_POST["user"]);
    }
    
    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($user_err)){
        
        // Prepare an insert statement
        $sql = "INSERT INTO users (username, password, user_type) VALUES (?, ?, ?)";
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sss", $param_username, $param_password,$param_user);
            
            // Set parameters
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            $param_user = $user;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Redirect to login page
                if($user!='Admin'){
                    if($user=="Company"){
                        $sql2 = "INSERT into companies (user_id,company_name) values ((SELECT user_id FROM users WHERE username = ?), ?);";
                    }
                    elseif($user=="Investor"){
                        $sql2 = "INSERT into investors (user_id,investor_name) values ((SELECT user_id FROM users WHERE username = ?), ?);";
                        }    
                    if($stmt2 = mysqli_prepare($link, $sql2)){
                        mysqli_stmt_bind_param($stmt2, "ss", $param_username2,$param_username2);
                    
                        // Set parameters
                        $param_username2 = $username;
                        if(mysqli_stmt_execute($stmt2)){
                            echo "OK, company modified";
                            header("location: login.php");
                        }
                        else{
                            $register_err="Something went wrong during '$user' registration.";
                            echo "$register_err";
                        }
                        
                    }
                    else{
                        $register_err="Something went wrong during '$user' registration.";
                        echo "$register_err";
                    }
                    mysqli_stmt_close($stmt2);
                }
                else {header("location: login.php");}
            } 
            else{
                $register_err="Something went wrong during '$username' registration. Please try again later.";
                echo "$register_err";
            }

        }
        else{
                $register_err="Something went wrong during '$username' registration. Please try again later.";
                echo "$register_err";
            }
        
        // Close statement
        mysqli_stmt_close($stmt);

    }
    
    // Close connection
    mysqli_close($link);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Sign Up</h2>
        <p>Please fill this form to create an account.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <label>Username</label>
                <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                <span class="help-block"><?php echo $username_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>Password</label>
                <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>">
                <span class="help-block"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="container">

            <ul>

            <input type="radio" id="Investor" name="user" value="Investor">
            <label for="Investor">Investor</label>
            
            <div class="check"></div>

            <input type="radio" id="Company" name="user" value="Company">
            <label for="Company">Company</label>
            
            <div class="check"><div class="inside"></div></div>

            <input type="radio" id="Admin" name="user" value="Admin">
            <label for="Admin">Admin</label>
            <span class="help-block"><?php echo $user_err; ?></span>
            
            <div class="check"><div class="inside"></div></div>

            </ul>
            </div>

            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <input type="reset" class="btn btn-default" value="Reset">
            </div>
            <p>Already have an account? <a href="login.php">Login here</a>.</p>
        </form>
    </div>    
</body>
</html>


