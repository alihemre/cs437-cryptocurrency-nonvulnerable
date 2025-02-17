<?php
include './header.php';

$title = "About";

// NON-VULNERABLE: KEEPING THE DATA IN SERVER MAKE THE ROLE OF THE USER UNCHANGABLE FROM CLIENT 
$userRole = $_SESSION['user_role'] ?? 'User';
?>

<main class="about-main">
    <h2>About Us</h2>
    <p>We are dedicated to bringing you the latest cryptocurrency news and price updates.</p>
    <p>Our mission is to simplify the complex world of digital assets for the everyday user.</p>

    <!-- Admin-Only Action -->
    <form method="POST">
        <button type="submit" name="admin_action">Perform Admin Task</button>
    </form>

    <?php
    // Process admin action
    if (isset($_POST['admin_action'])) {
        if ($userRole === 'Admin') {
            echo "<p style='color: red;'>Admin task performed! Logs cleared, settings updated.</p>";
        } else {
            echo "<p style='color: red;'>You are not authorized to perform this action!</p>";
        }
    }
    ?>
</main>

<?php include './footer.php'; ?>
