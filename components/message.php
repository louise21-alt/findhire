<?php
include_once '../core/dbConfig.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

// Get the logged-in user's user_id from session
$senderId = $_SESSION['user_id'];

// Check if email is set in the session, if not, fetch it from the database
if (!isset($_SESSION['email'])) {
  // Fetch the user's email from the database based on user_id
  $sql = "SELECT email FROM users WHERE user_id = :user_id";
  $stmt = $pdo->prepare($sql);
  $stmt->bindParam(':user_id', $senderId, PDO::PARAM_INT);
  $stmt->execute();

  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user) {
    $_SESSION['email'] = $user['email']; // Store the email in the session
    $senderEmail = $user['email']; // Assign email to variable
  } else {
    echo "<p class='text-red-500'>User not found. Please log in again.</p>";
    exit();
  }
} else {
  $senderEmail = $_SESSION['email']; // If email is already in session
}

// Check if the receiver email and message are provided
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['receiver_email']) && isset($_POST['message'])) {
  $receiverEmail = $_POST['receiver_email'];
  $messageContent = $_POST['message'];

  // Fetch the receiver's user ID based on the email
  $query = "SELECT user_id FROM users WHERE email = :receiverEmail";
  $stmt = $pdo->prepare($query);
  $stmt->bindParam(':receiverEmail', $receiverEmail, PDO::PARAM_STR);
  $stmt->execute();

  if ($stmt->rowCount() > 0) {
    // Receiver found, get the user_id
    $receiver = $stmt->fetch(PDO::FETCH_ASSOC);
    $receiverId = $receiver['user_id'];

    // Insert the message into the messages table
    $insertQuery = "INSERT INTO messages (sender_id, receiver_id, message) VALUES (:senderId, :receiverId, :messageContent)";
    $insertStmt = $pdo->prepare($insertQuery);
    $insertStmt->bindParam(':senderId', $senderId, PDO::PARAM_INT);
    $insertStmt->bindParam(':receiverId', $receiverId, PDO::PARAM_INT);
    $insertStmt->bindParam(':messageContent', $messageContent, PDO::PARAM_STR);

    if ($insertStmt->execute()) {
      $success = true; // Flag to show the modal on success
    } else {
      $error = "Failed to send the message.";
    }
  } else {
    $error = "Receiver email not found in the database.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Send Message</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    // Function to redirect to the dashboard after modal closes
    function redirectToDashboard() {
      window.location.href = "<?php echo ($_SESSION['role'] === 'applicant') ? 'applicant_dashboard.php' : 'hr_dashboard.php'; ?>";
    }
  </script>
</head>

<body class="bg-gray-100 font-sans leading-normal tracking-normal h-screen flex items-center justify-center">

  <!-- Message form -->
  <div class="w-full max-w-2xl p-6 bg-white rounded-lg shadow-lg">
    <div class="flex items-center space-x-4 mb-6">
      <a href="<?php echo ($_SESSION['role'] === 'applicant') ? 'applicant_dashboard.php' : 'hr_dashboard.php'; ?>"
        class="text-blue-600 hover:underline text-2xl">←</a>
      <h1 class="text-3xl font-bold text-gray-700">Send Message</h1>
    </div>

    <form action="message.php" method="POST" class="space-y-4">
      <!-- Receiver email -->
      <div class="relative">
        <label for="receiver_email" class="text-gray-500">Receiver's Email:</label>
        <input type="email" name="receiver_email" id="receiver_email" required
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
      </div>

      <!-- Message text area -->
      <div>
        <label for="message" class="block text-gray-700 font-medium">Your Message:</label>
        <textarea name="message" id="message" rows="4" required
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
      </div>

      <button type="submit"
        class="w-full py-2 px-4 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500">
        Send Message
      </button>
    </form>
  </div>

  <!-- Success Modal -->
  <?php if (isset($success) && $success): ?>
    <div id="successModal" class="fixed inset-0 flex items-center justify-center bg-gray-500 bg-opacity-75">
      <div class="bg-white p-6 rounded-lg shadow-lg w-96 text-center">
        <h2 class="text-2xl font-bold text-green-600">Success!</h2>
        <p class="text-gray-700 mt-4">Your message was sent successfully.</p>
        <button onclick="redirectToDashboard()"
          class="mt-4 py-2 px-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
          Go to Dashboard
        </button>
      </div>
    </div>
  <?php endif; ?>

  <!-- Error Modal -->
  <?php if (isset($error)): ?>
    <div id="errorModal" class="fixed inset-0 flex items-center justify-center bg-gray-500 bg-opacity-75">
      <div class="bg-white p-6 rounded-lg shadow-lg w-96 text-center">
        <h2 class="text-2xl font-bold text-red-600">Error!</h2>
        <p class="text-gray-700 mt-4"><?php echo htmlspecialchars($error); ?></p>
        <button onclick="window.history.back()"
          class="mt-4 py-2 px-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
          Go Back
        </button>
      </div>
    </div>
  <?php endif; ?>

</body>

</html>