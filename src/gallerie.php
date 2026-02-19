
<?php
include 'header.php';  // <-- this pulls in the header
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
                <!-- Image -->
                <img src="<?= htmlspecialchars($img['image_path']) ?>" alt="User Image">
                

                <!-- Image Info -->
                <div class="image-info">
                    <p class="username">by <strong><?= htmlspecialchars($img['username']) ?></strong></p>
                    <p class="date"><?= date("d M Y H:i", strtotime($img['created_at'])) ?></p>
                </div>

                <!-- Like / Comment Actions -->
                <div class="actions">
                    <!-- Like Button -->
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
                        <input type="hidden" name="image_id" value="<?= $img['id'] ?>">
                        <input type="text" name="comment" placeholder="Add a comment..." required>
                        <button type="submit">Post</button>
                    </form>

                    <!-- Existing comments -->
                    <div class="comments-list">
                        <?php
                        $comments = $mysqli->query("
                            SELECT c.comment, u.username
                            FROM comments c
                            JOIN users u ON c.user_id = u.id
                            WHERE c.image_id = {$img['id']}
                            ORDER BY c.created_at DESC
                        ");
                        while ($c = $comments->fetch_assoc()):
                        ?>
                            <p><strong><?= htmlspecialchars($c['username']) ?>:</strong>
                            <?= htmlspecialchars($c['comment']) ?></p>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    </div>
        
</body>

</html>