<!-- public/contact.php -->
<?php
include './header.php'; 
$title = "Contact";

// Insecurely handling form input
$name = isset($_POST['name']) ? $_POST['name'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';
$message = isset($_POST['message']) ? $_POST['message'] : '';
?>

<main class="contact-main">
  <h2>Contact Us</h2>

  <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
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
