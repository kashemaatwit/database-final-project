<?php
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$ItemName = $Price  ="";
$name_err = $price_err ="";
 
// Processing form data when form is submitted
if(isset($_POST["OrderId"]) && !empty($_POST["OrderId"])){
    // Get hidden input value
    $OrderId = $_POST["OrderId"];
    
    // Validate name
    $input_name = trim($_POST["ItemName"]);
    if(empty($input_name)){
        $name_err = "Please enter Item's name.";
    } elseif(!filter_var($input_name, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/"))))
    {
        $name_err = "Please enter a valid name.";
    } else{
        $ItemName = $input_name;
    }
     
    
    // Validate Price
  $input_price = trim($_POST["Price"]);
    if(empty($input_price)){
        $price_err = "Please enter the price.";     
    } elseif(!ctype_digit($input_price)){
        $price_err = "Please enter a positive integer value.";
    } else{
        $Price = $input_price;
    }
 
    
    
    
    // Check input errors before inserting in database
   if(empty($name_err) && empty($price_err) )
   {
        // Prepare an update statement
        $sql = "UPDATE item SET ItemName=?, Price=? WHERE OrderId=?";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssi", $param_name,  $param_price,  $param_OrderId);
            
            // Set parameters
            $param_name = $ItemName;
            $param_price = $Price;
            $param_OrderId = $OrderId;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records updated successfully. Redirect to landing page
                header("location: item_index.php");
                exit();
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    // Close connection
    mysqli_close($link);
} else{
    // Check existence of id parameter before processing further
    if(isset($_GET["OrderId"]) && !empty(trim($_GET["OrderId"]))){
        // Get URL parameter
        $OrderId =  trim($_GET["OrderId"]);
        
        // Prepare a select statement
        $sql = "SELECT * FROM item WHERE OrderId = ?";
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "i", $param_OrderId);
            
            // Set parameters
            $param_OrderId = $OrderId;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                $result = mysqli_stmt_get_result($stmt);
    
                if(mysqli_num_rows($result) == 1){
                    /* Fetch result row as an associative array. Since the result set
                    contains only one row, we don't need to use while loop */
                    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                    
                    // Retrieve individual field value
                    $Name = $row["ItemName"];
                    $Price = $row["Price"];
                } else{
                    // URL doesn't contain valid id. Redirect to error page
                    header("location: item_error.php");
                    exit();
                }
                
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        
        // Close statement
        mysqli_stmt_close($stmt);
        
        // Close connection
        mysqli_close($link);
    }  else{
        // URL doesn't contain id parameter. Redirect to error page
        header("location: item_error.php");
        exit();
    }
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Record</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .wrapper{
            width: 600px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="mt-5">Update Record</h2>
                    <p>Please edit the input values and submit to update the item record.</p>
                    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
                        <div class="form-group">
                            <label>Item's Name</label>
                            <input type="text" name="ItemName" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $ItemName; ?>">
                            <span class="invalid-feedback"><?php echo $name_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Price</label>
                            <input type="text" name="Price" class="form-control <?php echo (!empty($price_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $Price; ?>">
                            <span class="invalid-feedback"><?php echo $price_err;?></span>
                        </div>
                        <input type="hidden" name="OrderId" value="<?php echo $OrderId; ?>"/>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="item_index.php" class="btn btn-secondary ml-2">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>
