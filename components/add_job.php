<?php

require_once '../core/dbConfig.php';

if ($_SESSION['role'] != 'hr') {
  header('Location: index.php');
  exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $title = $_POST['title'];
  $description = $_POST['description'];
  $hr_id = $_SESSION['user_id'];

  // Prepare the SQL statement
  $sql = "INSERT INTO job_posts (title, description, created_by) VALUES (:title, :description, :created_by)";

  // Prepare the statement
  $stmt = $pdo->prepare($sql);

  // Bind parameters
  $stmt->bindParam(':title', $title, PDO::PARAM_STR);
  $stmt->bindParam(':description', $description, PDO::PARAM_STR);
  $stmt->bindParam(':created_by', $hr_id, PDO::PARAM_INT);

  // Execute the statement
  if ($stmt->execute()) {
    header('Location: hr_dashboard.php');
    exit();
  } else {
    echo "Error creating job post";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 font-sans leading-normal tracking-normal h-screen">

  <?php include_once 'navbar.php'; ?>


  <div class="w-full max-w-3xl p-6 bg-white mx-auto rounded-lg shadow-lg mt-10">
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-2xl font-bold text-gray-700">Add Job</h2>
    </div>

    <form method="POST" class="space-y-4">
      <div>
        <label for="title" class="block text-gray-700 text-sm font-medium mb-2">Job Title</label>
        <input type="text" name="title" id="title" placeholder="Enter job title"
          class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
          required>
      </div>

      <div>
        <label for="description" class="block text-gray-700 text-sm font-medium mb-2">Job Description</label>
        <textarea name="description" id="description" placeholder="Enter job description"
          class="w-full px-4 py-3 text-sm text-gray-700 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          required></textarea>
      </div>

      <div class="flex justify-center">
        <button type="submit"
          class="w-full px-6 py-3 text-white bg-blue-600 rounded-lg shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
          Post Job
        </button>
      </div>
    </form>
  </div>

</body>

</html>