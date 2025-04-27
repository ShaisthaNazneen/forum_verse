<?php
session_start();
include 'includes/db.php';  // Include your database connection
include 'includes/auth.php'; // User authentication check
include 'includes/header.php'; // Include the header file

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

// Fetch categories from the database for the category dropdown
$category_query = "SELECT * FROM categories"; // Assuming you have a 'categories' table
$category_result = mysqli_query($conn, $category_query);

// Check if the query was successful
if (!$category_result) {
    die('Error fetching categories: ' . mysqli_error($conn)); // Handle any SQL errors
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $category_id = $_POST['category_id']; // Get selected category
    $user_id = $_SESSION['user_id']; // Get the logged-in user's ID
    $username = $_SESSION['username']; // Get the logged-in user's username
    $query = "INSERT INTO threads (user_id, username, title, content) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);

if (!$stmt) {
    die("SQL prepare failed: " . $conn->error);
}
    // Prepare SQL statement to insert the thread into the database
    $stmt = $conn->prepare("INSERT INTO threads (user_id, username, title, content, category_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isssi", $user_id, $username, $title, $content, $category_id); // Bind the parameters

    // Execute the statement and check for success
    if ($stmt->execute()) {
        // Success message
        echo "Thread created successfully.";
        // Redirect to the index page after successful creation
        header("Location: index.php");
        exit();
    } else {
        // Error message if there is a failure in executing the statement
        echo "Error: " . $stmt->error;
    }

    $stmt->close(); // Close the statement
}

?>

<!-- Form for creating a new thread -->
<div class="card shadow p-4">
    <h2>Create New Thread</h2>
    <form method="post">
        <div class="mb-3">
            <label class="form-label">Title:</label>
            <input class="form-control" type="text" name="title" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Content:</label>
            <textarea class="form-control" name="content" rows="6" required></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Category:</label>
            <select class="form-control" name="category_id" required>
                <option value="">Select Category</option>
                <?php
                // Check if categories are fetched successfully
                if (mysqli_num_rows($category_result) > 0) {
                    // Loop through the categories and display them in the dropdown
                    while ($category = mysqli_fetch_assoc($category_result)) {
                        echo "<option value='" . $category['category_id'] . "'>" . htmlspecialchars($category['name']) . "</option>";
                    }
                } else {
                    echo "<option value=''>No categories available</option>";
                }
                ?>
            </select>
        </div>
        <button class="btn btn-primary" type="submit">Post Thread</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>



