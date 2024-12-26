<?php
session_start(); // Start the session

$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "news_site";
$port = 3306;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$sql = "CREATE TABLE IF NOT EXISTS users (
    Email TEXT NULL,
    Password TEXT NULL,
    Role TEXT NULL,
    Phone TEXT NULL
  )";
  
  // Execute the query to create the table
  if ($conn->query($sql) === true) {
  } else {
    echo "Error creating table: " . $conn->error;
  }

$error_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Vulnerable SQL query: Concatenate user inputs directly into the query
    $sql = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";

    // Execute the query
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['logged_in'] = true;
        $_SESSION['user_email'] = $user['Email'];
        $_SESSION['user_role'] = $user['Role'];
        $_COOKIES['user_role'] = $user['Role'];

        // Redirect to index.php
        header("Location: index.php");
        exit();
    } else {
        $error_message = "Invalid email or password.";
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #eef2f7;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }
        form {
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        form label {
            display: block;
            margin-top: 10px;
            font-weight: 600;
        }
        form input {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        form button {
            margin-top: 15px;
            padding: 10px 15px;
            background: #2980b9;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        form button:hover {
            background: #1f5f85;
        }
        form a {
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
            color: #2980b9;
        }
    </style>
</head>
<body>
    <h1>Login</h1>
    <form action="login.php" method="POST">
        <?php if ($error_message): ?>
            <p style="color: red;"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <label for="email">Email:</label>
        <input type="text" id="email" name="email" required>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <button type="submit">Login</button>
        <a href="signup.php">I do not have an account. Signup?</a>
    </form>
</body>
</html>
