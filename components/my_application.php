<?php
include_once '../core/dbConfig.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php'); // Redirect to login if not logged in
  exit();
}

$applicant_id = $_SESSION['user_id']; // The applicant's ID from session

// Fetch the application details for the logged-in applicant
$sql = "SELECT a.application_id, a.application_status, a.description, a.application_date, a.resume, jp.title AS job_title
        FROM applications a
        JOIN job_posts jp ON a.job_post_id = jp.job_post_id
        WHERE a.applicant_id = :applicant_id
        ORDER BY a.application_date DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute(['applicant_id' => $applicant_id]);

$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Applications</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.0.0/dist/tailwind.min.css" rel="stylesheet">
</head>

<?php include_once 'navbar.php'; ?>

<body class="max-w-7xl mx-auto ">


  <div class="max-w-6xl mx-auto p-6">
    <h1 class="text-4xl font-extrabold text-center text-indigo-600 mb-10">My Applications</h1>

    <?php if (empty($applications)): ?>
      <p class="text-center text-gray-500 text-lg">You have not applied for any jobs yet.</p>
    <?php else: ?>
      <!-- Table (Visible on medium and large screens) -->
      <div class="overflow-hidden rounded-lg shadow mb-8 md:block hidden">
        <table class="w-full text-left bg-white rounded-lg">
          <thead class="bg-indigo-500 text-white">
            <tr>
              <th class="py-3 px-6">Application ID</th>
              <th class="py-3 px-6">Job Title</th>
              <th class="py-3 px-6">Description</th>
              <th class="py-3 px-6">Status</th>
              <th class="py-3 px-6">Application Date</th>
              <th class="py-3 px-6">Resume</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <?php foreach ($applications as $application): ?>
              <tr class="hover:bg-gray-100 transition-colors duration-200">
                <td class="py-4 px-6"><?php echo htmlspecialchars($application['application_id']); ?></td>
                <td class="py-4 px-6"><?php echo htmlspecialchars($application['job_title']); ?></td>
                <td class="py-4 px-6"><?php echo nl2br(htmlspecialchars($application['description'])); ?></td>
                <td class="py-4 px-6 text-indigo-600 font-semibold">
                  <?php echo ucfirst(htmlspecialchars($application['application_status'])); ?>
                </td>
                <td class="py-4 px-6"><?php echo date('F j, Y, g:i a', strtotime($application['application_date'])); ?></td>
                <td class="py-4 px-6">
                  <a href="<?php echo htmlspecialchars($application['resume']); ?>" class="text-blue-500 hover:underline"
                    download>Download</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- Mobile View: Card Layout (Visible only on small screens) -->
      <div class="md:hidden space-y-4">
        <?php foreach ($applications as $application): ?>
          <div class="bg-white rounded-lg shadow p-4">
            <div class="flex justify-between items-center">
              <h2 class="text-lg font-bold text-gray-800">Application ID:</h2>
              <p class="text-sm text-gray-600"><?php echo htmlspecialchars($application['application_id']); ?></p>
            </div>
            <p class="mt-2"><span class="font-semibold">Job Title:</span>
              <?php echo htmlspecialchars($application['job_title']); ?></p>
            <p class="mt-2"><span class="font-semibold">Description:</span>
              <?php echo nl2br(htmlspecialchars($application['description'])); ?></p>
            <p class="mt-2"><span class="font-semibold">Status:</span>
              <span
                class="text-indigo-600"><?php echo ucfirst(htmlspecialchars($application['application_status'])); ?></span>
            </p>
            <p class="mt-2"><span class="font-semibold">Application Date:</span>
              <?php echo date('F j, Y, g:i a', strtotime($application['application_date'])); ?>
            </p>
            <div class="mt-2">
              <a href="<?php echo htmlspecialchars($application['resume']); ?>"
                class="text-blue-500 hover:underline font-semibold" download>Download Resume</a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

  </div>

</body>

</html>