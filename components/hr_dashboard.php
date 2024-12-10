<?php
include_once '../core/dbConfig.php';

// Fetch all job posts along with the username of the creator
$sql = "SELECT jp.job_post_id, jp.title, jp.description, jp.created_at, u.username, u.email
        FROM job_posts jp 
        INNER JOIN users u ON jp.created_by = u.user_id 
        ORDER BY jp.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute();

// Check if there are any job posts
if ($stmt->rowCount() > 0) {
  // Fetch all results
  $jobPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
  $jobPosts = [];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Job Posts</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.2/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="max-w-7xl mx-auto">

  <?php include_once 'navbar.php'; ?>

  <div class="container mx-auto px-8 py-12">
    <h1 class="text-4xl font-extrabold text-center text-gray-800 mb-8">Job Posts</h1>

    <!-- Display job posts -->
    <?php if (!empty($jobPosts)): ?>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($jobPosts as $post): ?>
          <div
            class="bg-white p-6 rounded-xl shadow-lg hover:shadow-2xl transition-shadow duration-300 ease-in-out border border-gray-200">
            <h2 class="text-xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($post['title']); ?></h2>
            <p class="text-gray-700 text-sm mb-4 line-clamp-3"><?php echo nl2br(htmlspecialchars($post['description'])); ?>
            </p>
            <div class="text-sm text-gray-500 space-y-1 mb-4">
              <p>Posted by: <span class="font-medium"><?php echo htmlspecialchars($post['username']); ?></span></p>
              <p>Posted on: <span><?php echo date('F j, Y, g:i a', strtotime($post['created_at'])); ?></span></p>
              <p>Contact: <span class="text-blue-600"><?php echo htmlspecialchars($post['email']); ?></span></p>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="text-center text-gray-600 mt-12">No job posts available at the moment.</p>
    <?php endif; ?>

  </div>

</body>

</html>