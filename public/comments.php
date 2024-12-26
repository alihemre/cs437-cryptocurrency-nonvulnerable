<!-- public/comment.php -->
<?php
include './header.php'; 
$title = "Comment";
// Database connection
$servername = "mysql-container";
$username = "root";
$password = "aliemre3169";
$dbname = "news_site";
$port = 3306;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);


// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
$sql = "CREATE TABLE IF NOT EXISTS comment (
  Name TEXT NULL,
  Message TEXT NULL
)";

// Execute the query to create the table
if ($conn->query($sql) === true) {
} else {
  echo "Error creating table: " . $conn->error;
}
// Ensure UTF-8 compatibility
$conn->set_charset("utf8mb4"); // UTF-8mb4 for full compatibility with special characters

//NON-VULNERABLE: WITH PARSING METHODS  Initialize variables
$name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : '';
$comment = isset($_POST['comment']) ? htmlspecialchars(trim($_POST['comment'])) : '';
$message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($name) && !empty($comment)) {
    $sql = "INSERT INTO comment (name, message) VALUES (?, ?)";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ss", $name, $comment);
        if ($stmt->execute()) {
            $message = "Thank you for your comment!";
        } else {
            $message = "Error saving your comment: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $message = "Error preparing the statement: " . $conn->error;
    }
}

// Fetch comments to display
$comments = [];
$sql = "SELECT * FROM comment;";
if ($result = $conn->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        $comments[] = $row;
    }
    $result->free();
} else {
    die("Query failed: " . $conn->error); // Display full query error for debugging
}

$conn->close();
?>

<style>
/* CSS for styling the comment form and list */
.comment-main {
    padding: 20px;
}

.comment-form {
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
}

.form-group input, .form-group textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.submit-button {
    padding: 10px 15px;
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.submit-button:hover {
    background-color: #0056b3;
}

.comment-list {
    margin-top: 20px;
}

.comment-item {
    margin-bottom: 15px;
    padding: 10px;
    border-bottom: 1px solid #ccc;
}

.comment-item strong {
    display: block;
    margin-bottom: 5px;
}
</style>

<main class="comment-main">
    <h2>Comment</h2>

    <?php if (!empty($message)): ?>
        
    <?php endif; ?>

    <form action="#" method="post" class="comment-form">
        <div class="form-group">
            <label for="name">Your Name:</label>
            <input type="text" id="name" name="name" required>
        </div>

        <div class="form-group">
            <label for="comment">Your Comment:</label>
            <textarea id="comment" name="comment" required></textarea>
        </div>

        <button type="submit" class="submit-button">Send</button>
    </form>

    <div class="comment-list">
        <h3>Previous Comments</h3>
        <?php if (!empty($comments)): ?>
            <?php foreach ($comments as $comment): ?>
                <div class="comment-item">
                <!-- NON-VULNERABLE: PARSE IT WHEN RENDERING THE COMMENTS -->
                    <strong><?php echo htmlspecialchars($comment['Name']); ?></strong>
                    <p><?php echo htmlspecialchars($comment['Message']); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No comments yet. Be the first to comment!</p>
        <?php endif; ?>
    </div>
</main>

<?php include './footer.php'; ?>
