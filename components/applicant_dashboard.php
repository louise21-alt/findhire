<?php
include_once '../core/dbConfig.php';

// Check if user is an applicant
if ($_SESSION['role'] != 'applicant') {
  header('Location: index.php');
  exit();
}

// Fetch all job posts along with the username of the creator
$sql = "SELECT jp.job_post_id, jp.title, jp.description, jp.created_at, u.username, u.email
        FROM job_posts jp 
        INNER JOIN users u ON jp.created_by = u.user_id 
        ORDER BY jp.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute();

// Check if there are any job posts
if ($stmt->rowCount() > 0) {
  $jobPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
  $jobPosts = [];
}

// Handle job application
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['apply'])) {
  $job_post_id = $_POST['job_post_id'];
  $applicant_id = $_SESSION['user_id'];

  // Insert the application into the database
  $applySql = "INSERT INTO applications (job_post_id, applicant_id) VALUES (?, ?)";
  $applyStmt = $pdo->prepare($applySql);
  $applyStmt->execute([$job_post_id, $applicant_id]);

  // Redirect to avoid reapplying on refresh
  header("Location: applicant_dashboard.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Applicant Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.0.0/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="max-w-7xl mx-auto">

  <?php include_once 'navbar.php'; ?>

  <div class="max-w-6xl mx-auto p-8">
    <h1 class="text-4xl font-extrabold text-center text-indigo-600 mb-10">Job Posts</h1>

    <!-- Display job posts -->
    <?php if (!empty($jobPosts)): ?>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($jobPosts as $post): ?>
          <div
            class="bg-white border border-gray-200 p-6 rounded-lg shadow hover:shadow-lg transition-shadow duration-300 ease-in-out">
            <h2 class="text-xl font-bold text-gray-800 mb-3">
              <?php echo htmlspecialchars($post['title']); ?>
            </h2>
            <p class="text-gray-600 mb-4 line-clamp-3">
              <?php echo nl2br(htmlspecialchars($post['description'])); ?>
            </p>
            <p class="text-sm text-gray-500 mb-2">Posted by:
              <span class="font-medium text-gray-700"><?php echo htmlspecialchars($post['username']); ?></span>
            </p>
            <p class="text-sm text-gray-500 mb-2">Posted on:
              <span class="font-medium"><?php echo date('F j, Y, g:i a', strtotime($post['created_at'])); ?></span>
            </p>
            <p class="text-sm text-gray-500">Contact:
              <span class="font-medium text-gray-700"><?php echo htmlspecialchars($post['email']); ?></span>
            </p>

            <!-- Apply Button -->
            <div class="mt-6">
              <form method="POST">
                <input type="hidden" name="job_post_id" value="<?php echo $post['job_post_id']; ?>">
                <a href="apply_job.php?job_post_id=<?php echo $post['job_post_id']; ?>"
                  class="inline-block w-full text-center py-2 px-4 bg-indigo-500 text-white font-semibold rounded-lg hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                  Apply for Job
                </a>
              </form>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="text-center text-gray-600 text-lg">No job posts available at the moment.</p>
    <?php endif; ?>
  </div>

</body>

</html>