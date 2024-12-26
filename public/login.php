<?php
session_start(); // Start the session

$servername = "mysql-container";
$username = "root";
$password = "aliemre3169";
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
    // NON-VULNERABLE:: Prevent SQL injection
    
    $email = $conn->real_escape_string($_POST['email']); 
    $password = $_POST['password'];

    // Secure SQL query using prepared statements
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // NON-VULNERABLE:Verify the password hash
        if (password_verify($password, $user['Password'])) {
            $_SESSION['logged_in'] = true;
            $_SESSION['user_email'] = $user['Email'];
            $_SESSION['user_role'] = $user['Role'];

            // Redirect to index.php
            header("Location: index.php");
            exit();
        } else {
            $error_message = "Invalid email or password.";
        }
    } else {
        $error_message = "Invalid email or password.";
    }
    $stmt->close();
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
