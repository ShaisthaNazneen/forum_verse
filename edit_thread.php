<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$thread_id = $_GET['id'] ?? null;
if (!$thread_id) {
    die("Thread ID missing.");
}

$query = "SELECT * FROM threads WHERE thread_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $thread_id);
$stmt->execute();
$result = $stmt->get_result();
$thread = $result->fetch_assoc();

if (!$thread || $thread['user_id'] != $_SESSION['user_id']) {
    die("Unauthorized access.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $category_id = $_POST['category'];

    $update = $conn->prepare("UPDATE threads SET title = ?, content = ?, category_id = ? WHERE thread_id = ?");
    $update->bind_param("ssii", $title, $content, $category_id, $thread_id);
    $update->execute();

    header("Location: index.php");
    exit();
}

// Fetch categories
$categories = $conn->query("SELECT * FROM categories");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Thread</title>
</head>
<body>
    <h2>Edit Thread</h2>
    <form method="post">
        <input type="text" name="title" value="<?= htmlspecialchars($thread['title']); ?>" required><br>
        <textarea name="content" rows="5" required><?= htmlspecialchars($thread['content']); ?></textarea><br>
        <select name="category" required>
            <?php while ($cat = $categories->fetch_assoc()) : ?>
                <option value="<?= $cat['category_id']; ?>" <?= ($cat['category_id'] == $thread['category_id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']); ?>
                </option>
            <?php endwhile; ?>
        </select><br><br>
        <button type="submit">Update Thread</button>
    </form>
</body>
</html>
