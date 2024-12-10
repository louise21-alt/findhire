<?php

$sql = "SELECT email FROM users WHERE user_id = " . $_SESSION['user_id'];
$stmt = $pdo->prepare($sql);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$email = $user['email'];

?>

<head>
  <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.js"></script>
</head>

<button data-drawer-target="default-sidebar" data-drawer-toggle="default-sidebar" aria-controls="default-sidebar"
  type="button"
  class="inline-flex items-center p-2 mt-2 ms-3 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
  <span class="sr-only">Open sidebar</span>
  <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
    <path clip-rule="evenodd" fill-rule="evenodd"
      d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z">
    </path>
  </svg>
</button>

<aside id="default-sidebar"
  class="fixed top-0 left-0 z-40 w-64 h-screen transition-transform -translate-x-full sm:translate-x-0"
  aria-label="Sidebar">
  <div class="h-full px-3 py-4 overflow-y-auto bg-gray-50 dark:bg-gray-800">
    <ul class="space-y-2 font-medium">
      <li>
        <a href="<?php echo ($_SESSION['role'] === 'applicant') ? 'applicant_dashboard.php' : 'hr_dashboard.php'; ?>"
          class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
          <svg
            class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 21">
            <path d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z" />
            <path
              d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z" />
          </svg>
          <span class="ms-3">Dashboard</span>
        </a>
      </li>
      <?php if ($_SESSION['role'] === 'hr'): ?>
        <li>
          <a href="add_job.php"
            class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
            <span class="ms-3">Add Job</span>
          </a>
        </li>
        <li>
          <a href="applicants.php"
            class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
            <span class="ms-3">Applications</span>
          </a>
        </li>
      <?php endif; ?>
      <?php if ($_SESSION['role'] === 'applicant'): ?>
        <li>
          <a href="my_application.php"
            class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
            <span class="ms-3">My Applications</span>
          </a>
        </li>
      <?php endif; ?>
      <li>
        <a href="inbox.php"
          class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
          <span class="ms-3">Inbox</span>
        </a>
      </li>
      <li>
        <button onclick="openModal()"
          class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
          <span class="ms-3">Signout</span>
        </button>
      </li>

      <!-- Confirmation Modal -->
      <div id="signoutModal" class="hidden fixed  bg-opacity-50 flex justify-center items-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-96 p-6">
          <h2 class="text-lg font-bold text-gray-900 dark:text-white">Confirm Sign Out</h2>
          <p class="text-gray-600 dark:text-gray-400 mt-2">
            Are you sure you want to sign out?
          </p>
          <div class="mt-4 flex justify-end space-x-4">
            <button onclick="closeModal()"
              class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
              Cancel
            </button>
            <a href="../logout.php" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
              Yes, Sign Out
            </a>
          </div>
        </div>
      </div>

      <script>
        // Function to open the modal
        function openModal() {
          document.getElementById('signoutModal').classList.remove('hidden');
        }

        // Function to close the modal
        function closeModal() {
          document.getElementById('signoutModal').classList.add('hidden');
        }
      </script>
    </ul>
  </div>
</aside>