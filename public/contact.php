<?php
include './header.php'; 
$title = "Contact";

// NON-VULNERABLE: Securely handling form input using htmlspecialchars to prevent XSS
$name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name']), ENT_QUOTES, 'UTF-8') : '';
$email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email']), ENT_QUOTES, 'UTF-8') : '';
$message = isset($_POST['message']) ? htmlspecialchars(trim($_POST['message']), ENT_QUOTES, 'UTF-8') : '';
?>

<main class="contact-main">
  <h2>Contact Us</h2>

  <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && $name && $email && $message): ?>
    <div class="user-input">
      <p>Thank you, <strong><?php echo $name; ?></strong>!</p>
      <p>We have received your message: <em><?php echo $message; ?></em></p>
    </div>
  <?php endif; ?>

  <form action="#" method="post" class="contact-form">
    <div class="form-group">
      <label for="name">Name:</label>
      <input type="text" id="name" name="name" required>
    </div>
  
    <div class="form-group">
      <label for="email">Email:</label>
      <input type="email" id="email" name="email" required>
    </div>
  
    <div class="form-group">
      <label for="message">Message:</label>
      <textarea id="message" name="message" required></textarea>
    </div>
  
    <button type="submit" class="submit-button">Send</button>
  </form>
</main>

<?php include './footer.php'; ?>
