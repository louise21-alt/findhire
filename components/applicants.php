<?php
include_once '../core/dbConfig.php';

// Fetch applications based on status
$statuses = ['pending', 'accepted', 'rejected'];

$applications = [];
foreach ($statuses as $status) {
  $sql = "SELECT a.application_id, a.applicant_id, a.job_post_id, a.description, a.application_status, a.application_date, a.resume, u.username, jp.title 
            FROM applications a 
            INNER JOIN users u ON a.applicant_id = u.user_id 
            INNER JOIN job_posts jp ON a.job_post_id = jp.job_post_id 
            WHERE a.application_status = :status
            ORDER BY a.application_date DESC";

  $stmt = $pdo->prepare($sql);
  $stmt->execute(['status' => $status]);

  if ($stmt->rowCount() > 0) {
    $applications[$status] = $stmt->fetchAll(PDO::FETCH_ASSOC);
  } else {
    $applications[$status] = [];
  }
}

// Handle action to update application status via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
  $application_id = $_POST['application_id'];
  $action = $_POST['action'];  // accepted, rejected, or pending

  // Update application status in the database
  $updateSql = "UPDATE applications SET application_status = :status WHERE application_id = :application_id";
  $updateStmt = $pdo->prepare($updateSql);
  $updateStmt->execute(['status' => $action, 'application_id' => $application_id]);

  // Redirect to the applicants page after the action
  header('Location: applicants.php');
  exit(); // Make sure the script stops after redirect
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Applications</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.2/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

</head>

<body class="max-w-7xl mx-auto">
  <?php include_once 'navbar.php'; ?>

  <div class="container mx-auto py-8 px-4">
    <h1 class="text-4xl font-extrabold text-center text-indigo-600 mb-10">Applications</h1>

    <!-- Display Pending Applications -->
    <section class="mb-12">
      <h2 class="text-3xl font-bold text-gray-700 mb-6">Pending Applications</h2>
      <?php if (!empty($applications['pending'])): ?>
        <div class="overflow-x-auto bg-white shadow-md rounded-lg">
          <table class="min-w-full table-auto">
            <thead class="bg-indigo-500 text-white">
              <tr>
                <th class="py-3 px-4 text-left">Application ID</th>
                <th class="py-3 px-4 text-left">Applicant</th>
                <th class="py-3 px-4 text-left">Job Title</th>
                <th class="py-3 px-4 text-left">Description</th>
                <th class="py-3 px-4 text-left">Status</th>
                <th class="py-3 px-4 text-left">Application Date</th>
                <th class="py-3 px-4 text-left">Resume</th>
                <th class="py-3 px-4 text-center">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <?php foreach ($applications['pending'] as $application): ?>
                <tr class="hover:bg-gray-100 transition-colors duration-200">
                  <td class="py-3 px-4"><?php echo htmlspecialchars($application['application_id']); ?></td>
                  <td class="py-3 px-4"><?php echo htmlspecialchars($application['username']); ?></td>
                  <td class="py-3 px-4"><?php echo htmlspecialchars($application['title']); ?></td>
                  <td class="py-3 px-4"><?php echo nl2br(htmlspecialchars($application['description'])); ?></td>
                  <td class="py-3 px-4 capitalize text-indigo-600">
                    <?php echo htmlspecialchars($application['application_status']); ?>
                  </td>
                  <td class="py-3 px-4"><?php echo date('F j, Y, g:i a', strtotime($application['application_date'])); ?>
                  </td>
                  <td class="py-3 px-4">
                    <a href="<?php echo htmlspecialchars($application['resume']); ?>" class="text-blue-500 hover:underline"
                      download>Download Resume</a>
                  </td>
                  <td class="py-3 px-4 text-center">
                    <form method="POST" class="inline" id="application-form-<?php echo $application['application_id']; ?>">
                      <input type="hidden" name="application_id" value="<?php echo $application['application_id']; ?>">
                      <input type="hidden" name="action" value=""> <!-- Hidden action input -->

                      <?php if ($application['application_status'] == 'pending'): ?>
                        <button type="button"
                          onclick="confirmAction('accepted', <?php echo $application['application_id']; ?>)"
                          class="text-green-600 hover:text-green-800 fa-lg">
                          <i class="fas fa-check-circle"></i> <!-- Accept Icon -->
                        </button>
                        <button type="button"
                          onclick="confirmAction('rejected', <?php echo $application['application_id']; ?>)"
                          class="text-red-600 hover:text-red-800 fa-lg">
                          <i class="fas fa-times-circle"></i> <!-- Reject Icon -->
                        </button>
                      <?php endif; ?>

                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <p class="text-gray-600 text-lg text-center mt-6">No pending applications at the moment.</p>
      <?php endif; ?>
    </section>

    <!-- Modal for Confirmation -->
    <div id="confirmation-modal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden">
      <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
        <h2 class="text-xl font-semibold mb-4 text-center" id="modal-message">Are you sure?</h2>
        <div class="flex justify-center items-center gap-4">
          <button id="confirm-button" class="bg-green-500 text-white py-2 px-4 rounded-lg">Yes</button>
          <button id="cancel-button" class="bg-red-500 text-white py-2 px-4 rounded-lg">No</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    function confirmAction(action, applicationId) {
      // Show modal
      const modal = document.getElementById('confirmation-modal');
      const modalMessage = document.getElementById('modal-message');
      const confirmButton = document.getElementById('confirm-button');
      const cancelButton = document.getElementById('cancel-button');

      modal.classList.remove('hidden');
      modalMessage.textContent = `Are you sure you want to ${action} this application?`;

      // Handle confirm action
      confirmButton.onclick = function () {
        // Set the action field value
        const form = document.getElementById('application-form-' + applicationId);
        form.elements['action'].value = action;

        // Submit the form
        form.submit();

        // Hide the modal after submission
        modal.classList.add('hidden');
      };

      // Close the modal on cancel
      cancelButton.onclick = function () {
        modal.classList.add('hidden');
      };
    }
  </script>
</body>

</html>