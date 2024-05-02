<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "teamwork";

// Create connection
$link = mysqli_connect($servername, $username, $password, $dbname) or die("unable to connect");

echo "Great Work!!! The database connection is successful";

// Check connection
if (!$link) {
    die("Connection failed: " . mysqli_connect_error());
}

$operation_val = '';
if (isset($_POST['operation'])) {
    $operation_val = $_POST["operation"];
}

// User signup function
function signupUser($link, $username, $password) {
    $id = getId($link);
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $query = "INSERT INTO users (userID, userName, userPassword) VALUES ('$id', '$username', '$hashedPassword')";
    return mysqli_query($link, $query);
}

// Create id for sign up
function getId($link) {
   
    $queryMaxID = "SELECT MAX(userID) FROM users;";
    $resultMaxID = mysqli_query($link, $queryMaxID);
    $row = mysqli_fetch_array($resultMaxID, MYSQLI_NUM);
    return $row[0]+1;
 }

// User login function
function loginUser($link, $username, $password) {
    $query = "SELECT * FROM users WHERE username='$username'";
    $result = mysqli_query($link, $query);
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            return true;
        }
    }
    return false;
}

// User logout function
function logoutUser() {
    session_unset();
    session_destroy();
}

// Handle signup form submission
if (isset($_POST['signupbtn'])) {
    $username = $_POST['signup_username'];
    $password = $_POST['signup_password'];
    if (signupUser($link, $username, $password)) {
        header("Location: main.html"); // Redirect to main page after signup
        exit();
    } else {
        echo "Error signing up user: " . mysqli_error($link);
    }
}

// Handle login form submission
if (isset($_POST['loginbtn'])) {
    $username = $_POST['login_username'];
    $password = $_POST['login_password'];
    if (loginUser($link, $username, $password)) {
        header("Location: main.html"); // Redirect to main page after login
        exit();
    } else {
        echo "Invalid username or password.";
    }
}

// Handle logout button click
if (isset($_POST['logoutbtn'])) {
    logoutUser();
    header("Location: index.php"); // Redirect to index page after logout
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynamic Website</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input[type="text"],
        .form-group input[type="password"] {
            width: 100%;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .form-group input[type="submit"] {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: none;
            background-color: #4caf50;
            color: #fff;
            cursor: pointer;
        }

        .form-group input[type="submit"]:hover {
            background-color: #45a049;
        }

        .message {
            margin-bottom: 15px;
            color: #ff0000;
        }

        .logout-btn {
            display: block;
            width: 100px;
            margin: 20px auto;
            padding: 10px;
            border-radius: 5px;
            border: none;
            background-color: #f44336;
            color: #fff;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
        }

        .logout-btn:hover {
            background-color: #d32f2f;
        }

        /* Main page styles */
        .main-content {
            text-align: center;
        }

        .circle {
            width: 200px;
            height: 200px;
            background-color: #4caf50;
            border-radius: 50%;
            margin: 50px auto;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
            font-size: 24px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Signup Form -->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="form-group">
            <label for="signup_username">Username:</label>
            <input type="text" id="signup_username" name="signup_username" required>
            <label for="signup_password">Password:</label>
            <input type="password" id="signup_password" name="signup_password" required>
            <input type="submit" name="signupbtn" value="Sign Up" >
        </form>

        <!-- Login Form -->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="form-group">
            <label for="login_username">Username:</label>
            <input type="text" id="login_username" name="login_username" required>
            <label for="login_password">Password:</label>
            <input type="password" id="login_password" name="login_password" required>
            <input type="submit" name="loginbtn" value="Login">
        </form>

        <!-- Logout Button -->
        <a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="logout-btn" name="logoutbtn">Log Out</a>

        <!-- PHP Messages -->
        <div class="message">
            <?php
            if (isset($_POST['signupbtn']) || isset($_POST['loginbtn']) || isset($_POST['logoutbtn'])) {
                echo "User feedback message here.";
            }
            ?>
        </div>
    </div>

</body>

</html>
