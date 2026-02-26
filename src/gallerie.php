<?php
include 'header.php';  
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 5; 
$offset = ($page - 1) * $limit;

$stmt = $pdo->prepare("
    SELECT images.*, users.username, 
           (SELECT COUNT(*) FROM likes WHERE likes.image_id = images.id) AS like_count,
            (SELECT COUNT(*) FROM comments WHERE comments.image_id = images.id) AS comment_count
    FROM images
    JOIN users ON images.user_id = users.id
    ORDER BY images.created_at DESC
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$images = $stmt->fetchAll();

$totalStmt = $pdo->query("SELECT COUNT(*) FROM images");
$totalImages = $totalStmt->fetchColumn();
$totalPages = ceil($totalImages / $limit);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Camagru</title>
    <link rel="stylesheet" href="../public/assets/css/user.css">
    <script src="../public/assets/js/script.js"></script> 


</head>
<body>
    <div id="gallery" class="gallery">
        <?php foreach ($images as $img): ?>
            <div class="image-card">
                <img src="<?= htmlspecialchars($img['image_path']) ?>" alt="User Image">
                

                <div class="image-info">
                    <p class="username">by <strong><?= htmlspecialchars($img['username']) ?></strong></p>
                    <p class="date"><?= date("d M Y H:i", strtotime($img['created_at'])) ?></p>
                </div>

                <div class="actions">
                    <form class="like-form" method="post" action="like.php">
                        
                        <input type="hidden" name="image_id" value="<?= $img['id'] ?>">
                        <?php if ($user != null): ?>
                            <button type="submit" class="like-btn">
                                ❤️ <span class="like-count"><?= $img['like_count'] ?? 0 ?></span>
                            </button>
                        <?php endif; ?>
                    </form>

                    <!-- Comment Button (toggles the comment section) -->
                    <?php if ($user != null): ?>
                        <button class="comment-btn" onclick="toggleCommentsSection(<?= $img['id'] ?>)">
                            💬 <?= $img['comment_count'] ?? 0 ?>
                        </button>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $img['user_id']): ?>
                    <form action="delete_image.php" method="POST" style="display:inline;">
                        <input type="hidden" name="image_id" value="<?= $img['id'] ?>">
                        <button type="submit" class="like-count"
                            onclick="return confirm('Are you sure you want to delete this image?');">
                            🗑️
                        </button>
                    </form>
                <?php endif; ?>
                </div>
                
                <!-- Comment Section (form + existing comments) -->
                <div class="comments-section" id="comments-section-<?= $img['id'] ?>" style="display:none;">
    
                    <!-- Add new comment -->
                    <form method="post" action="comment.php">
                        <input type="hidden" name="image_id" value="<?= htmlspecialchars($img['id']) ?>">
                        <input type="text" name="comment" placeholder="Add a comment..." required>
                        <button type="submit">Post</button>
                    </form>

                    <!-- Existing comments -->
                    <div class="comments-list">
                        <?php
                        $stmt = $pdo->prepare("
                            SELECT c.comment, u.username
                            FROM comments c
                            JOIN users u ON c.user_id = u.id
                            WHERE c.image_id = ?
                            ORDER BY c.created_at DESC
                        ");
                        $stmt->execute([$img['id']]);
                        $comments = $stmt->fetchAll();

                        if ($comments):
                            foreach ($comments as $c):
                        ?>
                                <p>
                                    <strong><?= htmlspecialchars($c['username']) ?>:</strong>
                                    <?= htmlspecialchars($c['comment']) ?>
                                </p>
                        <?php
                            endforeach;
                        else:
                        ?>
                            <p>No comments yet.</p>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>">&laquo; Previous</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>" class="<?= ($i == $page) ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page + 1 ?>">Next &raquo;</a>
        <?php endif; ?>
    </div>

    <footer id="mainFooter">
        <div class="header">
            <p>&copy; 2026 Camagru. All rights reserved.</p>
        </div>
    </footer>
</body>

</html>