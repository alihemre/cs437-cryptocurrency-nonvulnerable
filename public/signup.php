<?php
$title = "Home";
$servername = "mysql-container"; // Use 127.0.0.1 to avoid socket issues
$username = "root"; // Default MySQL username
$password = "aliemre3169"; // Leave blank if no password is set
$dbname = "news_site"; // Your database name
$port = 3306; // Default MySQL port

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
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
// Process form data when the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password']; // Store this password in plaintext

    // Vulnerable SQL query: Directly storing sensitive information in plaintext
    $sql = "INSERT INTO users (email, phone, password) VALUES ('$email', '$phone', '$password')";

    if ($conn->query($sql) === true) {
        echo "Kayıt başarılı! ";
    } else {
        echo "Hata: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
<style>
  /* Specific styles for login and signup pages */
  #login-page, #signup-page {
    font-family: Arial, sans-serif;
    background-color: #eef2f7;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    margin: 0;
    padding: 20px;
  }

  #login-page h1, #signup-page h1 {
    margin-bottom: 20px;
    color: #2c3e50;
  }

  #login-form, #signup-form {
    background: #ffffff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 400px;
    text-align: center; /* Centers the button inside the form */
  }

  #login-form label, #signup-form label {
    display: block;
    margin-top: 10px;
    font-weight: 600;
    color: #333;
    text-align: left;
  }

  #login-form input, #signup-form input {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    border: 1px solid #ccc;
    border-radius: 4px;
  }

  #login-form button, #signup-form button {
    margin-top: 15px;
    padding: 10px 15px;
    background: #2980b9;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 600;
    display: inline-block; /* Ensures proper alignment */
    margin: 0 auto; /* Center-aligns the button */
  }

  #login-form button:hover, #signup-form button:hover {
    background: #1f5f85;
  }

  #login-form a, #signup-form a {
    display: inline-block;
    margin-top: 15px;
    text-decoration: none;
    color: #2980b9;
    font-weight: 500;
    transition: color 0.3s;
  }

  #login-form a:hover, #signup-form a:hover {
    color: #1f5f85;
  }
</style>
<body id="signup-page">
    <h1>Signup</h1>
    <form id="signup-form" action="#" method="POST">
        <label for="email">Email:</label>
        <input type="text" id="email" name="email" ><br>
        <label for="phone">Phone:</label>
        <input type="phone" id="phone" name="phone" ><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" ><br>

        <button type="submit">Signup</button>
        <a href="login.php">Already have an account? Login</a>
    </form>
</body>
<?php include '../includes/footer.php'; ?>
