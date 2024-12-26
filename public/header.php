<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo isset($title) ? htmlspecialchars($title) : "Crypto News"; ?> | Crypto News</title>
    <link rel="stylesheet" href="/assets/styles.css">
</head>
<body>
<header>
    <div class="header-inner">
        <h1><a href="index.php" class="no-underline">Crypto News</a></h1>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="news.php">News</a></li>
                <li><a href="prices.php">Prices</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="/about.php">About</a></li>
                <li><a href="/comments.php">Comments</a></li>
                <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                    <li><a href="/logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="/login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>
