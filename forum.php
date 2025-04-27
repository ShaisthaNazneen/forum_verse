<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/header.php';
require_once 'includes/functions.php';

$threads_per_page = 5;
$page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
$offset = ($page - 1) * $threads_per_page;

$search = mysqli_real_escape_string($conn, $_GET['search'] ?? '');
$category_filter = mysqli_real_escape_string($conn, $_GET['category'] ?? '');

$conditions = [];
if (!empty($search)) {
    $conditions[] = "(t.title LIKE '%$search%' OR t.content LIKE '%$search%' OR c.name LIKE '%$search%')";
}
if (!empty($category_filter)) {
    $conditions[] = "t.category_id = '$category_filter'";
}
$where_clause = $conditions ? "WHERE " . implode(" AND ", $conditions) : "";

$query = "SELECT SQL_CALC_FOUND_ROWS t.thread_id, t.title, t.username, t.created_at, t.content, t.user_id, c.name AS category_name
          FROM threads t
          LEFT JOIN categories c ON t.category_id = c.category_id
          $where_clause
          ORDER BY t.created_at DESC
          LIMIT $threads_per_page OFFSET $offset";
$result = mysqli_query($conn, $query);

$count_result = mysqli_query($conn, "SELECT FOUND_ROWS() AS total_threads");
$total_threads = mysqli_fetch_assoc($count_result)['total_threads'] ?? 0;
$total_pages = ceil($total_threads / $threads_per_page);

$category_result = mysqli_query($conn, "SELECT * FROM categories");
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<style>
body {
    background: #f4f7f9;
}
.thread-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    padding: 20px;
    margin-bottom: 20px;
}
.thread-header {
    display: flex;
    align-items: center;
    gap: 15px;
}
.thread-header img {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
}
.thread-actions {
    margin-top: 10px;
}
</style>

<div class="container mt-5">
    <h2 class="mb-4">🧵 All Threads</h2>

    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="mb-4">
            <h4>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h4>
            <a href="create_thread.php" class="btn btn-primary">Create New Thread</a>
        </div>
    <?php endif; ?>

    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-5">
            <input type="text" name="search" class="form-control" placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <div class="col-md-4">
            <select name="category" class="form-select">
                <option value="">All Categories</option>
                <?php while ($cat = mysqli_fetch_assoc($category_result)) : ?>
                    <option value="<?= $cat['category_id']; ?>" <?= $category_filter == $cat['category_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-md-3">
            <button class="btn btn-primary w-100">Search</button>
        </div>
    </form>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <?php
            $avatar = getUserAvatar($conn, $row['username']);
            $likeCount = getLikeCount($conn, $row['thread_id']);
            $userHasLiked = isset($_SESSION['user_id']) ? userHasLiked($conn, $row['thread_id'], $_SESSION['user_id']) : false;
            $commentCount = getCommentCount($conn, $row['thread_id']);
            $tags = getTagsForThread($conn, $row['thread_id']);
            ?>
            <div class="thread-card">
                <div class="thread-header mb-2">
                    <img src="<?= $avatar; ?>" alt="Avatar" onerror="this.src='images/default-avatar.png';">
                    <div>
                        <h5>
                            <a href="thread.php?id=<?= $row['thread_id']; ?>" class="text-decoration-none text-dark">
                                <?= htmlspecialchars($row['title']); ?>
                            </a>
                        </h5>
                        <small>By <strong><?= htmlspecialchars($row['username']); ?></strong> | <?= date("d M Y, h:i A", strtotime($row['created_at'])); ?></small>
                    </div>
                </div>
                <div>
                    <?php foreach ($tags as $tag): ?>
                        <span class="badge bg-info text-dark">#<?= htmlspecialchars($tag); ?></span>
                    <?php endforeach; ?>
                </div>
                <p class="mt-2"><?= nl2br(htmlspecialchars($row['content'])); ?></p>
                <div class="text-muted">
                    💬 <?= $commentCount; ?> comments |
                    ❤️ <span id="like-count-<?= $row['thread_id']; ?>"><?= $likeCount; ?></span> likes |
                    <span class="badge bg-light text-dark"><?= htmlspecialchars($row['category_name'] ?? 'Uncategorized'); ?></span>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <span class="like-icon" onclick="toggleLike(<?= $row['thread_id']; ?>)">
                            <?= $userHasLiked ? '💙' : '👍'; ?>
                        </span>
                    <?php endif; ?>
                </div>
                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $row['user_id']): ?>
                    <div class="thread-actions mt-2">
                        <a href="edit_thread.php?id=<?= $row['thread_id']; ?>" class="btn btn-sm btn-outline-primary">✏️ Edit</a>
                        <a href="delete_thread.php?id=<?= $row['thread_id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this thread?');">🗑️ Delete</a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>

        <nav>
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : ''; ?>">
                        <a class="page-link" href="?<?= http_build_query(['search' => $search, 'category' => $category_filter, 'page' => $i]); ?>"><?= $i; ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php else: ?>
        <div class="alert alert-warning">No threads found.</div>
    <?php endif; ?>
</div>

<script>
function toggleLike(threadId) {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "like_handler.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onload = function () {
        if (xhr.status == 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.success) {
                document.getElementById(`like-count-${threadId}`).innerText = response.likeCount;
                document.querySelector(`.like-icon[onclick="toggleLike(${threadId})"]`).innerHTML = response.userHasLiked ? '💙' : '👍';
            }
        }
    };
    xhr.send("thread_id=" + threadId);
}
</script>

<?php include 'includes/footer.php'; ?>
