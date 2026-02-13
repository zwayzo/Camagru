<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$mysqli = require"../config/config.php";

session_start();
$successMessage = $_SESSION['succes'] ?? null;
unset($_SESSION['succes']);
if (!isset($_SESSION['user_id']) && !isset($_SESSION['username'])) {
    $_SESSION['register_error'] = 'The user should be authenticated';
    header("Location: ../public/index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($user_id) {
    // fetch the full user row
    $stmt = $mysqli->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();
} else {
    $user = null;
}


$sql = "
SELECT 
    images.id,
    images.image_path,
    images.created_at,
    users.username,
    COUNT(DISTINCT likes.id) AS like_count,
    COUNT(DISTINCT comments.id) AS comment_count
FROM images
JOIN users ON images.user_id = users.id
LEFT JOIN likes ON images.id = likes.image_id
LEFT JOIN comments ON images.id = comments.image_id
GROUP BY images.id
ORDER BY images.created_at DESC
";

$result = $mysqli->query($sql);
if (!$result) {
    die("SQL Error: " . $mysqli->error);
}

$images = [];
while ($row = $result->fetch_assoc()) {
    $images[] = $row;
}




?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Camagru</title>
    <link rel="stylesheet" href="../public/assets/css/user.css">
    <!-- <script src="../public/assets/js/script.js"></script>  -->

</head>
<body>
    <div id="mainContent">
        <div class="header">
        <?php if($successMessage): ?>
            <p id="flash-message" class="success-message"><?= htmlspecialchars($successMessage) ?></p>
        <?php endif; ?>
            <h1>Camagru</h1>

            <div class="header-actions">
                <div id="upload-section">
                    <input type="file" id="file-input" accept="image/*" style="display: none;">
                    <button class="verify-email-btn" id="upload-btn">Upload image</button>
                </div>
                <div id="canvas-container" style="display: none;">
                    <canvas id="canvas"></canvas>
                </div>
                

                <div id="stickers-panel" style="display:none;">
                    <h3>Add a sticker:</h3>
                    <button class="sticker-btn" data-src="../public/assets/stickers/sticker1.webp">👑</button>
                    <button class="sticker-btn" data-src="../public/assets/stickers/sticker2.avif">😎</button>
                    <button class="sticker-btn" data-src="../public/assets/stickers/sticker3.png">🔥</button>
                </div>

                <div id="save-section" style="display:none;">
                    <button id="save-btn">Save Image</button>
                </div>




                <button type="button" class="verify-email-btn">Upload image</button>
                <form action="enable.php" method="post">
                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                    <button type="submit" class="verify-email-btn">
                        <?= ($user['enable'] == 1) ? 'Disable notification' : 'Enable notification' ?>
                    </button>
                </form>
                <button type="button" class="verify-email-btn" onclick="openModal()">
                    Edit profile
                </button>
                <form action="logout.php" method="post">
                    <button type="submit" class="verify-email-btn">
                        Logout
                    </button>
                </form>
            </div>
        </div>
        <!-- Other content goes here -->
    </div>

    <!-- Overlay -->
    <div id="overlay" class="overlay"></div>

    <!-- Edit Profile Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <h2>Edit Profile</h2>

            <form action="edit_profile.php" method="post">
                <input type="text" name="username" placeholder="New username">
                <input type="email" name="email" placeholder="New email">
                <input type="text" name="password" placeholder="New password">

                <button type="submit">Save</button>
                <button type="button" onclick="closeModal()">Cancel</button>
            </form>
        </div>
    </div>
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
                    <button type="submit" class="like-btn">
                        ❤️ <span class="like-count"><?= $img['like_count'] ?? 0 ?></span>
                    </button>
                </form>

                <!-- Comment Button (toggles the comment section) -->
                <button class="comment-btn" onclick="toggleCommentsSection(<?= $img['id'] ?>)">
                    💬 <?= $img['comment_count'] ?? 0 ?>
                </button>
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


<!-- prevent XSS attacks -->

<script src="../public/assets/js/script.js"></script> 


</body>



</html>

